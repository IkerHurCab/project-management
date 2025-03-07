<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'user' => fn () => auth()->user(),
            'notifications' => function () {
                if (auth()->check()) {
                    // Obtener notificaciones formateadas usando el servicio
                    $notificationService = app(NotificationService::class);
                    return $notificationService->getFormattedNotifications();
                }
                return [];
            }
        ]);
    }
}
