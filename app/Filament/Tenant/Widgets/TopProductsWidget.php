<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class TopProductsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(3)
            ->get();

        $stats = [];
        
        foreach ($topProducts as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $stats[] = Stat::make($product->name, $item->total_quantity . ' Sold')
                    ->description('Price: $' . number_format($product->price, 2))
                    ->descriptionIcon('heroicon-m-sparkles')
                    ->color('success');
            }
        }

        if (empty($stats)) {
            $stats[] = Stat::make('Top Products', 'No sales yet')
                ->description('Start selling to see data')
                ->color('gray');
        }

        return $stats;
    }
}
