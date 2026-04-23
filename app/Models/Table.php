<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'area_id',
        'name',
        'capacity',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
