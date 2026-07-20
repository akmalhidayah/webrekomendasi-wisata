<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        Paginator::useBootstrapFive();

        if (config('app.force_https')) {
            URL::forceHttps();
            URL::forceRootUrl(config('app.url'));
        }

        RateLimiter::for('admin-login', fn (Request $request) => Limit::perMinute(5)
            ->by(strtolower(trim((string) $request->input('email'))).'|'.$request->ip())
            ->response(fn () => back()->withErrors(['email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam satu menit.'])));
        RateLimiter::for('rating', fn (Request $request) => Limit::perMinute(10)->by($this->guestKey($request)));
        RateLimiter::for('survey', fn (Request $request) => Limit::perMinute(5)->by($this->guestKey($request)));
        RateLimiter::for('recommendation', fn (Request $request) => Limit::perMinute(3)->by($this->guestKey($request)));
    }

    private function guestKey(Request $request): string
    {
        return hash('sha256', (string) $request->session()->get('kode_guest', 'new').'|'.$request->ip());
    }
}
