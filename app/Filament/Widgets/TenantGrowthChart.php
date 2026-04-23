<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;

class TenantGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Tenant Growth (Last 7 Days)';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = Tenant::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Restaurants',
                    'data' => $data,
                    'borderColor' => '#0ea5e9',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
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
