<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectDocsWithBasicAuth
{
    /**
     * Handle an incoming request. Protect docs via login page (session).
     * Set DOCS_PASSWORD in .env to enable; otherwise /docs is open.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $password = config('scribe.laravel.docs_password');

        if (empty($password)) {
            return $next($request);
        }

        if ($request->session()->get('docs_authenticated')) {
            return $next($request);
        }

        return redirect()->route('docs.login')->with('url.intended', $request->fullUrl());
    }
}
