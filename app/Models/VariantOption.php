<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VariantOption extends Model
{
    protected $fillable = [
        'variant_type_id',
        'name',
        'value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function variantType(): BelongsTo
    {
        return $this->belongsTo(VariantType::class);
    }

    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_options', 'variant_option_id', 'product_variant_id');
    }
}
