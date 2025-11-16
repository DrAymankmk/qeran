<?php

namespace App\Models;

use App\Helpers\Constant;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Translatable;

    public $translatedAttributes = ['title', 'content'];

    protected $fillable = [
        'id',
        'key',
        'image',
    ];
    protected $with=['translations'];

    protected static function boot()
    {
        parent::boot();
        static::updating(function () {
            cache()->forget('settings');
        });
    }


    public function keys()
    {
       if ($this->key == Constant::SETTINGS_KEY['Terms']){
            return __('Terms conditions');
        }
    }
}
