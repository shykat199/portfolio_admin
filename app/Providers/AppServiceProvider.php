<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(function (): Password {
            $rule = Password::min(12)->letters()->mixedCase()->numbers()->symbols();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        View::composer('components.layouts.app', function ($view): void {
            $view->with('unreadMessages', Message::query()->unread()->count());
        });

        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
