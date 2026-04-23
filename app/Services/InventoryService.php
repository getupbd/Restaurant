<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public static function adjustStock(Product $product, $amount, $reason, $referenceType = null, $referenceId = null)
    {
        if (!$product->track_stock) {
            return;
        }

        DB::transaction(function () use ($product, $amount, $reason, $referenceType, $referenceId) {
            // Lock the product row for update to prevent race conditions
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->first();
            
            $oldQuantity = $lockedProduct->stock_quantity;
            $newQuantity = $oldQuantity + $amount;
            
            $lockedProduct->update([
                'stock_quantity' => $newQuantity
            ]);

            // Create inventory log entry
            DB::table('inventory_logs')->insert([
                'product_id' => $product->id,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'change_amount' => $amount,
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
