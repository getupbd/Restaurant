<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Raw ingredients / stock items
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('unit');                          // kg, litre, pcs, g, ml
            $table->decimal('purchase_price', 10, 2)->default(0);  // latest purchase price
            $table->decimal('stock_qty', 12, 4)->default(0);       // current stock
            $table->decimal('min_stock_level', 12, 4)->default(0); // alert threshold
            $table->decimal('opening_stock', 12, 4)->default(0);   // initial stock on setup
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Suppliers directory
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Purchase Orders header
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->string('status')->default('pending'); // pending, received, partial, cancelled
            $table->string('product_type')->nullable();   // e.g., Dry Goods, Beverages
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Purchase Order line items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('restrict');
            $table->decimal('qty', 12, 4);
            $table->string('unit');
            $table->decimal('rate', 10, 2);           // price per unit
            $table->string('vat_type')->default('exclusive'); // inclusive, exclusive
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        // Purchase Returns
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->date('return_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('restrict');
            $table->decimal('qty', 12, 4);
            $table->decimal('rate', 10, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        // Inventory adjustments (manual corrections)
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('restrict');
            $table->date('adjustment_date');
            $table->decimal('qty_before', 12, 4);
            $table->decimal('qty_adjusted', 12, 4);    // positive = add, negative = remove
            $table->decimal('qty_after', 12, 4);
            $table->string('reason')->nullable();       // damage, shrinkage, recount
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // Ingredient consumption log (auto-deducted when orders are placed via production)
        Schema::create('ingredient_consumption_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('restrict');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('qty_consumed', 12, 4);
            $table->date('consumed_date');
            $table->string('source')->default('order'); // order, production, manual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_consumption_logs');
        Schema::dropIfExists('inventory_adjustments');
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('ingredients');
    }
};
