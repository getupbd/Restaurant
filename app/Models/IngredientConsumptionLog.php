<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientConsumptionLog extends Model
{
    protected $fillable = [
        'ingredient_id', 'order_id', 'qty_consumed', 'consumed_date', 'source',
    ];

    protected $casts = ['consumed_date' => 'date'];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
