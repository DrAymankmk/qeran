<?php

namespace App\Services\External;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BaileysGateway
{
    public static function isConfigured(): bool
    {
        return (bool) config('services.baileys.gateway_url')
            && (bool) config('services.baileys.gateway_secret');
    }

    public static function systemSessionId(): string
    {
        return (string) config('services.baileys.system_session', 'system');
    }

    public static function sessionIdForUser(int $userId): string
    {
        return 'user_'.$userId;
    }

    public static function startSession(?string $sessionId = null): array
    {
        return self::request('post', '/sessions', [
            'sessionId' => $sessionId ?? self::systemSessionId(),
            'linkMethod' => 'qr',
        ]);
    }

    public static function normalizeUserPhone(?string $countryCode, ?string $phone, ?string $override = null): string
    {
        if ($override !== null && $override !== '') {
            return preg_replace('/\D+/', '', $override);
        }

        $cc = preg_replace('/\D+/', '', (string) $countryCode);
        $local = preg_replace('/\D+/', '', (string) $phone);

        if ($cc !== '' && str_starts_with($local, $cc)) {
            return $local;
        }

        return $cc.$local;
    }

    public static function startSessionWithPairing(string $sessionId, string $phone): array
    {
        return self::request('post', '/sessions', [
            'sessionId' => $sessionId,
            'phone' => preg_replace('/\D+/', '', $phone),
            'linkMethod' => 'pairing',
        ]);
    }

    public static function gatewaySupportsPairing(): bool
    {
        if (! self::isConfigured()) {
            return false;
        }

        try {
            $response = Http::timeout(10)->get(self::baseUrl().'/health');
            $json = $response->json();

            return ($json['features']['pairingCode'] ?? false) === true
                || version_compare((string) ($json['version'] ?? '0'), '1.2.1', '>=');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function getPairingCode(string $sessionId, string $phone): array
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (! self::isConfigured()) {
            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => 'Baileys gateway is not configured.',
            ];
        }

        try {
            $response = self::http()->get(
                self::baseUrl().'/sessions/'.rawurlencode($sessionId).'/pairing-code',
                ['phone' => $digits]
            );

            $json = $response->json();

            if ($response->successful()) {
                return [
                    'ok' => true,
                    'status' => $response->status(),
                    'data' => is_array($json) ? $json : [],
                    'error' => null,
                ];
            }

            $message = is_array($json)
                ? ($json['error'] ?? $response->body())
                : $response->body();

            if (is_string($message) && str_contains($message, 'Cannot GET') && str_contains($message, 'pairing-code')) {
                $message = 'Gateway is outdated — deploy whatsapp-gateway v1.2.1+ and restart PM2 (missing /pairing-code route).';
            }

            return [
                'ok' => false,
                'status' => $response->status(),
                'data' => is_array($json) ? $json : null,
                'error' => is_string($message) ? $message : json_encode($message),
            ];
        } catch (\Throwable $e) {
            Log::error('BaileysGateway getPairingCode failed', ['error' => $e->getMessage()]);

            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public static function getStatus(?string $sessionId = null): array
    {
        $id = $sessionId ?? self::systemSessionId();

        return self::request('get', "/sessions/{$id}/status");
    }

    public static function getQr(?string $sessionId = null): array
    {
        $id = $sessionId ?? self::systemSessionId();

        return self::request('get', "/sessions/{$id}/qr");
    }

    public static function deleteSession(?string $sessionId = null): array
    {
        $id = $sessionId ?? self::systemSessionId();

        return self::request('delete', "/sessions/{$id}");
    }

    public static function send(
        string $to,
        string $message,
        ?string $sessionId = null,
        string $referenceId = ''
    ): array {
        $payload = [
            'sessionId' => $sessionId ?? self::systemSessionId(),
            'to' => preg_replace('/\D+/', '', $to),
            'message' => $message,
        ];

        if ($referenceId !== '') {
            $payload['referenceId'] = $referenceId;
        }

        return self::request('post', '/send', $payload);
    }

    protected static function http(): PendingRequest
    {
        return Http::withToken((string) config('services.baileys.gateway_secret'))
            ->acceptJson()
            ->timeout(90);
    }

    protected static function baseUrl(): string
    {
        return rtrim((string) config('services.baileys.gateway_url'), '/');
    }

    /**
     * @param  array<string, mixed>  $body
     */
    protected static function request(string $method, string $path, array $body = []): array
    {
        if (! self::isConfigured()) {
            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => 'Baileys gateway is not configured. Set BAILEYS_GATEWAY_URL and BAILEYS_GATEWAY_SECRET in .env',
            ];
        }

        $url = self::baseUrl().$path;

        try {
            $response = match ($method) {
                'get' => self::http()->get($url),
                'post' => self::http()->post($url, $body),
                'delete' => self::http()->delete($url),
                default => throw new \InvalidArgumentException("Unsupported method: {$method}"),
            };

            $json = $response->json();

            if ($response->successful()) {
                return [
                    'ok' => true,
                    'status' => $response->status(),
                    'data' => is_array($json) ? $json : [],
                    'error' => null,
                ];
            }

            $message = is_array($json)
                ? ($json['error'] ?? $json['message'] ?? $response->body())
                : $response->body();

            return [
                'ok' => false,
                'status' => $response->status(),
                'data' => is_array($json) ? $json : null,
                'error' => is_string($message) ? $message : json_encode($message),
            ];
        } catch (\Throwable $e) {
            Log::error('BaileysGateway request failed', [
                'method' => $method,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
