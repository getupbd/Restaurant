<?php

namespace App\Filament\Tenant\Pages;

use App\Filament\Tenant\Widgets\DashboardStatsWidget;
use App\Filament\Tenant\Widgets\DailySalesChart;
use App\Filament\Tenant\Widgets\RecentOrdersWidget;
use App\Filament\Tenant\Widgets\TopProductsWidget;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table as DiningTable;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -1;
    protected static string $view = 'filament.tenant.pages.dashboard';

    public function getViewData(): array
    {
        $todaySales = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $monthSales = Order::whereMonth('created_at', now()->month)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $todayOrders = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $recentOrders = Order::with(['table', 'items.product'])
            ->latest()
            ->limit(8)
            ->get();

        $topProducts = \App\Models\OrderItem::query()
            ->select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty'), \Illuminate\Support\Facades\DB::raw('SUM(subtotal) as total_rev'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $weeklySales = collect(range(6, 0))->map(fn($i) => [
            'day' => now()->subDays($i)->format('D'),
            'date' => now()->subDays($i)->format('M d'),
            'revenue' => (float) Order::whereDate('created_at', now()->subDays($i))
                ->where('payment_status', 'paid')
                ->sum('total_price'),
            'orders' => Order::whereDate('created_at', now()->subDays($i))->count(),
        ])->values();

        $activeMenus = Product::where('is_active', true)->count();

        return compact(
            'todaySales', 'monthSales', 'todayOrders',
            'pendingOrders', 'recentOrders', 'topProducts',
            'weeklySales', 'activeMenus'
        );
    }

    public function getWidgets(): array
    {
        return [];
    }
}
