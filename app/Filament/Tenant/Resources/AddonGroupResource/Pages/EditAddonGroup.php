<?php
namespace App\Filament\Tenant\Resources\AddonGroupResource\Pages;
use App\Filament\Tenant\Resources\AddonGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditAddonGroup extends EditRecord {
    protected static string $resource = AddonGroupResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
