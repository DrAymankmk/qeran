<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MediaDirectUploadController extends Controller
{
    /** Video extensions accepted for design media (same as DesignRequest). */
    private const ALLOWED_VIDEO_EXTENSIONS = ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'm4v'];

    /** Hub "folder" keys that may use direct-to-S3 upload. */
    private const ALLOWED_BUCKETS = [
        Constant::DESIGN_IMAGE_FOLDER_NAME,
    ];

    /**
     * Issue a short-lived presigned PUT URL so the browser can upload the file to Wasabi/S3
     * without sending the bytes through PHP.
     */
    public function presign(Request $request): JsonResponse
    {
        if (! mediaDiskSupportsDirectUpload()) {
            return response()->json([
                'message' => 'Direct upload is not available for the configured media disk.',
            ], 422);
        }

        $maxBytes = Constant::DESIGN_MEDIA_MAX_UPLOAD_KB * 1024;

        $validated = $request->validate([
            'bucket_name' => ['required', 'string', Rule::in(self::ALLOWED_BUCKETS)],
            'original_filename' => ['required', 'string', 'max:255'],
            'content_type' => ['required', 'string', 'regex:/^video\//'],
            'content_length' => ['required', 'integer', 'min:1', 'max:'.$maxBytes],
        ]);

        $ext = strtolower(pathinfo($validated['original_filename'], PATHINFO_EXTENSION));
        if ($ext === '' || ! in_array($ext, self::ALLOWED_VIDEO_EXTENSIONS, true)) {
            return response()->json(['message' => 'Unsupported video file type.'], 422);
        }

        $diskName = mediaDisk();
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($diskName);

        $filename = time().substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5).'.'.$ext;
        $relativeKey = $validated['bucket_name'].'/'.$filename;

        $token = (string) Str::uuid();

        $signed = $disk->temporaryUploadUrl($relativeKey, now()->addMinutes(20), [
            'ContentType' => $validated['content_type'],
        ]);

        $headersOut = [];
        foreach ($signed['headers'] ?? [] as $name => $values) {
            $headersOut[$name] = is_array($values) ? (string) reset($values) : (string) $values;
        }

        Cache::put(
            'media_direct_upload:'.$token,
            [
                'disk' => $diskName,
                'bucket_name' => $validated['bucket_name'],
                'relative_key' => $relativeKey,
                'filename' => $filename,
                'expected_size' => (int) $validated['content_length'],
                'original_name' => $validated['original_filename'],
                'extension' => $ext,
                'mime' => $validated['content_type'],
            ],
            now()->addMinutes(25)
        );

        return response()->json([
            'upload_token' => $token,
            'url' => $signed['url'],
            'headers' => $headersOut,
        ]);
    }
}
