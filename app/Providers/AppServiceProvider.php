<?php

namespace App\Providers;

use App\Support\Content;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Content::class, fn () => new Content(base_path('content')));
    }

    public function boot(): void
    {
        // Treści dostępne w każdym widoku (również w slotach komponentów, które renderują się przed layoutem).
        View::composer('*', function (\Illuminate\View\View $view): void {
            $content = $this->app->make(Content::class);
            $view->with('content', $content);
            if (! array_key_exists('site', $view->getData())) {
                $view->with('site', $content->site());
            }
        });

        RateLimiter::for('beta-start', fn (Request $request) => Limit::perMinute((int) config('beta.rate_limit.start'))->by($request->ip()));
        RateLimiter::for('beta-progress', fn (Request $request) => Limit::perMinute((int) config('beta.rate_limit.progress'))->by($request->ip()));
    }
}
