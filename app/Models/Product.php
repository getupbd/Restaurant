<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'kitchen_id',
        'name', 'slug', 'code', 'description', 'notes',
        'price', 'image',
        'storage_unit', 'conversion_qty',
        'is_stock_validate', 'without_production', 'add_as_ingredient',
        'offer_rate', 'offer_start_date', 'offer_end_date',
        'is_special', 'allow_custom_qty', 'is_visible_on_web', 'is_price_editable',
        'cooking_time', 'vat_rate', 'position',
        'is_active',
        // legacy inventory fields
        'stock_quantity', 'min_stock_level', 'track_stock',
    ];

    protected $casts = [
        'offer_start_date'    => 'date',
        'offer_end_date'      => 'date',
        'is_stock_validate'   => 'boolean',
        'without_production'  => 'boolean',
        'add_as_ingredient'   => 'boolean',
        'is_special'          => 'boolean',
        'allow_custom_qty'    => 'boolean',
        'is_visible_on_web'   => 'boolean',
        'is_price_editable'   => 'boolean',
        'is_active'           => 'boolean',
        'track_stock'         => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function kitchen()
    {
        return $this->belongsTo(Kitchen::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function addonGroups()
    {
        return $this->belongsToMany(AddonGroup::class, 'product_addon_groups');
    }
}
