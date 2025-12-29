<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimonialTranslation extends Model
{
    public $timestamps = true;
    protected $table = 'testimonial_translations';
    protected $fillable = ['testimonial_id', 'locale', 'name', 'job', 'message'];
}



















