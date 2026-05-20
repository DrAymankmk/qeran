<?php

namespace App\Support;

use App\Services\External\BaileysGateway;

class PhoneNumber
{
    /**
     * All plausible stored/request forms (leading zero, with/without country code, etc.).
     *
     * @return list<string>
     */
    public static function variants(?string $countryCode, string $phone): array
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $cc = preg_replace('/\D+/', '', (string) $countryCode);

        $local = $digits;
        if ($cc !== '' && str_starts_with($local, $cc)) {
            $local = substr($local, strlen($cc));
        }

        $localNoZero = $local;
        if (str_starts_with($localNoZero, '0')) {
            $localNoZero = substr($localNoZero, 1);
        }

        $localWithZero = str_starts_with($local, '0') ? $local : '0'.$localNoZero;

        $normalized = BaileysGateway::normalizeUserPhone($cc, $phone);

        $variants = [
            $phone,
            $digits,
            $local,
            $localNoZero,
            $localWithZero,
            $normalized,
        ];

        if ($cc !== '') {
            $variants[] = $cc.$local;
            $variants[] = $cc.$localNoZero;
            $variants[] = $cc.$localWithZero;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    /**
     * Prefer the form stored on the user row (leading-zero vs not).
     */
    public static function informationForStorage(?string $countryCode, string $requestPhone, ?string $storedPhone = null): string
    {
        if ($storedPhone !== null && $storedPhone !== '') {
            return $storedPhone;
        }

        $variants = self::variants($countryCode, $requestPhone);
        $localNoZero = null;
        $localWithZero = null;

        foreach ($variants as $variant) {
            if (str_starts_with($variant, '0') && strlen($variant) > 1) {
                $localWithZero = $variant;

                continue;
            }
            if (! str_starts_with($variant, '0') && strlen($variant) >= 9 && strlen($variant) <= 11) {
                $localNoZero = $variant;
            }
        }

        return $localWithZero ?? $localNoZero ?? $requestPhone;
    }
}
