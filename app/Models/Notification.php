<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use Translatable;

    /**
     * Translated attributes.
     *
     * @var array
     */
    public $translatedAttributes = ['title', 'description'];
    protected $table = 'notifications';
    /**
     * Fillable fields.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'user_id',
        'target_id'
    ];
    protected $hidden = ['translations', 'updated_at', 'user_id'];

    public function image()
    {
        $this->load('hubFiles');

        return $this->hubFiles?->get_path();
    }

    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']))->diffForHumans();


    }


}
