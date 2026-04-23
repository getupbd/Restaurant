<?php

namespace App\Providers;

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
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);

        // Synchronize Global Landing Page Locale
        if ($locale = session('app_locale')) {
            app()->setLocale($locale);
        }

        \Livewire\Livewire::setUpdateRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::post('/livewire/update', $handle)
                ->middleware([
                    'web',
                    'universal',
                    \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
                ]);
        });
    }
}
