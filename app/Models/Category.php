<?php

namespace App\Models;

use App\Helpers\Constant;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['title', 'name', 'slug' , 'description'];

    protected $with = 'translations';

    protected $fillable = [
        'active',
        'parent_id',
        'is_wedding',
        'is_party',
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
    public function invitations(){
        return $this->hasMany(Invitation::class);
    }
    public function designs(){
        return $this->hasMany(Design::class);
    }
    public function scopeGetActiveCategories($query)
    {
        $query->where('active', Constant::CATEGORY_STATUS['Active'])->whereNull('parent_id');
    }


}
