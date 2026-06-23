<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappSessionLog extends Model
{
    public const UPDATED_AT = null;

    public const LEVEL_INFO = 'info';

    public const LEVEL_SUCCESS = 'success';

    public const LEVEL_WARNING = 'warning';

    public const LEVEL_ERROR = 'error';

    public const EVENT_CONNECTED = 'connected';

    public const EVENT_DISCONNECTED = 'disconnected';

    public const EVENT_RECONNECTING = 'reconnecting';

    public const EVENT_PENDING_QR = 'pending_qr';

    public const EVENT_ADMIN_PREPARE_QR = 'admin_prepare_qr';

    public const EVENT_ADMIN_PREPARE_RECONNECT = 'admin_prepare_reconnect';

    public const EVENT_ADMIN_ALREADY_CONNECTED = 'admin_already_connected';

    public const EVENT_ADMIN_DISCONNECT = 'admin_disconnect';

    public const EVENT_ADMIN_DISCONNECT_FAILED = 'admin_disconnect_failed';

    public const EVENT_ADMIN_LOCK_CLEARED = 'admin_lock_cleared';

    public const EVENT_QR_GENERATED = 'qr_generated';

    public const EVENT_QR_FAILED = 'qr_failed';

    public const EVENT_GATEWAY_UNREACHABLE = 'gateway_unreachable';

    public const EVENT_AUTO_RECONNECT = 'auto_reconnect';

    public const EVENT_AUTO_RECONNECT_FAILED = 'auto_reconnect_failed';

    public const EVENT_OTP_SEND_SUCCESS = 'otp_send_success';

    public const EVENT_OTP_SEND_FAILED = 'otp_send_failed';

    protected $fillable = [
        'session_id',
        'event',
        'level',
        'message',
        'context',
        'admin_id',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function levelBadgeClass(): string
    {
        return match ($this->level) {
            self::LEVEL_SUCCESS => 'success',
            self::LEVEL_WARNING => 'warning',
            self::LEVEL_ERROR => 'danger',
            default => 'secondary',
        };
    }
}
