<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Cloud\Translate\V2\TranslateClient;

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
        if (env('GOOGLE_APPLICATION_CREDENTIALS')) {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path(env('GOOGLE_APPLICATION_CREDENTIALS')));
        }
    }
}
