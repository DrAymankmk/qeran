<?php

namespace App\Services\External;

use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FourJawalySms
{
    public static function isConfigured(): bool
    {
        return (bool) config('services.fourjawaly.api_key')
            && (bool) config('services.fourjawaly.api_secret')
            && (bool) config('services.fourjawaly.sender');
    }

    public static function formatRecipient(?string $countryCode, string $phone): string
    {
        return PhoneNumber::e164ForWhatsAppPairing($countryCode, $phone, $phone);
    }

    /**
     * Returns a user-facing error message, or null when the number looks valid.
     */
    public static function recipientValidationError(string $e164, ?string $countryCode): ?string
    {
        $cc = preg_replace('/\D+/', '', (string) $countryCode);

        if ($e164 === '' || strlen($e164) < 10 || strlen($e164) > 15) {
            return __('messages.otp_invalid_phone');
        }

        if ($cc === '966' && ! preg_match('/^9665\d{8}$/', $e164)) {
            return __('admin.otp-sms-error-invalid-saudi');
        }

        if ($cc === '20' && ! preg_match('/^20(10|11|12|15)\d{8}$/', $e164)) {
            return __('admin.otp-sms-error-invalid-egypt');
        }

        if ($cc !== '' && ! str_starts_with($e164, $cc)) {
            return __('admin.otp-sms-error-country-mismatch');
        }

        $allowed = self::allowedCountryCodes();
        if ($allowed !== [] && ! self::matchesAllowedCountry($e164, $allowed)) {
            return __('admin.otp-sms-error-country-not-allowed', [
                'countries' => implode(', ', $allowed),
            ]);
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public static function allowedCountryCodes(): array
    {
        $raw = trim((string) config('services.fourjawaly.allowed_country_codes', ''));

        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $code): string => preg_replace('/\D+/', '', trim($code)) ?: '',
            explode(',', $raw)
        )));
    }

    /**
     * @param  list<string>  $allowedCountryCodes
     */
    protected static function matchesAllowedCountry(string $e164, array $allowedCountryCodes): bool
    {
        foreach ($allowedCountryCodes as $code) {
            if ($code !== '' && str_starts_with($e164, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{ok: bool, data: ?array, error: ?string, status: int}
     */
    public static function send(string $to, string $text, ?string $countryCode = null): array
    {
        if (! self::isConfigured()) {
            return [
                'ok' => false,
                'data' => null,
                'error' => 'not_configured',
                'status' => 0,
            ];
        }

        $validationError = self::recipientValidationError($to, $countryCode);
        if ($validationError !== null) {
            return [
                'ok' => false,
                'data' => null,
                'error' => $validationError,
                'status' => 422,
            ];
        }

        $apiKey = (string) config('services.fourjawaly.api_key');
        $apiSecret = (string) config('services.fourjawaly.api_secret');
        $sender = (string) config('services.fourjawaly.sender');
        $url = (string) config('services.fourjawaly.api_url');

        try {
            $response = Http::withBasicAuth($apiKey, $apiSecret)
                ->acceptJson()
                ->asJson()
                ->timeout(20)
                ->post($url, [
                    'messages' => [
                        [
                            'text' => $text,
                            'numbers' => [(string) $to],
                            'sender' => $sender,
                        ],
                    ],
                ]);

            $json = $response->json();
            $ok = $response->successful() && self::responseIndicatesSuccess($json);

            if (! $ok) {
                $rawError = self::extractErrorMessage($json) ?? 'send_failed';
                $error = self::friendlyError($rawError, $to);

                Log::warning('4jawaly SMS: send failed', [
                    'http_status' => $response->status(),
                    'error' => $rawError,
                    'friendly_error' => $error,
                    'to_masked' => self::maskDigits($to),
                    'response' => is_array($json) ? $json : null,
                ]);

                return [
                    'ok' => false,
                    'data' => is_array($json) ? $json : null,
                    'error' => $error,
                    'status' => $response->status(),
                ];
            }

            return [
                'ok' => true,
                'data' => is_array($json) ? $json : null,
                'error' => null,
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('4jawaly SMS: request exception', [
                'error' => $e->getMessage(),
                'to_masked' => self::maskDigits($to),
            ]);

            return [
                'ok' => false,
                'data' => null,
                'error' => $e->getMessage(),
                'status' => 0,
            ];
        }
    }

    protected static function responseIndicatesSuccess(mixed $json): bool
    {
        if (! is_array($json)) {
            return false;
        }

        if (isset($json['code']) && is_numeric($json['code'])) {
            return (int) $json['code'] === 200;
        }

        if (isset($json['status']) && is_string($json['status'])) {
            $status = strtolower($json['status']);

            return in_array($status, ['success', 'ok', 'sent'], true);
        }

        if (isset($json['success'])) {
            return (bool) $json['success'];
        }

        return false;
    }

    public static function friendlyError(?string $message, ?string $recipient = null): string
    {
        $message = trim((string) $message);

        if ($message === '') {
            return __('messages.otp_sms_send_failed');
        }

        $normalized = mb_strtolower($message);

        if (str_contains($normalized, 'valid numbers') || str_contains($normalized, 'no valid')) {
            if ($recipient !== null && str_starts_with($recipient, '20')) {
                return __('admin.otp-sms-error-no-valid-numbers-egypt');
            }

            if ($recipient !== null && str_starts_with($recipient, '966')) {
                return __('admin.otp-sms-error-no-valid-numbers-saudi');
            }

            return __('admin.otp-sms-error-no-valid-numbers');
        }

        if (str_contains($normalized, 'باقات') || str_contains($normalized, 'packages')) {
            return __('admin.otp-sms-error-no-packages');
        }

        if (str_contains($normalized, 'مرسل') || str_contains($normalized, 'sender')) {
            return __('admin.otp-sms-error-sender');
        }

        if (str_contains($normalized, 'رصيد') || str_contains($normalized, 'balance')) {
            return __('admin.otp-sms-error-balance');
        }

        return $message;
    }

    protected static function extractErrorMessage(mixed $json): ?string
    {
        if (! is_array($json)) {
            return null;
        }

        foreach (['message', 'error', 'msg'] as $key) {
            if (! empty($json[$key]) && is_string($json[$key])) {
                return $json[$key];
            }
        }

        if (isset($json['messages']) && is_array($json['messages'])) {
            foreach ($json['messages'] as $message) {
                if (! is_array($message)) {
                    continue;
                }

                foreach (['err_text', 'error', 'message'] as $key) {
                    if (! empty($message[$key]) && is_string($message[$key])) {
                        return $message[$key];
                    }
                }
            }
        }

        if (isset($json['errors']) && is_array($json['errors'])) {
            $first = reset($json['errors']);

            if (is_string($first)) {
                return $first;
            }
        }

        return null;
    }

    protected static function maskDigits(string $digits): string
    {
        if (strlen($digits) <= 4) {
            return '****';
        }

        return str_repeat('*', strlen($digits) - 4).substr($digits, -4);
    }
}
