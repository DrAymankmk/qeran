<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $fillable = [
        'user_id',
        'objective',
        'code',
        'information_type',
        'information',
        'used',
        'expired_at'
    ];

    /**
     * Scope a query to only verify email of a given code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $phone
     * @param  mixed  $code
     * @param  mixed  $objective [ verify, reset ]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCheckCode($query, $information, $code, $objective)
    {
        $query
            ->where('information', $information)
            ->where('code', $code)
            ->where('objective', $objective)
            ->where('used', Constant::VERIFICATION_USED['Not used'])
            ->where('expired_at', '>', now());
    }
}
