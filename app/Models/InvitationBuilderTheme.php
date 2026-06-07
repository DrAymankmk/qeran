<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InvitationBuilderTheme extends Model
{
    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'category',
        'media_type',
        'media_path',
        'preview_color',
        'primary_color',
        'secondary_color',
        'background_color',
        'text_color',
        'renderer',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function mediaUrl(): string
    {
        if (Storage::disk('public')->exists($this->media_path)) {
            return route('invitation-theme-media', $this->slug);
        }

        return Storage::disk('public')->url($this->media_path);
    }

    /**
     * @return array<string, mixed>
     */
    public function toCatalogEntry(): array
    {
        $url = $this->mediaUrl();

        return [
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en ?? $this->name_ar,
            'category' => $this->category,
            'media_type' => $this->media_type,
            'opening_media_url' => $url,
            'opening_video_url' => $this->media_type === 'video' ? $url : '',
            'preview' => $this->preview_color,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'background_color' => $this->background_color,
            'text_color' => $this->text_color,
            'renderer' => $this->renderer,
            'is_custom' => true,
            'theme_id' => $this->id,
        ];
    }
}
