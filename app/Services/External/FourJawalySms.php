<?php

namespace App\Services\External;

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

    /**
     * @return array{ok: bool, data: ?array, error: ?string, status: int}
     */
    public static function send(string $to, string $text): array
    {
        if (! self::isConfigured()) {
            return [
                'ok' => false,
                'data' => null,
                'error' => 'not_configured',
                'status' => 0,
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
                            'numbers' => [$to],
                            'sender' => $sender,
                        ],
                    ],
                ]);

            $json = $response->json();
            $ok = $response->successful() && self::responseIndicatesSuccess($json);

            if (! $ok) {
                $error = self::friendlyError(self::extractErrorMessage($json) ?? 'send_failed');

                Log::warning('4jawaly SMS: send failed', [
                    'http_status' => $response->status(),
                    'error' => $error,
                    'to_masked' => self::maskDigits($to),
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

    public static function friendlyError(?string $message): string
    {
        $message = trim((string) $message);

        if ($message === '') {
            return __('messages.otp_sms_send_failed');
        }

        $normalized = mb_strtolower($message);

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
