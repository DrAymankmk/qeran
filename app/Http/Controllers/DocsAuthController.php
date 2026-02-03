<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocsAuthController extends Controller
{
    /**
     * Show the API docs login form.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (empty(config('scribe.laravel.docs_password'))) {
            return redirect()->route('scribe');
        }

        if ($request->session()->get('docs_authenticated')) {
            return redirect()->route('scribe');
        }

        return view('docs.login');
    }

    /**
     * Authenticate and allow access to /docs.
     */
    public function login(Request $request): RedirectResponse
    {
        if (empty(config('scribe.laravel.docs_password'))) {
            return redirect()->route('scribe');
        }

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = config('scribe.laravel.docs_username');
        $password = config('scribe.laravel.docs_password');

        if ($request->username !== $username || $request->password !== $password) {
            return redirect()
                ->route('docs.login')
                ->withInput($request->only('username'))
                ->with('error', __('admin.docs-invalid-credentials'));
        }

        $request->session()->put('docs_authenticated', true);

        return redirect()->intended(route('scribe'));
    }

    /**
     * Log out from docs access.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('docs_authenticated');

        return redirect()->route('docs.login');
    }
}
