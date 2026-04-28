<?php

namespace App\Filament\Tenant\Resources\VariantResource\Pages;
use App\Filament\Tenant\Resources\VariantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditVariant extends EditRecord {
    protected static string $resource = VariantResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
