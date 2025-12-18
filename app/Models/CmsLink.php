<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class CmsLink extends Model
{
    use Translatable; // Only if you need translated names

    protected $table = 'cms_links';
    
    // If using translations:
    // public $translatedAttributes = ['name'];
    // public $translationModel = CmsLinkTranslation::class;
    
    protected $fillable = [
        'linkable_type',
        'linkable_id',
        'name',
        'link',
        'icon',
        'target',
        'type',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Polymorphic relationship - can belong to Page, Section, or Item
     */
    public function linkable()
    {
        return $this->morphTo();
    }

    /**
     * Get icon HTML if icon is set
     */
    public function getIconHtmlAttribute()
    {
        if (!$this->icon) {
            return null;
        }

        // If icon is a class (e.g., FontAwesome)
        if (strpos($this->icon, 'fa-') === 0 || strpos($this->icon, 'icon-') === 0) {
            return '<i class="' . $this->icon . '"></i>';
        }

        // If icon is a full class string
        if (strpos($this->icon, ' ') !== false) {
            return '<i class="' . $this->icon . '"></i>';
        }

        // If icon is an image path
        if (filter_var($this->icon, FILTER_VALIDATE_URL) || strpos($this->icon, '/') === 0) {
            return '<img src="' . $this->icon . '" alt="' . $this->name . '">';
        }

        // Default: treat as class
        return '<i class="' . $this->icon . '"></i>';
    }

    /**
     * Scope to filter by linkable type
     */
    public function scopeForType($query, $type)
    {
        return $query->where('linkable_type', $type);
    }

    /**
     * Scope to filter by link type (social, contact, etc.)
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}

