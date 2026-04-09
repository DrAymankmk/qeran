<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
}

