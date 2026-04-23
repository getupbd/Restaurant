<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'features',
        'limits',
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
