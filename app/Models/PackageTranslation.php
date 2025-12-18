<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageTranslation extends Model
{
    public $timestamps = true;
    protected $table = 'package_translations';
    protected $fillable = ['package_id', 'locale', 'title', 'subtitle', 'content'];
}

