<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\InventoryService;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Only deduct if payment status changed from unpaid to paid
        if ($order->isDirty('payment_status') && 
            $order->payment_status === 'paid' && 
            $order->getOriginal('payment_status') !== 'paid'
        ) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    InventoryService::adjustStock(
                        $item->product, 
                        -($item->quantity), 
                        "Order #{$order->id} Paid", 
                        Order::class, 
                        $order->id
                    );
                }
            }
        }
    }
}
