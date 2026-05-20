<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'phone',
        'connected_at',
        'last_seen_at',
    ];

    protected $casts = [
        'connected_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }
}
