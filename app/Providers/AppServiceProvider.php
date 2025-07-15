<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    $logPath = storage_path('logs/laravel.log');

    if (!file_exists(dirname($logPath))) {
        mkdir(dirname($logPath), 0755, true);
    }

    if (!file_exists($logPath)) {
        file_put_contents($logPath, '');
    }
}
}
