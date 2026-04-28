<?php
namespace App\Filament\Tenant\Resources\ProductionResource\Pages;
use App\Filament\Tenant\Resources\ProductionResource;
use Filament\Resources\Pages\CreateRecord;
class CreateProduction extends CreateRecord {
    protected static string $resource = ProductionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
