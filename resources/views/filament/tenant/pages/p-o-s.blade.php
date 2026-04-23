<x-filament-panels::page>
    <div style="display: flex; gap: 1rem; height: calc(100vh - 12rem);">
        <!-- Left Side: Products Grid -->
        <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 1rem; overflow: hidden;">
            <!-- Search and Categories -->
            <div class="flex gap-4 items-center bg-white dark:bg-gray-900 p-4 rounded-xl shadow-sm">
                <div class="flex-1">
                    <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                        <x-filament::input
                            type="text"
                            placeholder="Search products..."
                            wire:model.live="search"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div style="flex: 1; display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;" class="noscrollbar">
                    <button
                        wire:click="$set('selectedCategoryId', null)"
                        style="padding: 0.5rem 1.25rem; border-radius: 9999px; font-weight: 700; transition: all 0.3s; border: none; cursor: pointer; white-space: nowrap; font-size: 0.875rem; 
                        {{ $selectedCategoryId === null ? 'background: #4F46E5; color: white; shadow: 0 4px 6px -1px rgb(79 70 229 / 0.1);' : 'background: #F3F4F6; color: #4B5563;' }}"
                    >
                        All
                    </button>
                    @foreach($this->categories as $category)
                        <button
                            wire:click="$set('selectedCategoryId', {{ $category->id }})"
                            style="padding: 0.5rem 1.25rem; border-radius: 9999px; font-weight: 700; transition: all 0.3s; border: none; cursor: pointer; white-space: nowrap; font-size: 0.875rem;
                            {{ $selectedCategoryId === $category->id ? 'background: #4F46E5; color: white; shadow: 0 4px 6px -1px rgb(79 70 229 / 0.1);' : 'background: #F3F4F6; color: #4B5563;' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products -->
            <div class="flex-1 overflow-y-auto p-1 noscrollbar" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.25rem;">
                @foreach($this->products as $product)
                    @php
                        $imageSrc = $product->image;
                        if ($imageSrc && !\Illuminate\Support\Str::startsWith($imageSrc, ['http://', 'https://', 'data:'])) {
                            $imageSrc = asset('storage/' . $imageSrc);
                        }
                    @endphp
                    <div 
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all cursor-pointer border-2 border-transparent active:border-primary-500 overflow-hidden group"
                    >
                        <div style="aspect-ratio: 1;" class="bg-gray-100 dark:bg-gray-700 rounded-2xl mb-3 overflow-hidden">
                            @if($imageSrc)
                                <img src="{{ $imageSrc }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <x-heroicon-o-camera class="w-10 h-10" />
                                </div>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 line-clamp-1">{{ $product->name }}</h3>
                        <p class="text-primary-600 font-bold mt-1">${{ number_format($product->price, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: Cart & Checkout -->
        <div style="width: 28rem; backdrop-filter: blur(16px); background: rgba(255, 255, 255, 0.9);" class="shrink-0 dark:bg-gray-900/90 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-s-shopping-bag class="w-6 h-6 text-primary-600" />
                    Current Order
                </h2>
                <div class="mt-4">
                    <x-filament::input.wrapper label="Table">
                        <select wire:model="tableId" class="block w-full border-none bg-transparent focus:ring-0 text-sm">
                            <option value="">Select Table / Takeout</option>
                            @foreach($this->tables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->area->name }})</option>
                            @endforeach
                        </select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-3 noscrollbar">
                @forelse($cart as $id => $item)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 group">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $item['name'] }}</h4>
                            <p class="text-xs text-gray-500">${{ number_format($item['price'], 2) }} / unit</p>
                        </div>
                        <div class="flex items-center gap-2 bg-white dark:bg-gray-900 rounded-lg p-1 border dark:border-gray-700 shadow-sm">
                            <button wire:click="updateQuantity({{ $id }}, -1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-danger-500">-</button>
                            <span class="font-bold text-sm min-w-[20px] text-center dark:text-gray-200">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $id }}, 1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-primary-500">+</button>
                        </div>
                        <div class="text-right min-w-[60px]">
                            <p class="font-black text-sm text-gray-900 dark:text-white">${{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex-1 flex flex-col items-center justify-center text-gray-300 py-20">
                        <x-heroicon-o-shopping-cart class="w-16 h-16 mb-2 opacity-20" />
                        <p class="text-sm">Empty tray... start adding items!</p>
                    </div>
                @endforelse
            </div>

            <div class="p-6 bg-gray-900 dark:bg-black text-white rounded-t-[40px] shadow-2xl">
                <div class="flex justify-between items-center mb-6 px-2">
                    <span class="text-gray-400 font-medium">Total Payable</span>
                    <span class="text-3xl font-black text-white">${{ number_format($this->total, 2) }}</span>
                </div>
                <x-filament::button 
                    size="xl" 
                    color="primary" 
                    class="w-full text-lg py-4 shadow-xl shadow-primary-500/20"
                    wire:click="checkout"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>COMPLETE ORDER</span>
                    <span wire:loading>PROCESSING...</span>
                </x-filament::button>
            </div>
        </div>
    </div>

    <style>
        .noscrollbar::-webkit-scrollbar { display: none; }
        .noscrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-filament-panels::page>
