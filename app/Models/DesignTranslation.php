<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignTranslation extends Model
{
    public $timestamps = true;
    protected $table = 'design_translations';
    protected $fillable = ['design_id', 'locale', 'name'];
}
