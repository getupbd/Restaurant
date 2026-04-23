<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class GrowthForecastWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Growth Forecast';
    protected static string $color = 'success';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // 1. Fetch sales for the last 7 days
        $data = Order::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(6))
            ->get()
            ->groupBy(fn ($order) => $order->created_at->format('Y-m-d'))
            ->map(fn ($orders) => $orders->sum('total_price'));

        $labels = [];
        $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;
            $values[] = $data[$date] ?? 0;
        }

        // 2. Simple Prediction Logic (Linear Trend)
        $avgGrowth = count($values) > 1 ? ($values[6] - $values[0]) / 6 : 0;
        
        $forecastLabels = [];
        $forecastValues = $values;
        for ($i = 1; $i <= 3; $i++) {
            $labels[] = now()->addDays($i)->format('Y-m-d') . ' (est)';
            $forecastValues[] = max(0, $values[6] + ($avgGrowth * $i));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Actual & Predicted Revenue',
                    'data' => $forecastValues,
                    'borderColor' => '#10b981',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
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
