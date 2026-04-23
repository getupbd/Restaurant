<?php

namespace App\Filament\Tenant\Resources\InventoryLogResource\Pages;

use App\Filament\Tenant\Resources\InventoryLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryLog extends EditRecord
{
    protected static string $resource = InventoryLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
