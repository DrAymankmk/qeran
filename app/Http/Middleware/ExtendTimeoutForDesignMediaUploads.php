<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Large design videos often exceed default max_execution_time while the body is received
 * and while PHP streams to S3/Wasabi. Runs before FormRequest validation.
 *
 * Env: DESIGN_MEDIA_UPLOAD_TIME_LIMIT — seconds (e.g. 3600). Empty/unset = unlimited (0).
 * Also configure the web server (nginx fastcgi_read_timeout, proxy_read_timeout, client_body_timeout).
 */
class ExtendTimeoutForDesignMediaUploads
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        if ($route && in_array($route->getActionMethod(), ['store', 'update'], true)) {
            $configured = env('DESIGN_MEDIA_UPLOAD_TIME_LIMIT');

            if ($configured === null || $configured === '') {
                @set_time_limit(0);
            } else {
                @set_time_limit(max(60, (int) $configured));
            }

            if (function_exists('ini_set')) {
                @ini_set('max_input_time', '-1');
            }
        }

        return $next($request);
    }
}
