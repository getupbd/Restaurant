<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name', 'code', 'unit', 'purchase_price',
        'stock_qty', 'min_stock_level', 'opening_stock', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock_qty' => 'decimal:4',
        'min_stock_level' => 'decimal:4',
        'opening_stock' => 'decimal:4',
    ];

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function consumptionLogs()
    {
        return $this->hasMany(IngredientConsumptionLog::class);
    }

    public function adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->min_stock_level;
    }
}
