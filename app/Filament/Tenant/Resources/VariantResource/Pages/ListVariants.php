<?php

namespace App\Filament\Tenant\Resources\VariantResource\Pages;
use App\Filament\Tenant\Resources\VariantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListVariants extends ListRecords {
    protected static string $resource = VariantResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
