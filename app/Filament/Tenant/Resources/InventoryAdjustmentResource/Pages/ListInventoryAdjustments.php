<?php
namespace App\Filament\Tenant\Resources\InventoryAdjustmentResource\Pages;
use App\Filament\Tenant\Resources\InventoryAdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListInventoryAdjustments extends ListRecords {
    protected static string $resource = InventoryAdjustmentResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
