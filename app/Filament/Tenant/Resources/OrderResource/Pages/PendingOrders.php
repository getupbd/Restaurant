<?php

namespace App\Filament\Tenant\Resources\OrderResource\Pages;

use App\Filament\Tenant\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class PendingOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'Pending Orders';
    }

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()->whereIn('status', ['pending', 'cooking', 'ready']);
    }
}
