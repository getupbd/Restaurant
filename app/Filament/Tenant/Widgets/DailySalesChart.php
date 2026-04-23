<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class DailySalesChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Sales (Last 7 Days)';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'paid')
                ->sum('total_price');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
