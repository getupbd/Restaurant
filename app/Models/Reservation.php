<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'booking_ref', 'customer_name', 'customer_phone', 'customer_email',
        'booking_date', 'booking_time', 'party_size', 'table_id',
        'special_request', 'status', 'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SEATED    = 'seated';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW   = 'no_show';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING   => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_SEATED    => 'Seated',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NO_SHOW   => 'No Show',
        ];
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    protected static function booted()
    {
        static::creating(function ($r) {
            if (empty($r->booking_ref)) {
                $r->booking_ref = 'BK-' . strtoupper(substr(uniqid(), -7));
            }
        });
    }
}
