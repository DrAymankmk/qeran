<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name', 'job', 'message'];
    public $translationModel = TestimonialTranslation::class;

    protected $fillable = [
        'rating',
        'order',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function image()
    {
        $this->load('hubFiles');
        return $this->hubFiles?->get_path();
    }

    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}









