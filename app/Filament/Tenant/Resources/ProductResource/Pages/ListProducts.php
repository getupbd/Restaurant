<?php

namespace App\Filament\Tenant\Resources\ProductResource\Pages;

use App\Filament\Tenant\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function toggleVariantField($variantId, $field)
    {
        $variant = \App\Models\ProductVariant::find($variantId);
        if ($variant && in_array($field, ['is_active', 'is_stock_validate'])) {
            $variant->update([$field => !$variant->$field]);
        }
    }
}
