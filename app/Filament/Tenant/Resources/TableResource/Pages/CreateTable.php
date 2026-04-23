<?php

namespace App\Filament\Tenant\Resources\TableResource\Pages;

use App\Filament\Tenant\Resources\TableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTable extends CreateRecord
{
    protected static string $resource = TableResource::class;
}
