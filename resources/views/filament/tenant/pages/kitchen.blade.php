<x-filament-panels::page>

<style>
/* Hide page title header on this page */
.fi-header { display: none !important; }

/* KDS Light Theme */
.kds-root {
    min-height: calc(100vh - 60px);
    background: transparent;
    padding: 0;
    margin: 0;
    font-family: 'Outfit', sans-serif;
}

/* Stats Bar */
.kds-stats-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.kds-stat-chip {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.8rem;
    font-weight: 700;
}
.kds-stat-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.kds-stat-val {
    font-size: 1.25rem;
    font-weight: 900;
    line-height: 1;
}
.kds-live {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
}
.kds-live-dot {
    width: 8px; height: 8px;
    background: #22c55e;
    border-radius: 50%;
    animation: blink 1.5s ease-in-out infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

/* Columns */
.kds-columns {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    align-items: start;
}
.kds-col-header {
    border-radius: 10px;
    padding: 0.6rem 1rem;
    margin-bottom: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid;
}
.kds-col-title {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}
.kds-count-badge {
    min-width: 24px;
    height: 24px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 900;
    padding: 0 6px;
}

/* Cards */
.kds-card {
    border-radius: 14px;
    border: 1px solid;
    overflow: hidden;
    margin-bottom: 1rem;
    transition: transform 0.2s, box-shadow 0.2s;
    background: #ffffff;
}
.kds-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.10);
}
.kds-card-header {
    padding: 0.875rem 1.1rem;
    border-bottom: 1px solid;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.kds-order-num {
    font-size: 1.5rem;
    font-weight: 900;
    letter-spacing: -0.04em;
    line-height: 1;
}
.kds-table-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.kds-timer {
    text-align: right;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 10px;
    min-width: 56px;
}
.kds-timer-val {
    font-size: 1.1rem;
    font-weight: 900;
    line-height: 1;
}
.kds-timer-label {
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #94a3b8;
    margin-top: 2px;
}

/* Items */
.kds-items {
    padding: 0.75rem 1.1rem;
    background: #ffffff;
}
.kds-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.kds-item:last-child { border-bottom: none; }
.kds-qty {
    width: 30px; height: 30px;
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem; font-weight: 900;
    flex-shrink: 0;
}
.kds-item-name {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kds-notes-bar {
    margin: 0 1.1rem 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    border-left: 3px solid #f59e0b;
    background: #fffbeb;
    font-size: 0.75rem;
    color: #92400e;
    font-style: italic;
}

/* Action Button */
.kds-action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.8rem;
    border: none;
    border-radius: 0 0 13px 13px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    cursor: pointer;
    transition: filter 0.15s, transform 0.15s;
}
.kds-action-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
.kds-action-btn:active { transform: translateY(0); }

/* Urgency animation */
@keyframes urgency-pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
    50%      { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
}
.kds-urgent { animation: urgency-pulse 1.8s infinite; }

/* Empty */
.kds-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    min-height: 200px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    color: #94a3b8;
    font-size: 0.8rem;
    font-weight: 600;
    gap: 0.5rem;
    text-align: center;
}

.noscrollbar::-webkit-scrollbar { display: none; }
.noscrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@php
    $orders   = $this->getOrders();
    $pending  = $orders->where('status', 'pending');
    $cooking  = $orders->where('status', 'cooking');
    $ready    = $orders->where('status', 'ready');
@endphp

