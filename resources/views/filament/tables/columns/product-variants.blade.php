@if($getRecord()->variants->count() > 0)
    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
        <h4 class="text-sm font-semibold mb-2">Item Variants</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-100 dark:bg-gray-900">
                    <tr>
                        <th class="px-3 py-2">SKU</th>
                        <th class="px-3 py-2">Options</th>
                        <th class="px-3 py-2">Price</th>
                        <th class="px-3 py-2">Stock</th>
                        <th class="px-3 py-2">Track Stock</th>
                        <th class="px-3 py-2">Active</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($getRecord()->variants as $variant)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-3 py-2 font-medium">{{ $variant->sku }}</td>
                            <td class="px-3 py-2">
                                @foreach($variant->options as $opt)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                        {{ $opt->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-3 py-2">৳{{ number_format($variant->price, 2) }}</td>
                            <td class="px-3 py-2">{{ $variant->stock_quantity }}</td>
                            <td class="px-3 py-2">
                                <button type="button" 
                                    wire:click="toggleVariantField({{ $variant->id }}, 'is_stock_validate')" 
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $variant->is_stock_validate ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $variant->is_stock_validate ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                            <td class="px-3 py-2">
                                <button type="button" 
                                    wire:click="toggleVariantField({{ $variant->id }}, 'is_active')" 
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $variant->is_active ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $variant->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="px-4 py-2 text-sm text-gray-500">
        No variants for this item.
    </div>
@endif
