<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number', 'supplier_id', 'order_date', 'expected_delivery_date',
        'status', 'product_type', 'subtotal', 'vat_amount', 'grand_total', 'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    // Auto-update ingredient stock when PO is marked as received
    protected static function booted()
    {
        static::creating(function ($po) {
            if (empty($po->po_number)) {
                $po->po_number = 'PO-' . strtoupper(substr(uniqid(), -6));
            }
        });

        static::updated(function ($po) {
            if ($po->isDirty('status') && $po->status === 'received') {
                foreach ($po->items as $item) {
                    $ingredient = $item->ingredient;
                    if ($ingredient) {
                        $ingredient->increment('stock_qty', $item->qty);
                        $ingredient->update(['purchase_price' => $item->rate]);
                    }
                }
            }
        });
    }
}
