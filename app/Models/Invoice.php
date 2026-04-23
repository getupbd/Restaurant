<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $connection = 'mysql';
    
    protected $fillable = [
        'tenant_id',
        'amount',
        'description',
        'status',
        'due_date',
        'paid_at'
    ];
}
