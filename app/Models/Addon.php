<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $fillable = ['addon_group_id', 'name', 'price', 'is_active'];

    public function group()
    {
        return $this->belongsTo(AddonGroup::class, 'addon_group_id');
    }
}
