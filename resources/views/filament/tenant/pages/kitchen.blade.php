<x-filament-panels::page>

    <style>
        .noscrollbar::-webkit-scrollbar { display: none; }
        .noscrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .kds-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .kds-card:hover { transform: translateY(-4px); }

        /* Urgency pulse for orders > 15 min */
        @keyframes urgency-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        }
        .urgent { animation: urgency-pulse 2s infinite; border-color: #ef4444 !important; }

        .kds-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .kds-badge-pending { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .kds-badge-cooking { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .kds-badge-ready   { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

        .kds-item-row {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: background 0.2s;
        }
        .kds-item-row:hover {
            background: #f1f5f9;
        }
    </style>

    <div wire:poll.8s id="kds-board"
         style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">

        @forelse($this->getOrders() as $order)
            @php
                $isUrgent  = $order->elapsed_minutes >= 15 && $order->status === 'pending';
                $isPending = $order->status === 'pending';
                $isCooking = $order->status === 'cooking';
                $isReady   = $order->status === 'ready';

                // Header color per status - subtle light backgrounds
                $headerColor = $isPending ? '#fef2f2' : ($isCooking ? '#fffbeb' : '#f0fdf4');
                $accentColor = $isPending ? '#dc2626' : ($isCooking ? '#d97706' : '#16a34a');
                $tableLabel = $order->table?->name ?? 'Walk-in';
            @endphp

            <div class="kds-card {{ $isUrgent ? 'urgent' : '' }}"
                 x-data="{ show: false }"
                 x-init="setTimeout(() => show = true, 50)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-400"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 style="background: #ffffff; border-radius: 1.25rem; overflow: hidden;
                        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05), 0 2px 10px -3px rgba(0,0,0,0.03);
                        border: 1px solid #e2e8f0;
                        display: flex; flex-direction: column; min-height: 380px;">

                {{-- ── HEADER ──────────────────────────────────────────── --}}
                <div style="background: {{ $headerColor }}; padding: 1.25rem; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.4rem;">
                            <span style="font-size:1.85rem; font-weight:900; letter-spacing:-0.05em; line-height:1; color: #1e293b;">
                                #{{ $order->id }}
                            </span>
                            <span class="kds-badge kds-badge-{{ $order->status }}">
                                @if($isPending) ⏱️ @elseif($isCooking) 👨‍🍳 @else ✅ @endif
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div style="font-size:0.75rem; font-weight:600; color: #64748b; text-transform:uppercase; letter-spacing:0.05em; display: flex; align-items: center; gap: 4px;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $tableLabel }}
                        </div>
                    </div>

                    <div style="text-align:right; background:#ffffff; border: 1px solid #e2e8f0; border-radius:0.75rem; padding:0.5rem 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <div style="font-size:0.65rem; font-weight:700; color: #94a3b8; text-transform:uppercase; letter-spacing:0.05em;">Elapsed</div>
                        <div style="font-size:1.1rem; font-weight:800; color: {{ $isUrgent ? '#dc2626' : '#1e293b' }};">
                            {{ $order->elapsed }}
                        </div>
                    </div>
                </div>

                {{-- ── ITEMS ───────────────────────────────────────────── --}}
                <div class="noscrollbar"
                     style="flex:1; overflow-y:auto; padding:1.25rem;">
                    <ul style="display:flex; flex-direction:column; gap:0.75rem; margin:0; padding:0; list-style:none;">
                        @foreach($order->items as $item)
                            <li class="kds-item-row" style="display:flex; align-items:center; gap:0.85rem;
                                       border-radius:0.75rem; padding:0.75rem 1rem;">
                                <span style="width:32px; height:32px; border-radius:8px;
                                             background: {{ $accentColor }};
                                             display:flex; align-items:center; justify-content:center;
                                             font-size:0.9rem; font-weight:800; color:#fff; flex-shrink:0;">
                                    {{ (int)$item->quantity }}
                                </span>
                                <div style="flex:1; min-width:0;">
                                    <p style="margin:0; font-weight:600; font-size:0.95rem; color:#334155;
                                              white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        {{ $item->product?->name }}
                                    </p>
                                    @if($item->notes)
                                        <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#64748b; font-style:italic;">
                                            {{ $item->notes }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    @if($order->notes)
                        <div style="margin-top:1rem; padding:0.75rem 1rem; background: #fffbeb;
                                    border-left:4px solid #f59e0b; border-radius: 4px; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">
                            <p style="margin:0; font-size:0.8rem; color:#92400e; font-weight: 500;">
                                📝 <span style="margin-left: 4px;">{{ $order->notes }}</span>
                            </p>
                        </div>
                    @endif
                </div>

                {{-- ── ACTION BUTTON ───────────────────────────────────── --}}
                <div style="padding:1rem 1.25rem; border-top:1px solid #f1f5f9; background:#fafafa;">
                    @if($isPending)
                        <button wire:click="updateStatus({{ $order->id }}, 'cooking')"
                                style="width:100%; padding:0.85rem 1rem;
                                       background:#d97706;
                                       color:#fff; font-weight:700; font-size:0.8rem;
                                       text-transform:uppercase; letter-spacing:0.05em;
                                       border:none; border-radius:0.75rem; cursor:pointer;
                                       display:flex; align-items:center; justify-content:center; gap:0.6rem;
                                       transition: all 0.2s; box-shadow:0 4px 12px rgba(217,119,6,0.2);"
                                onmouseover="this.style.background='#b45309'; this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.background='#d97706'; this.style.transform='translateY(0)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.657 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14l-0.655 0.655a3 3 0 01-0.466 0.466z"></path></svg>
                            Fire Order
                        </button>
                    @elseif($isCooking)
                        <button wire:click="updateStatus({{ $order->id }}, 'ready')"
                                style="width:100%; padding:0.85rem 1rem;
                                       background:#16a34a;
                                       color:#fff; font-weight:700; font-size:0.8rem;
                                       text-transform:uppercase; letter-spacing:0.05em;
                                       border:none; border-radius:0.75rem; cursor:pointer;
                                       display:flex; align-items:center; justify-content:center; gap:0.6rem;
                                       transition: all 0.2s; box-shadow:0 4px 12px rgba(22,163,74,0.2);"
                                onmouseover="this.style.background='#15803d'; this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.background='#16a34a'; this.style.transform='translateY(0)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Plated &amp; Ready
                        </button>
                    @else
                        <button wire:click="updateStatus({{ $order->id }}, 'served')"
                                style="width:100%; padding:0.85rem 1rem;
                                       background:#f8fafc;
                                       color:#64748b; font-weight:700; font-size:0.8rem;
                                       text-transform:uppercase; letter-spacing:0.05em;
                                       border:1px solid #e2e8f0; border-radius:0.75rem; cursor:pointer;
                                       display:flex; align-items:center; justify-content:center; gap:0.6rem;
                                       transition: all 0.2s;"
                                onmouseover="this.style.background='#f1f5f9'; this.style.color='#475569'"
                                onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Bump Order
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; min-height:400px; display:flex; flex-direction:column;
                         align-items:center; justify-content:center; text-align:center;
                         background:#ffffff; border:1px dashed #cbd5e1;
                         border-radius:1.5rem; padding:3rem;">
                <div style="width:80px; height:80px; background:#f1f5f9;
                             border-radius:50%; display:flex; align-items:center; justify-content:center;
                             font-size:2.5rem; margin-bottom:1.5rem;">
                    🍽️
                </div>
                <h3 style="color:#1e293b; font-size:1.5rem; font-weight:800; margin:0 0 0.5rem;">
                    Kitchen is Clear!
                </h3>
                <p style="color:#64748b; font-size:1rem; margin:0; max-width:320px; line-height:1.6;">
                    No active orders at the moment. Take a short break!
                </p>
            </div>
        @endforelse
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            let lastCount = {{ count($this->getOrders()) }};
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            setInterval(() => {
                const currentCount = document.querySelectorAll('#kds-board > div:not([style*="grid-column"])').length;
                if (currentCount > lastCount) {
                    audio.play().catch(() => {});
                }
                lastCount = currentCount;
            }, 8000);
        });
    </script>

</x-filament-panels::page>
