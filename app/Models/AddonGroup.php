<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonGroup extends Model
{
    protected $fillable = ['name', 'is_required', 'is_multi_select', 'is_active'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_multi_select' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function addons()
    {
        return $this->hasMany(Addon::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_addon_groups');
    }
}
