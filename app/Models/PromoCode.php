<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'valid_date',
        'expire_date',
        'discount_percentage',
        'package_id',
        'is_active',
        'usage_limit',
        'used_count'
    ];

    protected $casts = [
        'valid_date' => 'date',
        'expire_date' => 'date',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Get the package that this promo code belongs to (if any)
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Check if promo code is valid (active, within date range, and not exceeded usage limit)
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($now->lt($this->valid_date) || $now->gt($this->expire_date)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Scope to get only active promo codes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get promo codes valid for a specific package or all packages
     */
    public function scopeForPackage($query, $packageId = null)
    {
        return $query->where(function($q) use ($packageId) {
            $q->whereNull('package_id') // All packages
              ->orWhere('package_id', $packageId); // Specific package
        });
    }
}
