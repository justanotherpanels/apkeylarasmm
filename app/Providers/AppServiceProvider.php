<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

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
        Http::macro('external', function () {
            $verifySsl = config('services.http.verify_ssl');

            if ($verifySsl === null) {
                $verifySsl = !app()->environment(['local', 'development']);
            } else {
                $verifySsl = filter_var($verifySsl, FILTER_VALIDATE_BOOL);
            }

            return Http::withOptions([
                'verify' => (bool) $verifySsl,
            ]);
        });
    }
}
