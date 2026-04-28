<?php
namespace App\Filament\Tenant\Resources\AddonGroupResource\Pages;
use App\Filament\Tenant\Resources\AddonGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListAddonGroups extends ListRecords {
    protected static string $resource = AddonGroupResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
