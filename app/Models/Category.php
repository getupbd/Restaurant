<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'color', 'image', 'icon',
        'offer_rate', 'offer_start_date', 'offer_end_date',
        'position', 'show_on_web', 'is_active',
    ];

    protected $casts = [
        'offer_start_date' => 'date',
        'offer_end_date'   => 'date',
        'show_on_web'      => 'boolean',
        'is_active'        => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
