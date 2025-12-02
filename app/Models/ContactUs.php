<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_type',
        'status',
        'conversation_status',
        'name',
        'email',
        'country_code',
        'phone',
        'subject',
        'message',
        'user_id',
    ];

    public function reason()
    {
        return $this->belongsTo(Setting::class, 'reason_id');
    }

    public function scopeSearchBetweenDates($query, $from, $to)
    {
        return $query->when($from && $to, fn ($query) => $query->whereBetween('created_at', [$from, $to]));
    }

    public function scopeLastMonth($query)
    {
        $query->whereMonth('created_at', now()->subMonth()->month);
    }

    /**
     * Scope to order conversations by priority:
     * 1. New (Not Yet Replied by Admin) - newest first
     * 2. Under Review
     * 3. Closed
     */
    public function scopeOrderByConversationPriority($query)
    {
        $newStatus = Constant::CONTACT_CONVERSATION_STATUS['New'];
        $notActiveStatus = Constant::STATUS['Not active'];
        $underReviewStatus = Constant::CONTACT_CONVERSATION_STATUS['Under Review'];
        $closedStatus = Constant::CONTACT_CONVERSATION_STATUS['Closed'];
        
        return $query->orderByRaw("
            CASE 
                WHEN conversation_status = {$newStatus} AND status = {$notActiveStatus} THEN 1
                WHEN conversation_status = {$underReviewStatus} THEN 2
                WHEN conversation_status = {$closedStatus} THEN 3
                ELSE 4
            END,
            created_at DESC
        ");
    }

    public function image()
    {
        $this->load('hubFiles');

        return $this->hubFiles ? $this->hubFiles->get_path() : 'website/images/blog/02.jpg';
    }
    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }

}
