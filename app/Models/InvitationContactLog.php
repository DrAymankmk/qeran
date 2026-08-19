<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvitationContactLog extends Model
{
	use SoftDeletes;
    protected $fillable = [
        'invitation_id',
        'invited_by',
        'user_id',
        'contact_name',
        'country_code',
        'phone',
        'invitation_count',
        'guest_codes',
        'send_status',
        'seen',
        'acceptance_status',
        'error_message',
        'reference_id',
        'whatsapp_message_id',
        'sent_at',
        'delivered_at',
        'read_at',
        'reminder_sent_at',
        'reminder_error_message',
    ];

    protected $casts = [
        'invitation_count' => 'integer',
        'guest_codes' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function guestEntries(): array
    {
        return $this->guest_codes['guests'] ?? [];
    }

    public function scannedGuestsCount(): int
    {
        return collect($this->guestEntries())
            ->where('is_scanned', true)
            ->count();
    }

    public function allGuestsScanned(): bool
    {
        $guests = $this->guestEntries();

        return count($guests) > 0
            && collect($guests)->every(fn (array $guest) => ($guest['is_scanned'] ?? false) === true);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSent(): bool
    {
        return (int) $this->send_status === Constant::INVITATION_CONTACT_SEND_STATUS['sent'];
    }

    public function isFailed(): bool
    {
        return (int) $this->send_status === Constant::INVITATION_CONTACT_SEND_STATUS['failed'];
    }
}
