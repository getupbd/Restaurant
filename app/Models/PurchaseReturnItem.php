<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = ['purchase_return_id', 'ingredient_id', 'qty', 'rate', 'total'];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
