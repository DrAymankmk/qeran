<?php

namespace App\Models;

use App\Helpers\Constant;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name'];
    public $translationModel = DesignTranslation::class;

    protected $fillable = [
        'category_id',
        'code',
        'show_on',
    ];

    protected $casts = [
        'show_on' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        $this->load('hubFiles');
        return $this->hubFiles?->get_path();
    }

    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }
}
