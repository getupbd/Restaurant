<?php

namespace App\Filament\Tenant\Resources\InventoryLogResource\Pages;

use App\Filament\Tenant\Resources\InventoryLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryLog extends CreateRecord
{
    protected static string $resource = InventoryLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = \App\Models\Product::findOrFail($data['product_id']);
        
        $data['old_quantity'] = $product->stock_quantity ?? 0;
        $data['new_quantity'] = $data['old_quantity'] + $data['change_amount'];
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $product = \App\Models\Product::findOrFail($this->record->product_id);
        
        $product->update([
            'stock_quantity' => $this->record->new_quantity,
        ]);
    }
}
