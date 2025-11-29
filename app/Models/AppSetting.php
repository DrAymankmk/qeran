<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;
    protected $fillable=['key','value','type'];
    public $translatable = ['value'];

    public function scopeKey($query , $key)
    {
        $query->where('key',$key);
    }


}
