<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestTracker
{
    /**
     * Log API route calls (success/failure) with duration and context.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('api_tracker.enabled')) {
            return $next($request);
        }

        $requestId = (string) Str::uuid();
        $start = microtime(true);

        $baseContext = [
            'tracker' => 'api_request',
            'request_id' => $requestId,
            'method' => $request->getMethod(),
            'path' => '/'.ltrim($request->path(), '/'),
            'route_name' => optional($request->route())->getName(),
            'route_action' => optional($request->route())->getActionName(),
            'ip' => $request->ip(),
            'user_id' => auth('sanctum')->id() ?? auth()->id(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ];

        if (config('api_tracker.log_payload_keys')) {
            $baseContext['payload_keys'] = array_keys($request->all());
        }

        if (config('api_tracker.log_payload')) {
            $baseContext['payload'] = $this->sanitizePayload($request);
        }

        $channel = config('api_tracker.channel');
        $logger = $channel ? Log::channel($channel) : Log::getFacadeRoot();

        try {
            /** @var Response $response */
            $response = $next($request);

            $status = (int) $response->getStatusCode();
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $logger->info('api.request', $baseContext + [
                'ok' => $status < 400,
                'status' => $status,
                'duration_ms' => $durationMs,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $logger->error('api.request_exception', $baseContext + [
                'ok' => false,
                'status' => 500,
                'duration_ms' => $durationMs,
                'exception' => class_basename($e),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function sanitizePayload(Request $request): array
    {
        $payload = $request->all();

        // Add file metadata without logging contents
        if ($request->files->count() > 0) {
            $payload['_files'] = collect($request->allFiles())
                ->map(function ($file) {
                    if (is_array($file)) {
                        return collect($file)->map(fn ($f) => $this->fileMeta($f))->all();
                    }
                    return $this->fileMeta($file);
                })
                ->all();
        }

        // Redact sensitive keys (case-insensitive)
        $redactKeys = array_map('strtolower', (array) config('api_tracker.redact_keys', []));
        $payload = $this->redactRecursive($payload, $redactKeys);

        // Truncate long strings
        $maxLen = (int) config('api_tracker.max_string_length', 2000);
        $payload = $this->truncateStringsRecursive($payload, $maxLen);

        return $payload;
    }

    private function redactRecursive(mixed $value, array $redactKeys): mixed
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                $keyLower = is_string($k) ? strtolower($k) : null;
                if ($keyLower !== null && in_array($keyLower, $redactKeys, true)) {
                    $out[$k] = '[REDACTED]';
                    continue;
                }
                $out[$k] = $this->redactRecursive($v, $redactKeys);
            }
            return $out;
        }

        return $value;
    }

    private function truncateStringsRecursive(mixed $value, int $maxLen): mixed
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->truncateStringsRecursive($v, $maxLen);
            }
            return $value;
        }

        if (is_string($value) && $maxLen > 0 && mb_strlen($value) > $maxLen) {
            return mb_substr($value, 0, $maxLen).'...[TRUNCATED]';
        }

        return $value;
    }

    private function fileMeta($file): array
    {
        try {
            return [
                'original_name' => method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null,
                'mime' => method_exists($file, 'getClientMimeType') ? $file->getClientMimeType() : null,
                'size' => method_exists($file, 'getSize') ? $file->getSize() : null,
            ];
        } catch (\Throwable) {
            return [
                'original_name' => null,
                'mime' => null,
                'size' => null,
            ];
        }
    }
}

