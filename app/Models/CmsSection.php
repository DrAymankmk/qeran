<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsSection extends Model
{
    use Translatable, SoftDeletes;

    protected $table = 'cms_sections';
    
    public $translatedAttributes = ['title', 'subtitle', 'description'];
    public $translationModel = CmsSectionTranslation::class;
    
    protected $fillable = [
        'page_id',
        'name',
        'type',
        'template',
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
    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }

    public function items()
    {
        return $this->hasMany(CmsItem::class, 'section_id')->orderBy('order');
    }

    public function activeItems()
    {
        return $this->hasMany(CmsItem::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('order');
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
}

