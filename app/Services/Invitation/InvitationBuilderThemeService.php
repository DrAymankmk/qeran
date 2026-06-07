<?php

namespace App\Services\Invitation;

use App\Models\InvitationBuilderTheme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvitationBuilderThemeService
{
    public function store(array $data, UploadedFile $file): InvitationBuilderTheme
    {
        $defaults = config('invitation_builder.defaults', []);
        $slug = $this->generateUniqueSlug($data['name_ar'] ?? 'theme');
        $extension = strtolower($file->getClientOriginalExtension());
        $mediaPath = 'invitation-builder/themes/'.$slug.'.'.$extension;

        Storage::disk('public')->putFileAs(
            'invitation-builder/themes',
            $file,
            $slug.'.'.$extension
        );

        return InvitationBuilderTheme::query()->create([
            'slug' => $slug,
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'category' => $data['category'] ?? 'opening',
            'media_type' => $data['media_type'],
            'media_path' => $mediaPath,
            'preview_color' => $this->normalizeHex($data['preview_color'] ?? $data['background_color'] ?? null)
                ?? ($defaults['background_color'] ?? '#1a1520'),
            'primary_color' => $this->normalizeHex($data['primary_color'] ?? null)
                ?? ($defaults['primary_color'] ?? '#c9a962'),
            'secondary_color' => $this->normalizeHex($data['secondary_color'] ?? null)
                ?? ($defaults['secondary_color'] ?? '#e8b4b8'),
            'background_color' => $this->normalizeHex($data['background_color'] ?? null)
                ?? ($defaults['background_color'] ?? '#1a1520'),
            'text_color' => $this->normalizeHex($data['text_color'] ?? null)
                ?? ($defaults['text_color'] ?? '#faf6f0'),
            'renderer' => 'builder-wedding',
            'is_active' => true,
            'created_by' => Auth::guard('admin')->id(),
        ]);
    }

    public function delete(InvitationBuilderTheme $theme): void
    {
        if (Storage::disk('public')->exists($theme->media_path)) {
            Storage::disk('public')->delete($theme->media_path);
        }

        $theme->delete();
    }

    protected function generateUniqueSlug(string $name): string
    {
        $base = 'custom-'.Str::slug($name);
        $base = $base !== '' ? Str::limit($base, 60, '') : 'custom-theme';
        $slug = $base;
        $suffix = 1;

        while (InvitationBuilderTheme::query()->where('slug', $slug)->exists()
            || config('invitation_builder.animated_themes.'.$slug)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    protected function normalizeHex(?string $color): ?string
    {
        $color = trim((string) ($color ?? ''));

        if ($color === '') {
            return null;
        }

        if ($color[0] !== '#') {
            $color = '#'.$color;
        }

        return strtolower($color);
    }
}
