<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = 0;
        
        Tenant::all()->each(function ($tenant) use (&$totalRevenue) {
            $totalRevenue += $tenant->run(function () {
                return \App\Models\Order::where('payment_status', 'paid')->sum('total_price');
            });
        });

        return [
            Stat::make('Total Tenants', Tenant::count())
                ->description('Total restaurants on platform')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success'),
            Stat::make('Global Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Settled payments across all tenants')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Active Invoices', \App\Models\Invoice::where('status', 'pending')->count())
                ->description('Outstanding tenant dues')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
        ];
    }
}
