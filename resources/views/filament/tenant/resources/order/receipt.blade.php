<div class="p-4 bg-white text-black font-mono text-[11px] leading-tight w-[80mm] mx-auto print:p-0 print:w-full">
    <div class="text-center mb-6">
        <h1 class="text-lg font-black uppercase tracking-tighter">{{ tenant('id') }}</h1>
        <p class="text-[9px] font-bold opacity-60 uppercase tracking-widest">{{ __('messages.menu') }}</p>
    </div>

    <div class="border-b border-dashed border-black pb-2 mb-4">
        <div class="flex justify-between">
            <span>Order #{{ $order->id }}</span>
            <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
        </div>
        <div class="flex justify-between font-black uppercase">
            <span>{{ $order->table?->name ?? 'Walk-In' }}</span>
            <span>Status: {{ ucfirst($order->status) }}</span>
        </div>
    </div>

    <table class="w-full mb-6">
        <thead>
            <tr class="border-b border-black text-left">
                <th class="py-1">Item</th>
                <th class="py-1 text-center">Qty</th>
                <th class="py-1 text-right italic">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="py-1 uppercase font-black">{{ $item->product?->name }}</td>
                    <td class="py-1 text-center">{{ (int)$item->quantity }}</td>
                    <td class="py-1 text-right">{{ tenant('currency_symbol') }}{{ number_format($item->subtotal, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-t border-black pt-2 mb-6 text-right">
        <div class="flex justify-between text-base font-black">
            <span>{{ __('messages.total') }}</span>
            <span>{{ tenant('currency_symbol') }}{{ number_format($order->total_price, 0) }}</span>
        </div>
    </div>

    <div class="text-center italic opacity-60">
        <p>Thank you for dining with us!</p>
        <p class="mt-1">আবার দেখা হবে!</p>
    </div>

    <div class="mt-8 pt-4 border-t border-dashed border-black text-center opacity-40 text-[8px]">
        Powered by Antigravity OS
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .print-modal, .print-modal * { visibility: visible; }
        .print-modal { position: absolute; left: 0; top: 0; width: 100%; }
        button, footer, header { display: none !important; }
    }
</style>
