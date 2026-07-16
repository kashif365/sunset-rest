<?php

namespace App\Providers;

use App\Models\User;
use App\Services\CartService;
use App\Services\Payments\PaymentManager;
use App\Services\SettingsService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
        $this->app->singleton(PaymentManager::class);
    }

    public function boot(): void
    {
        $this->defineGates();
        $this->defineRateLimiters();
        $this->composeViews();
    }

    private function defineGates(): void
    {
        // Any active back-office user.
        Gate::define('access-admin', fn (User $user) => $user->is_active
            && in_array($user->role, User::ROLES, true));

        // Menu, content, marketing management: manager and above.
        Gate::define('manage-content', fn (User $user) => $user->is_active && $user->canManageContent());

        // Orders can be worked by every active role, including staff.
        Gate::define('manage-orders', fn (User $user) => $user->is_active
            && in_array($user->role, User::ROLES, true));

        // Business settings and user management: super admin only.
        Gate::define('manage-settings', fn (User $user) => $user->is_active && $user->isSuperAdmin());
        Gate::define('manage-users', fn (User $user) => $user->is_active && $user->isSuperAdmin());
    }

    private function defineRateLimiters(): void
    {
        RateLimiter::for('public-forms', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip().'|'.(string) $request->input('email'));
        });
    }

    private function composeViews(): void
    {
        // Shared data for every public page: settings, navigation, cart badge.
        View::composer('layouts.public', function ($view) {
            $settings = app(SettingsService::class);

            $view->with([
                'siteSettings' => $settings,
                'cartCount' => app(CartService::class)->count(),
                'headerLinks' => \App\Models\NavigationLink::location('header')->get(),
                'footerLinks' => \App\Models\NavigationLink::location('footer')->get(),
                'footerHours' => \App\Models\BusinessHour::orderBy('day_of_week')->get(),
            ]);
        });
    }
}
