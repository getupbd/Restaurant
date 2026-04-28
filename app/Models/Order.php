<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice_no', 'order_type', 'table_id',
        'customer_id', 'waiter_id', 'delivery_person_id',
        'total_price', 'subtotal', 'discount_amount', 'discount_type',
        'coupon_code', 'vat_amount', 'event_code', 'voucher_no',
        'status', 'payment_status', 'payment_method',
        'is_paid', 'paid_at', 'notes',
    ];

    protected $casts = [
        'is_paid'  => 'boolean',
        'paid_at'  => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING   = 'pending';
    const STATUS_COOKING   = 'cooking';
    const STATUS_READY     = 'ready';
    const STATUS_SERVED    = 'served';
    const STATUS_PAID      = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const ORDER_TYPE_DINE_IN   = 'dine_in';
    const ORDER_TYPE_TAKEAWAY  = 'takeaway';
    const ORDER_TYPE_DELIVERY  = 'delivery';
    const ORDER_TYPE_ONLINE    = 'online';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING   => 'Pending',
            self::STATUS_COOKING   => 'Cooking',
            self::STATUS_READY     => 'Ready',
            self::STATUS_SERVED    => 'Served',
            self::STATUS_PAID      => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function orderTypeOptions(): array
    {
        return [
            self::ORDER_TYPE_DINE_IN  => 'Dine In',
            self::ORDER_TYPE_TAKEAWAY => 'Take Away',
            self::ORDER_TYPE_DELIVERY => 'Delivery',
            self::ORDER_TYPE_ONLINE   => 'Online',
        ];
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(DeliveryPerson::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Auto-generate invoice number
    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->invoice_no)) {
                $order->invoice_no = 'INV-' . strtoupper(substr(uniqid(), -7));
            }
        });
    }
}
