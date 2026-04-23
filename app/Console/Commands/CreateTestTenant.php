<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestTenant extends Command
{
    protected $signature = 'tenant:create {id} {domain}';

    protected $description = 'Create a test tenant and domain';

    public function handle()
    {
        $id = $this->argument('id');
        $domain = $this->argument('domain');

        if (\App\Models\Tenant::find($id)) {
            $this->error("Tenant '{$id}' already exists.");
            return;
        }

        $tenant = \App\Models\Tenant::create([
            'id' => $id,
            'currency' => 'BDT',
            'locale' => 'en',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(14),
        ]);
        $tenant->domains()->create(['domain' => $domain]);

        // Create a rich restaurant environment inside the tenant database
        $tenant->run(function () use ($id) {
            // 1. Admin User
            $admin = \App\Models\User::create([
                'name' => 'Restaurant Manager',
                'email' => "admin@{$id}.com",
                'password' => 'password',
            ]);

            // 2. Areas & Tables
            $mainFloor = \App\Models\Area::create(['name' => 'Main Hall']);
            $terrace = \App\Models\Area::create(['name' => 'Garden Terrace']);

            for ($i = 1; $i <= 5; $i++) {
                \App\Models\Table::create(['area_id' => $mainFloor->id, 'name' => "Table T{$i}", 'capacity' => 4]);
            }
            \App\Models\Table::create(['area_id' => $terrace->id, 'name' => "VIP 1", 'capacity' => 2]);

            // 3. Categories
            $catPizza = \App\Models\Category::create(['name' => 'Artisan Pizzas', 'slug' => 'pizzas']);
            $catDrinks = \App\Models\Category::create(['name' => 'Signature Cocktails', 'slug' => 'drinks']);

            // 4. Products with Stock tracking
            $pizza = \App\Models\Product::create([
                'category_id' => $catPizza->id,
                'name' => 'Margherita Deluxe',
                'slug' => 'margherita-deluxe',
                'price' => 14.99,
                'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbad80ad50?q=80&w=2070',
                'track_stock' => true,
                'stock_quantity' => 50,
                'min_stock_level' => 10,
            ]);

            $coke = \App\Models\Product::create([
                'category_id' => $catDrinks->id,
                'name' => 'Old Fashioned',
                'slug' => 'old-fashioned',
                'price' => 12.00,
                'image' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070',
                'track_stock' => true,
                'stock_quantity' => 100,
                'min_stock_level' => 20,
            ]);

            // 5. Demo Orders for Analytics
            for ($i = 1; $i <= 3; $i++) {
                $order = \App\Models\Order::create([
                    'table_id' => 1,
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'total_price' => 26.99,
                    'created_at' => now()->subDays(random_int(1, 5)),
                ]);
                $order->items()->create(['product_id' => $pizza->id, 'quantity' => 1, 'unit_price' => 14.99, 'subtotal' => 14.99]);
                $order->items()->create(['product_id' => $coke->id, 'quantity' => 1, 'unit_price' => 12.00, 'subtotal' => 12.00]);
            }

            // One Pending order for the KDS
            $pendingOrder = \App\Models\Order::create([
                'table_id' => 2,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_price' => 14.99,
            ]);
            $pendingOrder->items()->create(['product_id' => $pizza->id, 'quantity' => 1, 'unit_price' => 14.99, 'subtotal' => 14.99]);
        });

        $this->info("SUCCESS: Tenant '{$id}' is now live with a complete menu and demo data.");
        $this->info("Domain: http://{$domain}");
        $this->info("Credentials: admin@{$id}.com / password");
    }
}
