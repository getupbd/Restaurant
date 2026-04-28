<?php
namespace App\Filament\Tenant\Resources\ReservationResource\Pages;
use App\Filament\Tenant\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListReservations extends ListRecords {
    protected static string $resource = ReservationResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
