<?php

namespace App\Filament\Tenant\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use Filament\Notifications\Notification;

class Kitchen extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static string $view = 'filament.tenant.pages.kitchen';
    protected static ?string $title = 'Kitchen Display (KDS)';

    public function getOrders()
    {
        return Order::whereIn('status', ['pending', 'cooking', 'ready'])
            ->with(['items.product', 'table'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($order) {
                $minutes = (int) $order->created_at->diffInMinutes(now());
                if ($minutes < 60) {
                    $order->elapsed = $minutes . 'm';
                } else {
                    $h = intdiv($minutes, 60);
                    $m = $minutes % 60;
                    $order->elapsed = $m > 0 ? "{$h}h {$m}m" : "{$h}h";
                }
                $order->elapsed_minutes = $minutes;
                return $order;
            });
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['status' => $status]);
            Notification::make()
                ->title("Order #{$orderId} → " . ucfirst($status))
                ->success()
                ->send();
        }
    }
}

