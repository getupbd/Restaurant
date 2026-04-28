<?php
namespace App\Filament\Tenant\Resources\UnavailableDayResource\Pages;
use App\Filament\Tenant\Resources\UnavailableDayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditUnavailableDay extends EditRecord {
    protected static string $resource = UnavailableDayResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
