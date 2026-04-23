<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 style="font-size: 1.875rem; letter-spacing: -0.025em;" class="font-black text-gray-900 dark:text-white">Subscription Status</h2>
                    <div class="mt-2 flex items-center gap-2">
                        @php
                            $isSubscribed = tenant()->isSubscribed();
                        @endphp
                        
                        @if($isSubscribed)
                            <div class="px-3 py-1 bg-success-500/10 text-success-600 rounded-full text-sm font-bold flex items-center gap-1">
                                <x-heroicon-s-check-circle class="w-4 h-4" />
                                Active
                            </div>
                            <p class="text-sm text-gray-500">Your trial/subscription ends in {{ tenant()->trial_ends_at->diffForHumans() }}</p>
                        @else
                            <div class="px-3 py-1 bg-danger-500/10 text-danger-600 rounded-full text-sm font-bold flex items-center gap-1">
                                <x-heroicon-s-x-circle class="w-4 h-4" />
                                Subscription Expired
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Please pay any outstanding invoices to restore access to your platform.</p>
                        @endif
                    </div>
                </div>
                <div class="bg-primary-500/10 p-5 rounded-2xl shadow-inner">
                    <x-heroicon-o-credit-card class="w-12 h-12 text-primary-600" />
                </div>
            </div>
        </div>

        <!-- Invoices Section -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 px-2">Pending Invoices</h3>
            <div class="grid gap-4">
                @forelse($invoices as $invoice)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-800 flex items-center justify-between hover:border-primary-500/50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-500 transition-colors">
                                <x-heroicon-o-document-text class="w-6 h-6" />
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">Invoice #{{ $invoice->id }}</h4>
                                <p class="text-xs text-gray-500">Due Date: {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="font-black text-xl text-gray-900 dark:text-white">${{ number_format($invoice->amount, 2) }}</p>
                                <span class="text-xs font-bold uppercase tracking-wider {{ $invoice->status === 'paid' ? 'text-success-500' : 'text-danger-500' }}">
                                    {{ $invoice->status }}
                                </span>
                            </div>

                            @if($invoice->status !== 'paid')
                                <x-filament::button
                                    wire:click="payMock({{ $invoice->id }})"
                                    wire:loading.attr="disabled"
                                    color="primary"
                                    size="lg"
                                    class="rounded-xl"
                                >
                                    Pay Now (Mock)
                                </x-filament::button>
                            @else
                                <div class="px-4 py-2 border dark:border-gray-700 rounded-xl text-gray-400 font-bold text-sm">
                                    PAID
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-20 flex flex-col items-center justify-center text-gray-400 bg-gray-50/50 dark:bg-gray-800/20 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                        <x-heroicon-o-receipt-refund class="opacity-20 mb-3" style="width: 3rem; height: 3rem;" />
                        <p class="text-sm">No pending invoices found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
