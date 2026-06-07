<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvitationBuilderThemeRequest;
use App\Models\InvitationBuilderTheme;
use App\Services\Invitation\InvitationBuilderThemeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvitationBuilderThemeController extends Controller
{
    public function __construct(
        protected InvitationBuilderThemeService $themes
    ) {}

    public function store(StoreInvitationBuilderThemeRequest $request): JsonResponse
    {
        $theme = $this->themes->store(
            $request->validated(),
            $request->file('media')
        );

        return response()->json([
            'ok' => true,
            'message' => __('admin.ib-theme-upload-success'),
            'theme' => array_merge(
                ['slug' => $theme->slug],
                $theme->toCatalogEntry()
            ),
        ]);
    }

    public function destroy(InvitationBuilderTheme $theme): JsonResponse
    {
        $this->themes->delete($theme);

        return response()->json([
            'ok' => true,
            'message' => __('admin.ib-theme-delete-success'),
        ]);
    }

    public function showMedia(InvitationBuilderTheme $theme): BinaryFileResponse
    {
        abort_unless($theme->is_active, 404);

        if (! Storage::disk('public')->exists($theme->media_path)) {
            abort(404);
        }

        $path = Storage::disk('public')->path($theme->media_path);

        return response()->file($path, [
            'Content-Type' => Storage::disk('public')->mimeType($theme->media_path) ?: 'application/octet-stream',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
