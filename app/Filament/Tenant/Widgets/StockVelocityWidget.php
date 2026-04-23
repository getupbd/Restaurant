<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Product;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockVelocityWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // 1. Fetch top 3 products with stock tracking enabled
        $products = Product::where('track_stock', true)
            ->where('stock_quantity', '>', 0)
            ->limit(3)
            ->get();

        $stats = [];

        foreach ($products as $product) {
            // 2. Calculate average daily sales over last 30 days
            $totalSold = OrderItem::where('product_id', $product->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('quantity');
            
            $dailyVelocity = $totalSold / 30;
            
            // 3. Predict days remaining
            if ($dailyVelocity > 0) {
                $daysRemaining = floor($product->stock_quantity / $dailyVelocity);
                $label = "Est. {$daysRemaining} days left";
                $color = $daysRemaining < 3 ? 'danger' : ($daysRemaining < 7 ? 'warning' : 'success');
            } else {
                $label = "No recent sales data";
                $color = 'gray';
            }

            $stats[] = Stat::make($product->name, $product->stock_quantity)
                ->description($label)
                ->descriptionIcon($dailyVelocity > 0 && $daysRemaining < 3 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-chart-bar')
                ->color($color);
        }

        return $stats;
    }
}
