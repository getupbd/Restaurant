<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionItem extends Model
{
    protected $fillable = [
        'production_id', 'ingredient_id',
        'unit', 'qty_required', 'current_rate', 'total_cost',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
