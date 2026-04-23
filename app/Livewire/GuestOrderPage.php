use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\Session;

class GuestOrderPage extends Component
{
    public $tableId;
    public $table;
    public $search = '';
    public $selectedCategoryId = null;
    public $cart = [];
    public $locale = 'en';

    public $trendingProducts = [];

    public function mount($tableId)
    {
        $this->tableId = $tableId;
        $this->table = Table::findOrFail($tableId);
        $this->locale = tenant('locale') ?? 'en';
        app()->setLocale($this->locale);
        $this->cart = Session::get("cart_{$this->tableId}", []);
        $this->loadTrendingProducts();
    }

    public function switchLanguage($locale)
    {
        $this->locale = $locale;
        app()->setLocale($locale);
        session()->put('locale', $locale);
    }

    public function loadTrendingProducts()
    {
        // AI-style recommendation: Fetch top 3 most ordered products for this tenant
        $this->trendingProducts = Product::where('is_active', true)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(3)
            ->get();
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
        
        Session::put("cart_{$this->tableId}", $this->cart);
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
        Session::put("cart_{$this->tableId}", $this->cart);
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function submitOrder()
    {
        if (empty($this->cart)) return;

        $order = Order::create([
            'table_id' => $this->tableId,
            'total_price' => $this->total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'notes' => 'GUEST ORDER',
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
        Session::forget("cart_{$this->tableId}");
        Session::flash('success', 'Order submitted successfully! We are preparing your food.');
    }

    public function render()
    {
        return view('livewire.guest-order-page')->layout('layouts.guest');
    }
}
