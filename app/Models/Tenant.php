<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'currency',
            'locale',
        ];
    }

    protected $casts = [
        'data' => 'array',
        'currency' => 'string',
        'locale' => 'string',
        'trial_ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function isSubscribed(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->trial_ends_at && $this->trial_ends_at->isFuture()) {
            return true;
        }

        // Add more logic here (e.g., checking if there's a paid invoice for this month)
        return false;
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match ($this->currency) {
            'BDT' => '৳',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => '৳',
        };
    }
}
