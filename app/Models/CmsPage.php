<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPage extends Model
{
    use Translatable, SoftDeletes;

    protected $table = 'cms_pages';
    
    public $translatedAttributes = ['title', 'meta_description', 'meta_keywords'];
    public $translationModel = CmsPageTranslation::class;
    
    protected $fillable = [
        'slug',
        'name',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Relationships
    public function sections()
    {
        return $this->hasMany(CmsSection::class, 'page_id')->orderBy('order');
    }

    public function activeSections()
    {
        return $this->hasMany(CmsSection::class, 'page_id')
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

