<?php
namespace App\Filament\Tenant\Resources\ProductionResource\Pages;
use App\Filament\Tenant\Resources\ProductionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewProduction extends ViewRecord {
    protected static string $resource = ProductionResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
