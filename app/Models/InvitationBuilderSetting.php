<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationBuilderSetting extends Model
{
    protected $fillable = [
        'invitation_id',
        'event_category',
        'theme_template',
        'theme_mode',
        'opening_type',
        'settings',
        'published_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'published_at' => 'datetime',
        'theme_template' => 'integer',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }
}
