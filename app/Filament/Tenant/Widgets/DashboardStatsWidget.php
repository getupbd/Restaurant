<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todaySales = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $yesterdaySales = Order::whereDate('created_at', yesterday())
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $salesTrend = $yesterdaySales > 0
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : 0;

        $todayOrders = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalProducts = Product::where('is_active', true)->count();

        $weeklySales = collect(range(6, 0))->map(fn($i) =>
            Order::whereDate('created_at', now()->subDays($i))
                ->where('payment_status', 'paid')
                ->sum('total_price')
        )->toArray();

        return [
            Stat::make('Today\'s Revenue', '৳' . number_format($todaySales, 2))
                ->description($salesTrend >= 0 ? "+{$salesTrend}% from yesterday" : "{$salesTrend}% from yesterday")
                ->descriptionIcon($salesTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($weeklySales)
                ->color($salesTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Today\'s Orders', $todayOrders)
                ->description("{$pendingOrders} pending right now")
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('Active Menu Items', $totalProducts)
                ->description('Available for ordering')
                ->descriptionIcon('heroicon-m-cake')
                ->color('warning'),
        ];
    }
}
