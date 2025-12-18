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
        $query = HubFile::query();
        
        // Filter by file type
        if ($request->has('file_type') && $request->file_type != '') {
            $query->where('file_type', $request->file_type);
        }
        
        // Filter by bucket name (folder)
        if ($request->has('bucket_name') && $request->bucket_name != '') {
            $query->where('bucket_name', $request->bucket_name);
        }
        
        // Search by original name
        if ($request->has('search') && $request->search != '') {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }
        
        $media = $query->orderBy('created_at', 'desc')
        ->get();
        
        // Get unique bucket names for filter
        $bucketNames = HubFile::distinct()->pluck('bucket_name')->sort();
        
        return view('pages.media.index', compact('media', 'bucketNames'));
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
            Storage::put('public/' . $request->bucket_name . '/' . $filename, $image);
            
            // Store medium
            $imageMedium = \Intervention\Image\Facades\Image::make($file);
            $imageMedium->resize(256, 170, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90);
            Storage::put('public/' . $request->bucket_name . '/medium/' . $filename, $imageMedium);
            
            // Store thumbnail
            $imageThumb = \Intervention\Image\Facades\Image::make($file);
            $imageThumb->resize(170, 130, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 50);
            Storage::put('public/' . $request->bucket_name . '/thumbnail/' . $filename, $imageThumb);
        } else {
            // For video, audio, etc., store directly
            Storage::putFileAs('public/' . $request->bucket_name, $file, $filename);
        }
        
        // Create HubFile record
        $hubFile = HubFile::create([
            'bucket_name' => $request->bucket_name,
            'original_name' => $file->getClientOriginalName(),
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
        ]);
        
        $updateData = [
            'bucket_name' => $request->bucket_name,
            'file_key' => $request->file_key ?? $medium->file_key,
            'original_name' => $request->original_name ?? $medium->original_name,
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
                Storage::put('public/' . $request->bucket_name . '/' . $filename, $image);
                
                // Store medium
                $imageMedium = \Intervention\Image\Facades\Image::make($file);
                $imageMedium->resize(256, 170, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('webp', 90);
                Storage::put('public/' . $request->bucket_name . '/medium/' . $filename, $imageMedium);
                
                // Store thumbnail
                $imageThumb = \Intervention\Image\Facades\Image::make($file);
                $imageThumb->resize(170, 130, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('webp', 50);
                Storage::put('public/' . $request->bucket_name . '/thumbnail/' . $filename, $imageThumb);
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
            if (!$request->original_name) {
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
}