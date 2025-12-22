<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsLink extends Model
{
    protected $table = 'cms_links';
    
    protected $fillable = [
        'linkable_type',
        'linkable_id',
        'name',
        'link',
        'route_name',
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

    /**
     * Get the actual URL for the link
     * If route_name is set, generate URL from route, otherwise use link
     */
    public function getUrlAttribute()
    {
        if ($this->route_name) {
            try {
                return route($this->route_name);
            } catch (\Exception $e) {
                // If route doesn't exist, return the link or #
                return $this->link ?? '#';
            }
        }
        return $this->link ?? '#';
    }
}

