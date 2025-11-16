<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTranslation extends Model
{
    protected $table = 'notification_translations';

    /**
     * Primary key.
     *
     * @var string
     */
    protected $primaryKey = 'notifications_trans_id';

    /**
     * Fillable fields.
     *
     * @var array
     */
    protected $fillable = ['title','description'];

    /**
     * Timestamps.
     *
     * @var boolean
     */
    public $timestamps = false;

}
