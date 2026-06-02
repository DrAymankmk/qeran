<?php

namespace App\Support;

class PhoneNumber
{
    /**
     * E.164 digits for WhatsApp pairing (no + sign).
     * Must match the SIM / WhatsApp account on the phone exactly.
     */
    public static function e164ForWhatsAppPairing(?string $countryCode, ?string $phone, ?string $override = null): string
    {
        $cc = preg_replace('/\D+/', '', (string) $countryCode);
        $raw = preg_replace('/\D+/', '', (string) ($override !== null && $override !== '' ? $override : $phone));

        if ($raw === '') {
            return '';
        }

        if (str_starts_with($raw, '00')) {
            $raw = substr($raw, 2);
        }

        if ($cc !== '' && str_starts_with($raw, $cc)) {
            $national = substr($raw, strlen($cc));
        } else {
            $national = $raw;
        }

        // Egyptian local 010xxxxxxxx → 10xxxxxxxx (one leading 0 only)
        while (str_starts_with($national, '0') && strlen($national) > 1) {
            $national = substr($national, 1);
        }

        if ($cc === '') {
            return $national;
        }

        return $cc.$national;
    }

    public static function isValidWhatsAppPairingNumber(string $e164, ?string $countryCode): bool
    {
        $cc = preg_replace('/\D+/', '', (string) $countryCode);

        if ($cc === '20') {
            return (bool) preg_match('/^20(10|11|12|15)\d{8}$/', $e164);
        }

        if ($cc === '966') {
            return (bool) preg_match('/^9665\d{8}$/', $e164);
        }

        return strlen($e164) >= 10 && strlen($e164) <= 15;
    }

    /**
     * Local format with leading 0 (e.g. 201090537394 → 01090537394).
     */
    public static function localWithLeadingZero(string $e164, ?string $countryCode): string
    {
        $cc = preg_replace('/\D+/', '', (string) $countryCode);
        if ($cc !== '' && str_starts_with($e164, $cc)) {
            return '0'.substr($e164, strlen($cc));
        }

        return $e164;
    }

    /**
     * Digits to type in WhatsApp "link with phone number" (prefer E.164 without +).
     */
    public static function whatsAppPhoneEntryOptions(string $e164, ?string $countryCode): array
    {
        $local = self::localWithLeadingZero($e164, $countryCode);

        return array_values(array_unique([$e164, $local]));
    }

    /**
     * Human-readable number the user must enter in WhatsApp when asked for phone.
     */
    public static function formatForWhatsAppDisplay(string $e164): string
    {
        if (preg_match('/^20(10|11|12|15)(\d{4})(\d{4})$/', $e164, $m)) {
            return '+20 '.$m[1].' '.$m[2].' '.$m[3];
        }

        if (preg_match('/^9665(\d{2})(\d{3})(\d{4})$/', $e164, $m)) {
            return '+966 5'.$m[1].' '.$m[2].' '.$m[3];
        }

        return '+'.$e164;
    }

    /**
     * All plausible stored/request forms (leading zero, with/without country code, etc.).
     *
     * @return list<string>
     */
    public static function variants(?string $countryCode, string $phone): array
    {
        $raw = trim($phone);
        $e164 = self::e164ForWhatsAppPairing($countryCode, $raw);
        $digits = preg_replace('/\D+/', '', $raw);
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

        $variants = [
            $raw,
            $digits,
            $local,
            $localNoZero,
            $localWithZero,
            $e164,
            $raw !== '' ? '+'.$digits : null,
            $e164 !== '' ? '+'.$e164 : null,
        ];

        if ($cc !== '') {
            $variants[] = $cc.$local;
            $variants[] = $cc.$localNoZero;
            $variants[] = '+'.$cc.$local;
            $variants[] = '+'.$cc.$localNoZero;
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
