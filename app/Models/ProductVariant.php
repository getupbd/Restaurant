<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'barcode', 'price', 
        'offer_rate', 'offer_start_date', 'offer_end_date',
        'stock_quantity', 'is_stock_validate', 'is_active'
    ];

    protected $casts = [
        'offer_start_date' => 'date',
        'offer_end_date' => 'date',
        'is_stock_validate' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->belongsToMany(VariantOption::class, 'product_variant_options', 'product_variant_id', 'variant_option_id');
    }
}
