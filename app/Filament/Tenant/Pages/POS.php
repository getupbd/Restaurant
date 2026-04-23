<?php

namespace App\Filament\Tenant\Pages;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class POS extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string $view = 'filament.tenant.pages.p-o-s';

    protected static ?string $title = 'Point of Sale';

    public $search = '';
    public $selectedCategoryId = null;
    public $cart = [];
    public $tableId = null;

    public function mount()
    {
        $this->cart = [];
    }

    protected function getForms(): array
    {
        return [];
    }

    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)->get();
    }

    public function getProductsProperty()
    {
        return Product::where('is_active', true)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
            ->get();
    }

    public function getTablesProperty()
    {
        return Table::all();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
            ];
        }
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function updateQuantity($productId, $delta)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity'] += $delta;
            if ($this->cart[$productId]['quantity'] <= 0) {
                unset($this->cart[$productId]);
            } else {
                $this->cart[$productId]['subtotal'] = $this->cart[$productId]['quantity'] * $this->cart[$productId]['price'];
            }
        }
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty')->danger()->send();
            return;
        }

        $order = Order::create([
            'table_id' => $this->tableId,
            'total_price' => $this->total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        foreach ($this->cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        $this->cart = [];
        $this->tableId = null;

        Notification::make()
            ->title('Order Created Successfully')
            ->success()
            ->send();
    }
}
