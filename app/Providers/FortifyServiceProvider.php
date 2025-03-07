<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Inertia\Inertia;
use Inertia\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(TwoFactorChallengeViewResponse::class, function () {
        //     return new class implements TwoFactorChallengeViewResponse {
        //         public function toResponse($request)
        //         {
        //             return Inertia::render('Auth/TwoFactorChallenge')->toResponse($request);
        //         }
        //     };
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'user' => fn () => auth()->user(),
        ]);
    }
}
