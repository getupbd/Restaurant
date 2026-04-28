<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationSetting extends Model
{
    protected $fillable = [
        'advance_booking_days', 'max_party_size', 'slot_duration_minutes',
        'auto_confirm', 'allow_online_booking', 'time_slots', 'blocked_days_of_week',
    ];

    protected $casts = [
        'auto_confirm'          => 'boolean',
        'allow_online_booking'  => 'boolean',
        'time_slots'            => 'array',
        'blocked_days_of_week'  => 'array',
    ];

    // Get singleton settings row, create if not exists
    public static function current(): self
    {
        return self::firstOrCreate([], [
            'advance_booking_days'   => 30,
            'max_party_size'         => 20,
            'slot_duration_minutes'  => 60,
            'auto_confirm'           => false,
            'allow_online_booking'   => true,
            'time_slots'             => ['12:00', '13:00', '14:00', '18:00', '19:00', '20:00', '21:00'],
            'blocked_days_of_week'   => [],
        ]);
    }
}
