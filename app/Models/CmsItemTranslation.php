<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsItemTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'sub_title', 'content', 'icon'];
}

