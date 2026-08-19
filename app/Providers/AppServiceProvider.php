<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;
use App\Models\Admin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ini_set('serialize_precision', 14);
        ini_set('precision', 14);

    LogViewer::auth(function ($request) {
        return $request->user()
            && $request->user()->email === 'admin@admin.com';
    });

    }
}
