<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class DailySalesChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue — Last 7 Days';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $orderCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('D, M d');
            $data[] = (float) Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'paid')
                ->sum('total_price');
            $orderCounts[] = Order::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (৳)',
                    'data' => $data,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99,102,241,0.12)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#6366f1',
                    'pointRadius' => 5,
                ],
                [
                    'label' => 'Orders',
                    'data' => $orderCounts,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245,158,11,0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#f59e0b',
                    'pointRadius' => 4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
