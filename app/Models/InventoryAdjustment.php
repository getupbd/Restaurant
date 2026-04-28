<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $fillable = [
        'ingredient_id', 'adjustment_date', 'qty_before',
        'qty_adjusted', 'qty_after', 'reason', 'user_id',
    ];

    protected $casts = ['adjustment_date' => 'date'];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
