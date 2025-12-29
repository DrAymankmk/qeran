<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\HubFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class MediaController extends Controller
{
    /**
     * Display a listing of all media files.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get HubFile media
        $hubFileQuery = HubFile::query();
        
        // Filter by file type
        if ($request->has('file_type') && $request->file_type != '') {
            $hubFileQuery->where('file_type', $request->file_type);
        }
        
        // Filter by bucket name (folder)
        if ($request->has('bucket_name') && $request->bucket_name != '') {
            $hubFileQuery->where('bucket_name', $request->bucket_name);
        }
        
        // Search by original name
        if ($request->has('search') && $request->search != '') {
            $hubFileQuery->where('original_name', 'like', '%' . $request->search . '%');
        }
        
        $hubFiles = $hubFileQuery->orderBy('created_at', 'desc')->get();
        
        // Get Spatie Media Library media
        $spatieQuery = SpatieMedia::query();
        
        // Filter by collection name (similar to bucket_name)
        if ($request->has('bucket_name') && $request->bucket_name != '') {
            $spatieQuery->where('collection_name', $request->bucket_name);
        }
        
        // Filter by mime type (similar to file_type)
        if ($request->has('file_type') && $request->file_type != '') {
            if ($request->file_type == '1') { // Image
                $spatieQuery->where('mime_type', 'like', 'image/%');
            } elseif ($request->file_type == '2') { // Video
                $spatieQuery->where('mime_type', 'like', 'video/%');
            } elseif ($request->file_type == '3') { // Audio
                $spatieQuery->where('mime_type', 'like', 'audio/%');
            }
        }
        
        // Search by name or file_name
        if ($request->has('search') && $request->search != '') {
            $spatieQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('file_name', 'like', '%' . $request->search . '%');
            });
        }
        
        $spatieMedia = $spatieQuery->orderBy('created_at', 'desc')->get();
        
        // Merge and sort by created_at
        $allMedia = collect($hubFiles)->map(function($item) {
            return (object)[
                'id' => $item->id,
                'type' => 'hubfile',
                'model' => $item,
                'created_at' => $item->created_at,
            ];
        })->merge(
            collect($spatieMedia)->map(function($item) {
                return (object)[
                    'id' => $item->id,
                    'type' => 'spatie',
                    'model' => $item,
                    'created_at' => $item->created_at,
                ];
            })
        )->sortByDesc('created_at')->values();
        
        // Get unique bucket names for filter (from both sources)
        $hubFileBuckets = HubFile::distinct()->pluck('bucket_name')->toArray();
        $spatieCollections = SpatieMedia::distinct()->pluck('collection_name')->toArray();
        $bucketNames = collect(array_merge($hubFileBuckets, $spatieCollections))->unique()->sort()->values();
        
        return view('pages.media.index', compact('allMedia', 'bucketNames'));
    }

    /**
     * Show the form for creating a new media file.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.media.create');
    }

    /**
     * Store a newly uploaded media file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'bucket_name' => 'required|string|max:255',
            'file_type' => 'nullable|integer|in:1,2,3,4',
            'file_key' => 'nullable|integer|in:1,2,3',
            'file_name' => 'nullable|string|max:255',
        ]);
        
        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        
        // Determine file type if not provided
        $fileType = $request->file_type;
        if (!$fileType) {
            if (str_starts_with($mimeType, 'image/')) {
                $fileType = Constant::FILE_TYPE['Image'];
            } elseif (str_starts_with($mimeType, 'video/')) {
                $fileType = Constant::FILE_TYPE['Video'];
            } elseif (str_starts_with($mimeType, 'audio/')) {
                $fileType = Constant::FILE_TYPE['Audio'];
            } else {
                $fileType = Constant::FILE_TYPE['Image'];
            }
        }
        
        $filename = time() . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . '.' . $file->extension();
        
        // Store file based on type
        if ($fileType == Constant::FILE_TYPE['Image']) {
            // Use existing storeImage helper logic
            $image = \Intervention\Image\Facades\Image::make($file);
            
            // Store original
            $image->resize(1024, 1024, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('webp');
            Storage::disk('public')->put($request->bucket_name . '/' . $filename, $image);
            
            // Store medium
            $imageMedium = \Intervention\Image\Facades\Image::make($file);
            $imageMedium->resize(256, 170, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90);
            Storage::disk('public')->put($request->bucket_name . '/medium/' . $filename, $imageMedium);
            
            // Store thumbnail
            $imageThumb = \Intervention\Image\Facades\Image::make($file);
            $imageThumb->resize(170, 130, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 50);
            Storage::disk('public')->put($request->bucket_name . '/thumbnail/' . $filename, $imageThumb);
        } else {
            // For video, audio, etc., store directly
            Storage::putFileAs('public/' . $request->bucket_name, $file, $filename);
        }
        
        // Use custom file name if provided, otherwise use original name
        $originalName = $request->file_name ?: $file->getClientOriginalName();
        
        // Create HubFile record
        $hubFile = HubFile::create([
            'bucket_name' => $request->bucket_name,
            'original_name' => $originalName,
            'path' => $filename,
            'extension' => $file->extension(),
            'size' => $file->getSize(),
            'getMimeType' => $mimeType,
            'file_type' => $fileType,
            'file_key' => $request->file_key ?? Constant::FILE_KEY['Not Main'],
            'morphable_type' => 'App\Models\Media', // Standalone media
            'morphable_id' => 0, // No parent model
        ]);
        
        return redirect()->route('media.index')->with('success', 'Media uploaded successfully');
    }

    /**
     * Show the form for editing the specified media file.
     *
     * @param  \App\Models\HubFile  $medium
     * @return \Illuminate\Http\Response
     */
    public function edit(HubFile $medium)
    {
        return view('pages.media.edit', compact('medium'));
    }

    /**
     * Update the specified media file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HubFile  $medium
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HubFile $medium)
    {
        $request->validate([
            'file' => 'nullable|file|max:10240', // 10MB max
            'bucket_name' => 'required|string|max:255',
            'file_key' => 'nullable|integer|in:1,2,3',
            'original_name' => 'nullable|string|max:255',
            'file_name' => 'nullable|string|max:255',
        ]);
        
        // Use file_name if provided, otherwise use original_name, otherwise keep existing
        $originalName = $request->file_name 
            ?: ($request->original_name ?? $medium->original_name);
        
        $updateData = [
            'bucket_name' => $request->bucket_name,
            'file_key' => $request->file_key ?? $medium->file_key,
            'original_name' => $originalName,
        ];
        
        // Handle file replacement if a new file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            
            // Determine file type
            $fileType = $medium->file_type; // Keep same type by default
            if (str_starts_with($mimeType, 'image/')) {
                $fileType = Constant::FILE_TYPE['Image'];
            } elseif (str_starts_with($mimeType, 'video/')) {
                $fileType = Constant::FILE_TYPE['Video'];
            } elseif (str_starts_with($mimeType, 'audio/')) {
                $fileType = Constant::FILE_TYPE['Audio'];
            }
            
            // Delete old files
            $oldFilePath = $medium->bucket_name . '/' . $medium->path;
            $oldMediumPath = $medium->bucket_name . '/medium/' . $medium->path;
            $oldThumbnailPath = $medium->bucket_name . '/thumbnail/' . $medium->path;
            
            Storage::disk('public')->delete($oldFilePath);
            Storage::disk('public')->delete($oldMediumPath);
            Storage::disk('public')->delete($oldThumbnailPath);
            
            // Generate new filename (consistent with store method)
            $extension = $file->extension();
            $filename = time() . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . '.' . $extension;
            
            // Store new file based on type
            if ($fileType == Constant::FILE_TYPE['Image']) {
                // Use existing storeImage helper logic
                $image = \Intervention\Image\Facades\Image::make($file);
                
                // Store original (encode as webp but keep original extension in filename)
                $image->resize(1024, 1024, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode('webp');
                Storage::disk('public')->put($request->bucket_name . '/' . $filename, $image);
                
                // Store medium
                $imageMedium = \Intervention\Image\Facades\Image::make($file);
                $imageMedium->resize(256, 170, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('webp', 90);
                Storage::disk('public')->put($request->bucket_name . '/medium/' . $filename, $imageMedium);
                
                // Store thumbnail
                $imageThumb = \Intervention\Image\Facades\Image::make($file);
                $imageThumb->resize(170, 130, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('webp', 50);
                Storage::disk('public')->put($request->bucket_name . '/thumbnail/' . $filename, $imageThumb);
            } else {
                // For video, audio, etc., store directly
                Storage::putFileAs('public/' . $request->bucket_name, $file, $filename);
            }
            
            // Update file information
            $updateData['path'] = $filename;
            $updateData['extension'] = $extension;
            $updateData['size'] = $file->getSize();
            $updateData['getMimeType'] = $mimeType;
            $updateData['file_type'] = $fileType;
            // Only update original_name if file_name is not provided and original_name is not provided
            if (!$request->file_name && !$request->original_name) {
                $updateData['original_name'] = $file->getClientOriginalName();
            }
        }
        
        $medium->update($updateData);
        
        return redirect()->route('media.index')->with('success', 'Media updated successfully');
    }

    /**
     * Remove the specified media file.
     *
     * @param  \App\Models\HubFile  $medium
     * @return \Illuminate\Http\Response
     */
    public function destroy(HubFile $medium)
    {
        // Delete file from storage
        $filePath = $medium->bucket_name . '/' . $medium->path;
        $mediumPath = $medium->bucket_name . '/medium/' . $medium->path;
        $thumbnailPath = $medium->bucket_name . '/thumbnail/' . $medium->path;
        
        Storage::disk('public')->delete($filePath);
        Storage::disk('public')->delete($mediumPath);
        Storage::disk('public')->delete($thumbnailPath);
        
        // Delete database record
        $medium->delete();
        
        return redirect()->route('media.index')->with('success', 'Media deleted successfully');
    }

    /**
     * Proxy media files to avoid CORS issues
     * Fetches the file from external URL and serves it with proper CORS headers
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function proxy(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->get('url');
        
        \Log::info('Media proxy request', ['url' => $url]);
        
        try {
            // Handle OPTIONS request for CORS preflight
            if ($request->isMethod('OPTIONS')) {
                return response('', 200)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, OPTIONS, HEAD')
                    ->header('Access-Control-Allow-Headers', 'Range, Content-Type')
                    ->header('Access-Control-Max-Age', '86400');
            }

            // Use file_get_contents with stream context for simpler binary handling
            $contextOptions = [
                'http' => [
                    'method' => 'GET',
                    'timeout' => 60,
                    'follow_location' => true,
                    'ignore_errors' => false,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ];

            // Add Range header if present
            if ($request->hasHeader('Range')) {
                $contextOptions['http']['header'] = 'Range: ' . $request->header('Range');
            }

            // Stream the file directly without loading into memory
            // This is the simplest and most reliable approach
            $context = stream_context_create($contextOptions);
            $handle = @fopen($url, 'rb', false, $context);

            if ($handle === false) {
                $error = error_get_last();
                throw new \Exception('Failed to open file: ' . ($error['message'] ?? 'Unknown error'));
            }

            // Get response headers
            $responseHeaders = [];
            $httpCode = 200;
            
            if (isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (strpos($header, ':') !== false) {
                        list($key, $value) = explode(':', $header, 2);
                        $responseHeaders[strtolower(trim($key))] = trim($value);
                    } elseif (preg_match('/HTTP\/[\d.]+ (\d+)/', $header, $matches)) {
                        $httpCode = (int)$matches[1];
                    }
                }
            }

            // Detect Content-Type from URL
            $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
            $mimeTypes = [
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
                'ogg' => 'audio/ogg',
                'oga' => 'audio/ogg',
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ];
            $contentType = $responseHeaders['content-type'] ?? $mimeTypes[$extension] ?? 'application/octet-stream';
            $contentLength = $responseHeaders['content-length'] ?? null;
            $acceptRanges = $responseHeaders['accept-ranges'] ?? 'bytes';
            $contentRange = $responseHeaders['content-range'] ?? null;

            // Set headers
            header('Content-Type: ' . $contentType);
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS, HEAD');
            header('Access-Control-Allow-Headers: Range, Content-Type');
            header('Accept-Ranges: ' . $acceptRanges);
            header('Cache-Control: public, max-age=3600');
            
            if ($contentLength) {
                header('Content-Length: ' . $contentLength);
            }
            
            if ($contentRange) {
                header('Content-Range: ' . $contentRange);
            }
            
            http_response_code($httpCode);

            // Stream the file directly to output
            fpassthru($handle);
            fclose($handle);
            
            exit; // Important: exit after streaming
        } catch (\Exception $e) {
            \Log::error('Media proxy error', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Error loading media file: ' . $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }
}