<?php

namespace App\Filament\Tenant\Resources\OrderResource\Pages;

use App\Filament\Tenant\Resources\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class CompleteOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'Completed Orders';
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()->whereIn('status', ['served', 'paid']);
    }
}
