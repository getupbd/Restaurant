<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Restaurants', \App\Models\Tenant::count())
                ->description('Active restaurants on the platform')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success'),
            Stat::make('Available Packages', \App\Models\Package::count())
                ->description('Subscription tiers')
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('info'),
            Stat::make('Global Domains', \Stancl\Tenancy\Database\Models\Domain::count())
                ->description('Active domains & subdomains')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('warning'),
        ];
    }
}
