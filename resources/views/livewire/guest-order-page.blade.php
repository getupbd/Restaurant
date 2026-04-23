<div class="min-h-screen flex flex-col max-w-md mx-auto relative pb-32">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 p-6 flex items-center justify-between border-b border-gray-100">
        <div>
            <h1 class="text-2xl font-black tracking-tight uppercase">{{ tenant('id') }}</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $table->name }}</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex bg-gray-100 p-1 rounded-xl">
                <button 
                    wire:click="switchLanguage('en')"
                    class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all {{ $locale === 'en' ? 'bg-white shadow-sm text-primary-600' : 'text-gray-400' }}"
                >
                    EN
                </button>
                <button 
                    wire:click="switchLanguage('bn')"
                    class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all {{ $locale === 'bn' ? 'bg-white shadow-sm text-primary-600' : 'text-gray-400' }}"
                >
                    বাংলা
                </button>
            </div>
            <div class="bg-primary-500/10 p-3 rounded-2xl relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.112 16.835a2.25 2.25 0 0 1-2.244 2.398H5.033a2.25 2.25 0 0 1-2.244-2.398L3.899 8.507a2.25 2.25 0 0 1 2.244-2.398h12.712a2.25 2.25 0 0 1 2.244 2.398ZM9.75 10.5h4.5" />
                </svg>
                @if(count($cart) > 0)
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary-600 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">
                        {{ count($cart) }}
                    </span>
                @endif
            </div>
        </div>
    </header>

    @if(session()->has('success'))
        <div class="m-6 p-4 bg-success-500 text-white rounded-2xl shadow-xl shadow-success-500/20 text-sm font-bold flex items-center gap-2 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.74-5.24Z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="p-6">
        <!-- Search -->
        <div class="relative mb-8">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                </svg>
            </span>
            <input 
                type="text" 
                wire:model.live="search"
                placeholder="Hungry for something specific?" 
                class="w-full bg-white border-none rounded-3xl py-4 pl-12 pr-4 shadow-sm focus:ring-2 focus:ring-primary-500/20 text-sm"
            >
        </div>

        <!-- Categories -->
        <div class="flex gap-3 overflow-x-auto noscrollbar mb-8 -mx-6 px-6">
            <button 
                wire:click="$set('selectedCategoryId', null)"
                class="whitespace-nowrap px-6 py-3 rounded-2xl text-sm font-bold transition-all {{ $selectedCategoryId === null ? 'bg-primary-600 text-white shadow-xl shadow-primary-500/20' : 'bg-white text-gray-400 hover:text-gray-600' }}"
            >
                {{ __('messages.all_items') ?? 'All items' }}
            </button>
            @foreach($this->categories as $category)
                <button 
                    wire:click="$set('selectedCategoryId', {{ $category->id }})"
                    class="whitespace-nowrap px-6 py-3 rounded-2xl text-sm font-bold transition-all {{ $selectedCategoryId === $category->id ? 'bg-primary-600 text-white shadow-xl shadow-primary-500/20' : 'bg-white text-gray-400 hover:text-gray-600' }}"
                >
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <!-- Trending / Chef's Picks -->
        @if(count($trendingProducts) > 0 && $selectedCategoryId === null && !$search)
            <div class="mb-12 -mx-6 px-6">
                <h3 class="text-lg font-black uppercase tracking-tighter mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-500">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.chef_picks') }}
                </h3>
                <div class="flex gap-4 overflow-x-auto pb-6 noscrollbar -mx-6 px-6">
                    @foreach($trendingProducts as $product)
                        <div class="min-w-[240px] bg-white rounded-[32px] p-3 shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col group active:scale-95 transition-transform">
                            <div class="relative overflow-hidden rounded-[24px] aspect-[4/3] mb-4">
                                @if($product->image)
                                    <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/'.$product->image) }}" class="w-full h-full object-cover">
                                @endif
                                <div class="absolute top-2 right-2 bg-black/40 backdrop-blur-md px-3 py-1 rounded-full">
                                    <span class="text-[10px] font-black text-white uppercase tracking-widest leading-none">{{ tenant('currency_symbol') }}{{ number_format($product->price, 2) }}</span>
                                </div>
                            </div>
                            <div class="px-2 pb-2">
                                <h4 class="font-bold text-gray-900 leading-tight group-hover:text-primary-600 transition-colors">{{ $product->name }}</h4>
                                <div class="mt-4 flex justify-between items-center">
                                     <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $product->category->name }}</span>
                                     <button 
                                        wire:click="addToCart({{ $product->id }})"
                                        class="w-10 h-10 bg-primary-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30 hover:bg-primary-700 active:scale-90 transition-all"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Products -->
        <h2 class="text-xl font-black mb-6">{{ __('messages.menu') }}</h2>
        <div class="grid gap-6">
            @foreach($this->products as $product)
                <div class="bg-white rounded-[40px] p-2 pr-6 shadow-sm flex items-center gap-4 group active:scale-95 transition-transform">
                    <div class="w-24 h-24 rounded-[32px] overflow-hidden bg-gray-50 flex-shrink-0">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V9.574c0-1.067-.75-1.994-1.802-2.169a48.324 48.324 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 leading-tight">{{ $product->name }}</h3>
                        <p class="text-[10px] text-gray-400 mt-1 line-clamp-1 italic">{{ $product->description ?? 'Chef selected specialty' }}</p>
                        <p class="text-primary-600 font-black mt-1 text-lg">{{ tenant('currency_symbol') }}{{ number_format($product->price, 2) }}</p>
                    </div>
                    <button 
                        wire:click="addToCart({{ $product->id }})"
                        class="w-10 h-10 bg-[#F8F9FD] rounded-2xl flex items-center justify-center text-gray-900 hover:bg-primary-600 hover:text-white transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Floating Cart Footer -->
    @if(count($cart) > 0)
        <div class="fixed bottom-8 left-6 right-6 z-50 transition-all duration-500">
            <div class="bg-gray-900 rounded-[40px] p-6 shadow-2xl flex items-center justify-between border border-white/10">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">{{ count($cart) }} Items in Tray</p>
                    <p class="text-2xl font-black text-white">{{ tenant('currency_symbol') }}{{ number_format($this->total, 2) }}</p>
                </div>
                <button 
                    wire:click="submitOrder"
                    wire:loading.attr="disabled"
                    class="bg-primary-600 text-white font-black px-8 py-4 rounded-3xl shadow-xl shadow-primary-500/20 active:scale-95 transition-transform flex items-center gap-2"
                >
                    <span wire:loading.remove>{{ __('messages.order_now') }}</span>
                    <span wire:loading>...</span>
                    <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>