<div class="kds-root" wire:poll.8s>

    {{-- ── Stats Bar ─────────────────────────────────────────── --}}
    <div class="kds-stats-bar">
        <div style="display:flex; gap:1.5rem; align-items:center;">
            <div class="kds-stat-chip">
                <div class="kds-stat-dot" style="background:#ef4444;"></div>
                <div>
                    <div class="kds-stat-val" style="color:#ef4444;">{{ $pending->count() }}</div>
                    <div style="font-size:.65rem; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">New</div>
                </div>
            </div>
            <div style="width:1px; height:28px; background:#e2e8f0;"></div>
            <div class="kds-stat-chip">
                <div class="kds-stat-dot" style="background:#f59e0b;"></div>
                <div>
                    <div class="kds-stat-val" style="color:#f59e0b;">{{ $cooking->count() }}</div>
                    <div style="font-size:.65rem; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">Cooking</div>
                </div>
            </div>
            <div style="width:1px; height:28px; background:#e2e8f0;"></div>
            <div class="kds-stat-chip">
                <div class="kds-stat-dot" style="background:#22c55e;"></div>
                <div>
                    <div class="kds-stat-val" style="color:#22c55e;">{{ $ready->count() }}</div>
                    <div style="font-size:.65rem; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">Ready</div>
                </div>
            </div>
            <div style="width:1px; height:28px; background:#e2e8f0;"></div>
            <div class="kds-stat-chip">
                <div class="kds-stat-dot" style="background:#6366f1;"></div>
                <div>
                    <div class="kds-stat-val" style="color:#1e293b;">{{ $orders->count() }}</div>
                    <div style="font-size:.65rem; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">Total</div>
                </div>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:1.5rem;">
            <div class="kds-live">
                <div class="kds-live-dot"></div>
                Auto-refresh 8s
            </div>
            <div style="font-size:1.1rem; font-weight:800; color:#94a3b8; font-variant-numeric:tabular-nums;"
                 x-data="{t:''}" x-init="setInterval(()=>t=new Date().toLocaleTimeString(),1000)" x-text="t">
            </div>
        </div>
    </div>

    {{-- ── Three Columns ──────────────────────────────────────── --}}
    <div class="kds-columns">

        {{-- PENDING column --}}
        <div>
            <div class="kds-col-header" style="background:#fef2f2; border-color:#fecaca;">
                <div class="kds-col-title" style="color:#dc2626;">⏱ New Orders</div>
                <div class="kds-count-badge" style="background:#ef4444; color:#fff;">{{ $pending->count() }}</div>
            </div>
            @forelse($pending as $order)
                @php $isUrgent = $order->elapsed_minutes >= 15; @endphp
                <div class="kds-card {{ $isUrgent ? 'kds-urgent' : '' }}"
                     style="border-color:{{ $isUrgent ? '#ef4444' : '#fecaca' }};"
                     x-data="{show:false}" x-init="setTimeout(()=>show=true,50)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    <div class="kds-card-header" style="background:#fef2f2; border-color:#fecaca;">
                        <div>
                            <div class="kds-order-num" style="color:#dc2626;">#{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }}</div>
                            <div class="kds-table-label" style="color:#ef4444;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                                {{ $order->table?->name ?? 'Walk-in' }}
                            </div>
                        </div>
                        <div class="kds-timer">
                            <div class="kds-timer-val" style="color:{{ $isUrgent ? '#dc2626' : '#1e293b' }};">{{ $order->elapsed }}</div>
                        </div>
                    </div>

                    <div class="kds-items">
                        @foreach($order->items as $item)
                        <div class="kds-item">
                            <div class="kds-qty" style="background:#fee2e2; color:#dc2626;">{{ (int)$item->quantity }}</div>
                            <div class="kds-item-name" style="color:#1e293b;">{{ $item->product?->name }}</div>
                        </div>
                        @endforeach
                    </div>
                    @if($order->notes)
                    <div class="kds-notes-bar">📝 {{ $order->notes }}</div>
                    @endif

                    <button wire:click="updateStatus({{ $order->id }}, 'cooking')"
                            class="kds-action-btn"
                            style="background:#d97706; color:#fff;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.657 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                        🔥 Start Cooking
                    </button>
                </div>
            @empty
                <div class="kds-empty">🎉<br>No new orders</div>
            @endforelse
        </div>

        {{-- COOKING column --}}
        <div>
            <div class="kds-col-header" style="background:#fffbeb; border-color:#fde68a;">
                <div class="kds-col-title" style="color:#d97706;">👨‍🍳 In Kitchen</div>
                <div class="kds-count-badge" style="background:#f59e0b; color:#fff;">{{ $cooking->count() }}</div>
            </div>
            @forelse($cooking as $order)
                <div class="kds-card" style="border-color:#fde68a;"
                     x-data="{show:false}" x-init="setTimeout(()=>show=true,50)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    <div class="kds-card-header" style="background:#fffbeb; border-color:#fde68a;">
                        <div>
                            <div class="kds-order-num" style="color:#d97706;">#{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }}</div>
                            <div class="kds-table-label" style="color:#f59e0b;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                                {{ $order->table?->name ?? 'Walk-in' }}
                            </div>
                        </div>
                        <div class="kds-timer">
                            <div class="kds-timer-val" style="color:#d97706;">{{ $order->elapsed }}</div>
                        </div>
                    </div>

                    <div class="kds-items">
                        @foreach($order->items as $item)
                        <div class="kds-item">
                            <div class="kds-qty" style="background:#fef3c7; color:#d97706;">{{ (int)$item->quantity }}</div>
                            <div class="kds-item-name" style="color:#1e293b;">{{ $item->product?->name }}</div>
                        </div>
                        @endforeach
                    </div>
                    @if($order->notes)
                    <div class="kds-notes-bar">📝 {{ $order->notes }}</div>
                    @endif

                    <button wire:click="updateStatus({{ $order->id }}, 'ready')"
                            class="kds-action-btn"
                            style="background:#16a34a; color:#fff;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        ✅ Mark Ready
                    </button>
                </div>
            @empty
                <div class="kds-empty">🍳<br>Nothing cooking</div>
            @endforelse
        </div>

        {{-- READY column --}}
        <div>
            <div class="kds-col-header" style="background:#f0fdf4; border-color:#bbf7d0;">
                <div class="kds-col-title" style="color:#16a34a;">✅ Ready to Serve</div>
                <div class="kds-count-badge" style="background:#22c55e; color:#fff;">{{ $ready->count() }}</div>
            </div>
            @forelse($ready as $order)
                <div class="kds-card" style="border-color:#bbf7d0;"
                     x-data="{show:false}" x-init="setTimeout(()=>show=true,50)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    <div class="kds-card-header" style="background:#f0fdf4; border-color:#bbf7d0;">
                        <div>
                            <div class="kds-order-num" style="color:#16a34a;">#{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }}</div>
                            <div class="kds-table-label" style="color:#22c55e;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                                {{ $order->table?->name ?? 'Walk-in' }}
                            </div>
                        </div>
                        <div class="kds-timer">
                            <div class="kds-timer-val" style="color:#16a34a;">{{ $order->elapsed }}</div>
                        </div>
                    </div>

                    <div class="kds-items">
                        @foreach($order->items as $item)
                        <div class="kds-item">
                            <div class="kds-qty" style="background:#dcfce7; color:#16a34a;">{{ (int)$item->quantity }}</div>
                            <div class="kds-item-name" style="color:#1e293b;">{{ $item->product?->name }}</div>
                        </div>
                        @endforeach
                    </div>
                    @if($order->notes)
                    <div class="kds-notes-bar">📝 {{ $order->notes }}</div>
                    @endif

                    <button wire:click="updateStatus({{ $order->id }}, 'served')"
                            class="kds-action-btn"
                            style="background:#f1f5f9; color:#64748b;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Bump / Served
                    </button>
                </div>
            @empty
                <div class="kds-empty">🏁<br>Nothing ready yet</div>
            @endforelse
        </div>

    </div>{{-- end kds-columns --}}

    {{-- All clear --}}
    @if($orders->isEmpty())
    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center;
                min-height:50vh; text-align:center; gap:1rem;">
        <div style="font-size:4rem;">🍽️</div>
        <h2 style="color:#1e293b; font-size:1.75rem; font-weight:900; margin:0;">Kitchen is Clear!</h2>
        <p style="color:#64748b; font-size:1rem; margin:0;">No active orders. Enjoy the calm before the storm!</p>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let lastCount = {{ $orders->count() }};
    document.addEventListener('livewire:update', () => {
        const current = document.querySelectorAll('.kds-card').length;
        if (current > lastCount) {
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            audio.volume = 0.5;
            audio.play().catch(() => {});
        }
        lastCount = current;
    });
});
</script>

</x-filament-panels::page>
