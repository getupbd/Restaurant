<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Handle Locale Switching
    if (request('locale')) {
        session(['app_locale' => request('locale')]);
        app()->setLocale(request('locale'));
    } else {
        app()->setLocale(session('app_locale', 'en'));
    }

    // Collect Ecosystem Stats for the Landing Page
    $totalRevenue = 0;
    $tenants = \App\Models\Tenant::all();
    
    foreach ($tenants as $tenant) {
        $tenant->run(function() use (&$totalRevenue) {
            $totalRevenue += \App\Models\Order::where('status', 'paid')->sum('total_price');
        });
    }

    return view('welcome', [
        'tenantCount' => count($tenants),
        'ecosystemGtv' => $totalRevenue,
        'appLocale' => app()->getLocale()
    ]);
});

Route::get('/test-login', function() {
    $tenant = \App\Models\Tenant::find('bdt-restaurant');
    tenancy()->initialize($tenant);
    
    $attempt = \Illuminate\Support\Facades\Auth::attempt([
        'email' => 'admin@bdt-restaurant.com', 
        'password' => 'password'
    ]);
    
    return [
        'success' => $attempt,
        'user' => \Illuminate\Support\Facades\Auth::user(),
        'db_connection' => \Illuminate\Support\Facades\DB::connection()->getName(),
        'tenant' => tenant('id')
    ];
});
