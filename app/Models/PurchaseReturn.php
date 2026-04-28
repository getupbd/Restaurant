<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'return_number', 'purchase_order_id', 'supplier_id',
        'return_date', 'total_amount', 'reason',
    ];

    protected $casts = ['return_date' => 'date'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    protected static function booted()
    {
        static::creating(function ($r) {
            if (empty($r->return_number)) {
                $r->return_number = 'PR-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }
}
