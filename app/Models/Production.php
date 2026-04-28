<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'production_no', 'product_id', 'variant_id',
        'production_date', 'qty_produced',
        'total_cost', 'cost_per_unit', 'status', 'notes', 'user_id',
    ];

    protected $casts = ['production_date' => 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function items()
    {
        return $this->hasMany(ProductionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Auto-generate production number & deduct ingredient stock when saved
    protected static function booted()
    {
        static::creating(function ($p) {
            if (empty($p->production_no)) {
                $p->production_no = 'PRD-' . strtoupper(substr(uniqid(), -6));
            }
        });

        // After a production is created, deduct ingredient stock
        static::created(function ($production) {
            foreach ($production->items as $item) {
                $ingredient = $item->ingredient;
                if ($ingredient) {
                    $ingredient->decrement('stock_qty', $item->qty_required);
                }
            }
        });
    }
}
