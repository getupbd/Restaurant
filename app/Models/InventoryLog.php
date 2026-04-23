<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'product_id',
        'old_quantity',
        'new_quantity',
        'change_amount',
        'reason',
        'reference_type',
        'reference_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
