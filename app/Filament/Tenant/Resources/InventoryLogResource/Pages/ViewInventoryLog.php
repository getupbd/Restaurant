<?php

namespace App\Filament\Tenant\Resources\InventoryLogResource\Pages;

use App\Filament\Tenant\Resources\InventoryLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventoryLog extends ViewRecord
{
    protected static string $resource = InventoryLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
