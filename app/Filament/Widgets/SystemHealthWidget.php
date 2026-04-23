<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemHealthWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Database Health
        $dbStatus = true;
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = false;
        }


        // 3. Global Stats (Cross-Tenant Aggregation)
        $totalRevenue = 0;
        $activeTenantsList = \App\Models\Tenant::all();
        
        foreach ($activeTenantsList as $tenant) {
            $tenant->run(function() use (&$totalRevenue) {
                $totalRevenue += \App\Models\Order::where('status', 'paid')->sum('total_price');
            });
        }

        return [
            Stat::make('Ecosystem GTV', '৳' . number_format($totalRevenue, 2))
                ->description('Total Gross Transaction Value')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Active Tenants', count($activeTenantsList))
                ->description('Total restaurants in ecosystem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Central DB Status', $dbStatus ? 'Online' : 'Degraded')
                ->description($dbStatus ? 'Healthy' : 'Connection issues detected')
                ->descriptionIcon($dbStatus ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($dbStatus ? 'success' : 'danger'),
        ];
    }
}
