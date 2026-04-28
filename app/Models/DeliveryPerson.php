<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPerson extends Model
{
    protected $table = 'delivery_persons';

    protected $fillable = ['name', 'phone', 'commission_rate', 'is_active'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
