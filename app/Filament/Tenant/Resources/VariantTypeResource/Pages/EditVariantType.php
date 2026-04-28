<?php

namespace App\Filament\Tenant\Resources\VariantTypeResource\Pages;

use App\Filament\Tenant\Resources\VariantTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVariantType extends EditRecord
{
    protected static string $resource = VariantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
