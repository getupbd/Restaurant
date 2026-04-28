<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Today's Stats --}}
        <div class="grid grid-cols-4 gap-4">
            <div class="rounded-xl border bg-white dark:bg-gray-800 p-4 text-center shadow-sm">
                <p class="text-3xl font-black text-gray-800 dark:text-white">{{ $todayStats['total'] }}</p>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mt-1">Today's Total</p>
            </div>
            <div class="rounded-xl border border-warning-200 bg-warning-50 dark:bg-warning-900/20 p-4 text-center shadow-sm">
                <p class="text-3xl font-black text-warning-600">{{ $todayStats['pending'] }}</p>
                <p class="text-xs font-semibold text-warning-600 uppercase tracking-widest mt-1">⏳ Pending</p>
            </div>
            <div class="rounded-xl border border-success-200 bg-success-50 dark:bg-success-900/20 p-4 text-center shadow-sm">
                <p class="text-3xl font-black text-success-600">{{ $todayStats['confirmed'] }}</p>
                <p class="text-xs font-semibold text-success-600 uppercase tracking-widest mt-1">✅ Confirmed</p>
            </div>
            <div class="rounded-xl border border-primary-200 bg-primary-50 dark:bg-primary-900/20 p-4 text-center shadow-sm">
                <p class="text-3xl font-black text-primary-600">{{ $todayStats['seated'] }}</p>
                <p class="text-xs font-semibold text-primary-600 uppercase tracking-widest mt-1">🪑 Seated</p>
            </div>
        </div>

        {{-- Calendar Header --}}
        <div class="rounded-xl border bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
                <button wire:click="previousMonth"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                </h2>
                <button wire:click="nextMonth"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- Day headers --}}
            <div class="grid grid-cols-7 border-b dark:border-gray-700">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                    <div class="py-2 text-center text-xs font-bold text-gray-500 uppercase">{{ $day }}</div>
                @endforeach
            </div>

            {{-- Calendar grid --}}
            <div class="grid grid-cols-7">
                @foreach($calendarDays as $day)
                    <div class="min-h-[90px] border-b border-r dark:border-gray-700 p-2
                        {{ !$day['is_current_month'] ? 'bg-gray-50 dark:bg-gray-900/30' : 'bg-white dark:bg-gray-800' }}
                        {{ $day['is_today'] ? 'ring-2 ring-inset ring-primary-500' : '' }}
                        {{ $day['is_blocked'] ? 'bg-red-50 dark:bg-red-900/10' : '' }}">

                        <p class="text-sm font-semibold mb-1
                            {{ $day['is_today'] ? 'text-primary-600' : ($day['is_current_month'] ? 'text-gray-700 dark:text-gray-200' : 'text-gray-300 dark:text-gray-600') }}">
                            {{ $day['day'] }}
                        </p>

                        @if($day['is_blocked'])
                            <span class="inline-block text-xs bg-red-100 text-red-600 rounded px-1 py-0.5 truncate w-full" title="{{ $day['block_reason'] }}">
                                🚫 {{ $day['block_reason'] ?? 'Closed' }}
                            </span>
                        @elseif($day['reservation_count'] > 0)
                            <div class="space-y-0.5">
                                @if($day['pending_count'] > 0)
                                    <span class="inline-block text-xs bg-warning-100 text-warning-700 rounded px-1">
                                        ⏳ {{ $day['pending_count'] }} pending
                                    </span>
                                @endif
                                @if($day['confirmed_count'] > 0)
                                    <span class="inline-block text-xs bg-success-100 text-success-700 rounded px-1">
                                        ✅ {{ $day['confirmed_count'] }} confirmed
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex gap-4 text-sm text-gray-500">
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded bg-warning-200"></span> Pending</span>
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded bg-success-200"></span> Confirmed</span>
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded bg-red-200"></span> Closed</span>
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded ring-2 ring-primary-400"></span> Today</span>
        </div>
    </div>
</x-filament-panels::page>
