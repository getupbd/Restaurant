<?php

namespace App\Filament\Tenant\Resources\KitchenResource\Pages;

use App\Filament\Tenant\Resources\KitchenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKitchen extends EditRecord
{
    protected static string $resource = KitchenResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
