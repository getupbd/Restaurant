<?php
namespace App\Filament\Tenant\Resources\DeliveryPersonResource\Pages;
use App\Filament\Tenant\Resources\DeliveryPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDeliveryPersons extends ListRecords {
    protected static string $resource = DeliveryPersonResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
