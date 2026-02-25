<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'category',
        'notification_type',
        'user_id',
        'target_id',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
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

    /**
     * Scope notifications to those with valid targets:
     * - type = 0 (no specific target required), or
     * - target_id is null, or
     * - target_id exists as an Invitation id.
     */
    public function scopeWithValidTarget(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('type', 0)
                ->orWhereNull('target_id')
                ->orWhereExists(function ($sub) {
                    $sub->selectRaw(1)
                        ->from('invitations')
                        ->whereColumn('invitations.id', 'notifications.target_id');
                });
        });
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']))->diffForHumans();
    }

    /**
     * Scope to order notifications by read status: Unread first, then read
     */
    public function scopeOrderByReadStatus($query)
    {
        return $query->orderByRaw('read_at IS NULL DESC, created_at DESC');
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Check if notification is read
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }
}
