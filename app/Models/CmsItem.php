<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CmsItem extends Model implements HasMedia
{
    use Translatable, SoftDeletes, InteractsWithMedia;

    protected $table = 'cms_items';
    
    public $translatedAttributes = ['title', 'sub_title', 'content', 'icon'];
    public $translationModel = CmsItemTranslation::class;
    
    protected $fillable = [
        'section_id',
        'type',
        'settings',
        'order',
        'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Relationships
    public function section()
    {
        return $this->belongsTo(CmsSection::class, 'section_id');
    }

    public function links()
    {
        return $this->morphMany(CmsLink::class, 'linkable')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function allLinks()
    {
        return $this->morphMany(CmsLink::class, 'linkable')->orderBy('order');
    }

    /**
     * Register media collections for this model
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'])
            ->singleFile(false); // Allow multiple files
    }

    /**
     * Register media conversions (thumbnails, etc.)
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('images');

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('images');
    }

    /**
     * Get all images ordered by custom property 'order'
     */
    public function getImagesAttribute()
    {
        return $this->getMedia('images')->sortBy(function ($media) {
            return $media->getCustomProperty('order', 0);
        });
    }
}

