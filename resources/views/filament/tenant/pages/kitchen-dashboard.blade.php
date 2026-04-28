<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-xl bg-warning-50 border border-warning-200 p-4 text-center dark:bg-warning-900/20">
                <p class="text-3xl font-black text-warning-600">{{ collect($orders)->where('status', 'pending')->count() }}</p>
                <p class="text-sm font-semibold text-warning-700 uppercase tracking-widest mt-1">⏳ Pending</p>
            </div>
            <div class="rounded-xl bg-primary-50 border border-primary-200 p-4 text-center dark:bg-primary-900/20">
                <p class="text-3xl font-black text-primary-600">{{ collect($orders)->where('status', 'cooking')->count() }}</p>
                <p class="text-sm font-semibold text-primary-700 uppercase tracking-widest mt-1">🔥 Cooking</p>
            </div>
            <div class="rounded-xl bg-success-50 border border-success-200 p-4 text-center dark:bg-success-900/20">
                <p class="text-3xl font-black text-success-600">{{ collect($orders)->where('status', 'ready')->count() }}</p>
                <p class="text-sm font-semibold text-success-700 uppercase tracking-widest mt-1">✅ Ready</p>
            </div>
        </div>

        {{-- Refresh Button --}}
        <div class="flex justify-end">
            <button wire:click="loadOrders"
                class="flex items-center gap-2 rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Refresh
            </button>
        </div>

        {{-- Order Tickets Grid --}}
        @if(count($orders) === 0)
            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-16 text-center">
                <p class="text-5xl mb-4">👨‍🍳</p>
                <p class="text-xl font-semibold text-gray-600 dark:text-gray-300">No active orders</p>
                <p class="text-sm text-gray-400 mt-1">All caught up! Waiting for new orders...</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($orders as $order)
                    @php
                        $status = $order['status'];
                        $cardColor = match($status) {
                            'pending' => 'border-warning-400 bg-warning-50 dark:bg-warning-900/10',
                            'cooking' => 'border-primary-400 bg-primary-50 dark:bg-primary-900/10',
                            'ready'   => 'border-success-400 bg-success-50 dark:bg-success-900/10',
                            default   => 'border-gray-300',
                        };
                        $elapsed = now()->diffInMinutes(\Carbon\Carbon::parse($order['created_at']));
                        $urgent = $elapsed > 15;
                    @endphp

                    <div class="rounded-xl border-2 {{ $cardColor }} {{ $urgent ? 'ring-2 ring-danger-400' : '' }} overflow-hidden shadow-sm transition hover:shadow-md">
                        {{-- Ticket Header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-opacity-30
                            {{ $status === 'pending' ? 'border-warning-300' : ($status === 'cooking' ? 'border-primary-300' : 'border-success-300') }}">
                            <div>
                                <p class="font-black text-lg">{{ $order['invoice_no'] ?? '#' . $order['id'] }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $order['table'] ? '🪑 ' . $order['table']['name'] : '🛍️ ' . ucfirst(str_replace('_', ' ', $order['order_type'])) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold px-2 py-1 rounded-full
                                    {{ $status === 'pending' ? 'bg-warning-200 text-warning-800' : ($status === 'cooking' ? 'bg-primary-200 text-primary-800' : 'bg-success-200 text-success-800') }}">
                                    {{ strtoupper($status) }}
                                </span>
                                <p class="text-xs text-gray-400 mt-1 {{ $urgent ? 'text-danger-500 font-bold' : '' }}">
                                    ⏱ {{ $elapsed }}m ago
                                </p>
                            </div>
                        </div>

                        {{-- Items --}}
                        <div class="px-4 py-3 space-y-2">
                            @foreach($order['items'] as $item)
                                <div class="flex items-start justify-between text-sm">
                                    <div>
                                        <span class="font-semibold">{{ $item['quantity'] }}×</span>
                                        {{ $item['product']['name'] ?? 'Item' }}
                                        @if($item['variant'])
                                            <span class="text-xs text-gray-400">({{ $item['variant']['name'] }})</span>
                                        @endif
                                        @if($item['notes'])
                                            <p class="text-xs text-orange-500 italic">📝 {{ $item['notes'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Actions --}}
                        <div class="px-4 py-3 border-t border-opacity-20 flex gap-2">
                            @if($status === 'pending')
                                <button wire:click="updateStatus({{ $order['id'] }}, 'cooking')"
                                    class="flex-1 rounded-lg bg-primary-600 text-white text-xs font-bold py-2 hover:bg-primary-700 transition">
                                    🔥 Start Cooking
                                </button>
                            @elseif($status === 'cooking')
                                <button wire:click="updateStatus({{ $order['id'] }}, 'ready')"
                                    class="flex-1 rounded-lg bg-success-600 text-white text-xs font-bold py-2 hover:bg-success-700 transition">
                                    ✅ Mark Ready
                                </button>
                            @elseif($status === 'ready')
                                <button wire:click="updateStatus({{ $order['id'] }}, 'served')"
                                    class="flex-1 rounded-lg bg-gray-600 text-white text-xs font-bold py-2 hover:bg-gray-700 transition">
                                    🍽️ Mark Served
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Auto-refresh every 30 seconds --}}
    <script>
        setInterval(() => {
            @this.loadOrders();
        }, 30000);
    </script>
</x-filament-panels::page>
