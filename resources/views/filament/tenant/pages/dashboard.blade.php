<x-filament-panels::page>

<style>
.dash-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 3px 0 rgb(0 0 0/.06);
    padding: 1.25rem;
    transition: box-shadow .2s;
}
.dash-card:hover { box-shadow: 0 4px 12px 0 rgb(0 0 0/.08); }
.dark .dash-card { background: #1e293b; border-color: rgba(255,255,255,.08); }

.stat-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-value { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
.stat-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }

.badge {
    display: inline-flex; align-items: center; padding: 2px 8px;
    border-radius: 999px; font-size: .7rem; font-weight: 600;
}

.bar-track { background: #f1f5f9; border-radius: 8px; height: 6px; flex: 1; }
.dark .bar-track { background: rgba(255,255,255,.07); }
.bar-fill { border-radius: 8px; height: 6px; transition: width .5s; }
</style>

<div class="space-y-5">

    {{-- Top Row: Stats --}}
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">

        {{-- Today Revenue --}}
        <div class="dash-card flex items-center gap-4">
            <div class="stat-icon" style="background:#eef2ff">
                <svg class="w-5 h-5" style="color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="stat-label text-gray-400">Today Revenue</div>
                <div class="stat-value text-gray-900 dark:text-white">৳{{ number_format($todaySales, 0) }}</div>
                <div class="text-xs text-gray-400 mt-0.5">Month: ৳{{ number_format($monthSales, 0) }}</div>
            </div>
        </div>

        {{-- Today Orders --}}
        <div class="dash-card flex items-center gap-4">
            <div class="stat-icon" style="background:#fefce8">
                <svg class="w-5 h-5" style="color:#eab308" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="stat-label text-gray-400">Today Orders</div>
                <div class="stat-value text-gray-900 dark:text-white">{{ $todayOrders }}</div>
                <div class="text-xs text-gray-400 mt-0.5">Total placed today</div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="dash-card flex items-center gap-4">
            <div class="stat-icon" style="background:#fef2f2">
                <svg class="w-5 h-5" style="color:#ef4444" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="stat-label text-gray-400">Pending</div>
                <div class="stat-value text-gray-900 dark:text-white">{{ $pendingOrders }}</div>
                <div class="text-xs {{ $pendingOrders > 0 ? 'text-red-400' : 'text-gray-400' }} mt-0.5">
                    {{ $pendingOrders > 0 ? 'Needs attention' : 'All clear' }}
                </div>
            </div>
        </div>

        {{-- Active Menu --}}
        <div class="dash-card flex items-center gap-4">
            <div class="stat-icon" style="background:#f0fdf4">
                <svg class="w-5 h-5" style="color:#22c55e" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="min-w-0">
                <div class="stat-label text-gray-400">Active Menu</div>
                <div class="stat-value text-gray-900 dark:text-white">{{ $activeMenus }}</div>
                <div class="text-xs text-gray-400 mt-0.5">Items available</div>
            </div>
        </div>
    </div>

    {{-- Middle Row: Chart + Top Products --}}
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">

        {{-- Weekly Bar Chart --}}
        <div class="dash-card">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white">Revenue Overview</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Last 7 days performance</p>
                </div>
                <span class="badge" style="background:#eef2ff; color:#6366f1">Weekly</span>
            </div>
            @php $maxRev = $weeklySales->max('revenue') ?: 1; @endphp
            <div class="flex items-end gap-3" style="height: 140px;">
                @foreach($weeklySales as $day)
                @php
                    $isToday = $day['date'] === now()->format('M d');
                    $heightPct = max(8, round(($day['revenue'] / $maxRev) * 120));
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1.5">
                    @if($day['revenue'] > 0)
                    <span class="text-xs font-semibold" style="color:#6366f1; font-size: .65rem;">
                        ৳{{ number_format($day['revenue'], 0) }}
                    </span>
                    @else
                    <span class="text-xs text-gray-200">—</span>
                    @endif
                    <div class="w-full rounded-t-lg"
                         style="height: {{ $heightPct }}px;
                                background: {{ $isToday ? 'linear-gradient(180deg,#818cf8,#6366f1)' : '#e0e7ff' }};
                                min-height: 8px;">
                    </div>
                    <div class="text-center">
                        <div class="font-semibold" style="font-size:.65rem; color: {{ $isToday ? '#6366f1' : '#94a3b8' }};">{{ $day['day'] }}</div>
                        <div style="font-size:.6rem; color:#cbd5e1;">{{ $day['orders'] }}x</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top Selling --}}
        <div class="dash-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">🔥 Top Items</h3>
                <span class="text-xs text-gray-400">All time</span>
            </div>
            <div class="space-y-4">
                @php $maxQty = $topProducts->max('total_qty') ?: 1; @endphp
                @forelse($topProducts as $i => $item)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-xs font-bold w-4 text-center" style="color: {{ ['#f59e0b','#94a3b8','#cd7c2f'][$i] ?? '#94a3b8' }}">
                                {{ $i + 1 }}
                            </span>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                {{ $item->product?->name ?? 'Unknown' }}
                            </span>
                        </div>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 ml-2 shrink-0">
                            {{ $item->total_qty }}x
                        </span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:{{ round(($item->total_qty/$maxQty)*100) }}%; background: {{ ['#f59e0b','#94a3b8','#cd7c2f','#6366f1','#22c55e'][$i] ?? '#6366f1' }};"></div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-6">No sales data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom: Recent Orders --}}
    <div class="dash-card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Recent Orders</h3>
                <p class="text-xs text-gray-400 mt-0.5">Latest 8 transactions</p>
            </div>
            <a href="{{ \App\Filament\Tenant\Resources\OrderResource::getUrl('index') }}"
               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                View all →
            </a>
        </div>
        <div class="overflow-x-auto -mx-1">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <th class="pb-2 text-left" style="font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; padding-left:4px">#</th>
                        <th class="pb-2 text-left" style="font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;">Table</th>
                        <th class="pb-2 text-left" style="font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;">Items</th>
                        <th class="pb-2 text-left" style="font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;">Total</th>
                        <th class="pb-2 text-left" style="font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;">Payment</th>
                        <th class="pb-2 text-left" style="font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;">Status</th>
                        <th class="pb-2 text-left" style="font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr style="border-bottom: 1px solid #f8fafc;" class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <td class="py-2.5" style="padding-left:4px">
                            <span class="font-bold text-xs text-gray-600 dark:text-gray-300">
                                #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="py-2.5">
                            <span class="badge" style="background:#eef2ff; color:#6366f1;">
                                {{ $order->table?->name ?? 'Takeaway' }}
                            </span>
                        </td>
                        <td class="py-2.5 text-xs text-gray-500">{{ $order->items->count() }} items</td>
                        <td class="py-2.5">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200">৳{{ number_format($order->total_price, 0) }}</span>
                        </td>
                        <td class="py-2.5">
                            @php
                                $pColor = match($order->payment_status) {
                                    'paid' => ['bg'=>'#f0fdf4','c'=>'#16a34a'],
                                    'pending' => ['bg'=>'#fefce8','c'=>'#ca8a04'],
                                    default => ['bg'=>'#fef2f2','c'=>'#dc2626'],
                                };
                            @endphp
                            <span class="badge" style="background:{{ $pColor['bg'] }}; color:{{ $pColor['c'] }};">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="py-2.5">
                            @php
                                $sColor = match($order->status) {
                                    'completed' => ['bg'=>'#f0fdf4','c'=>'#16a34a'],
                                    'pending' => ['bg'=>'#fefce8','c'=>'#ca8a04'],
                                    'cancelled' => ['bg'=>'#fef2f2','c'=>'#dc2626'],
                                    'preparing' => ['bg'=>'#eff6ff','c'=>'#2563eb'],
                                    default => ['bg'=>'#f8fafc','c'=>'#64748b'],
                                };
                            @endphp
                            <span class="badge" style="background:{{ $sColor['bg'] }}; color:{{ $sColor['c'] }};">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="py-2.5 text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-xs text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                No orders yet
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</x-filament-panels::page>
