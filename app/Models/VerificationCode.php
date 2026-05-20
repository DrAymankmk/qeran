<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Services\External\BaileysGateway;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VerificationCode extends Model
{
    protected $fillable = [
        'user_id',
        'objective',
        'code',
        'information_type',
        'information',
        'used',
        'expired_at',
    ];

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $information
     * @param  mixed  $code
     * @param  mixed  $objective
     */
    public function scopeCheckCode($query, $information, $code, $objective): Builder
    {
        return $query
            ->where('information', $information)
            ->where('code', trim((string) $code))
            ->where('objective', (int) $objective)
            ->where('used', Constant::VERIFICATION_USED['Not used'])
            ->where('expired_at', '>', now());
    }

    /**
     * Find an active (unused, unexpired) code, trying common phone formats.
     */
    public static function findActive(
        string $phone,
        ?string $countryCode,
        string $code,
        int $objective
    ): ?self {
        $code = trim($code);
        $objective = (int) $objective;

        foreach (self::phoneVariants($phone, $countryCode) as $variant) {
            $record = self::query()
                ->checkCode($variant, $code, $objective)
                ->first();

            if ($record) {
                return $record;
            }
        }

        return null;
    }

    /**
     * Why verification failed (for logging and API messages).
     */
    public static function failureReason(
        string $phone,
        ?string $countryCode,
        string $code,
        int $objective
    ): string {
        $code = trim($code);
        $objective = (int) $objective;
        $variants = self::phoneVariants($phone, $countryCode);

        $latest = self::query()
            ->whereIn('information', $variants)
            ->where('objective', $objective)
            ->orderByDesc('id')
            ->first();

        if (! $latest) {
            $anyObjective = self::query()
                ->whereIn('information', $variants)
                ->orderByDesc('id')
                ->first();

            if ($anyObjective && (int) $anyObjective->objective !== $objective) {
                return 'wrong_type';
            }

            return 'no_record';
        }

        if (trim((string) $latest->code) !== $code) {
            return 'wrong_code';
        }

        if ((int) $latest->used === (int) Constant::VERIFICATION_USED['Used']) {
            return 'already_used';
        }

        if ($latest->expired_at && $latest->expired_at <= now()) {
            return 'expired';
        }

        if ((int) $latest->objective !== $objective) {
            return 'wrong_type';
        }

        return 'not_found';
    }

    /**
     * @return list<string>
     */
    public static function phoneVariants(string $phone, ?string $countryCode): array
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $cc = preg_replace('/\D+/', '', (string) $countryCode);
        $normalized = BaileysGateway::normalizeUserPhone($cc, $phone);

        $local = $digits;
        if ($cc !== '' && str_starts_with($digits, $cc)) {
            $local = substr($digits, strlen($cc));
        }

        $variants = array_filter(array_unique([
            $phone,
            $digits,
            $normalized,
            $local,
            $cc !== '' ? $cc.$local : null,
        ]));

        return array_values($variants);
    }

    public static function logVerificationFailure(
        string $reason,
        string $phone,
        ?string $countryCode,
        string $code,
        int $objective,
        ?int $userId = null
    ): void {
        Log::warning('OTP verify failed', [
            'reason' => $reason,
            'user_id' => $userId,
            'objective' => $objective,
            'phone_variants' => self::phoneVariants($phone, $countryCode),
            'code_length' => strlen(trim($code)),
        ]);
    }
}
