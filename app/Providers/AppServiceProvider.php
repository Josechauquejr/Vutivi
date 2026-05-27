<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\Resource;
use App\Observers\ReservationObserver;
use App\Policies\ReservationPolicy;
use App\Policies\ResourcePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Policies
        Gate::policy(Reservation::class, ReservationPolicy::class);
        Gate::policy(Resource::class, ResourcePolicy::class);

        // Observer
        Reservation::observe(ReservationObserver::class);

        // Rate limiter for reservation write operations (10 per minute per user)
        RateLimiter::for('reservations', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
        });
    }
}
