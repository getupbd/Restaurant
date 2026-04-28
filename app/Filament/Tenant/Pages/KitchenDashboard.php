<?php

namespace App\Filament\Tenant\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class KitchenDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $navigationGroup = 'Order Management';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Kitchen Dashboard';
    protected static string $view = 'filament.tenant.pages.kitchen-dashboard';
    protected static ?string $title = 'Kitchen Dashboard';

    public array $orders = [];

    public function mount(): void
    {
        $this->loadOrders();
    }

    #[On('echo:orders,.OrderPlaced')]
    public function loadOrders(): void
    {
        $this->orders = Order::with(['items.product', 'items.variant', 'table'])
            ->whereIn('status', ['pending', 'cooking', 'ready'])
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        $this->loadOrders();
    }
}
