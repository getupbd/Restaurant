<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Customers table
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable()->unique();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->decimal('loyalty_points', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('delivery_persons')) {
            Schema::create('delivery_persons', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->decimal('commission_rate', 5, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Upgrade orders table — only add columns that don't already exist
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'invoice_no'))
                $table->string('invoice_no')->nullable()->unique()->after('id');
            if (!Schema::hasColumn('orders', 'order_type'))
                $table->string('order_type')->default('dine_in')->after('invoice_no');
            if (!Schema::hasColumn('orders', 'customer_id'))
                $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null')->after('order_type');
            if (!Schema::hasColumn('orders', 'waiter_id'))
                $table->foreignId('waiter_id')->nullable()->constrained('users')->onDelete('set null')->after('customer_id');
            if (!Schema::hasColumn('orders', 'delivery_person_id'))
                $table->foreignId('delivery_person_id')->nullable()->constrained('delivery_persons')->onDelete('set null')->after('waiter_id');
            if (!Schema::hasColumn('orders', 'subtotal'))
                $table->decimal('subtotal', 10, 2)->default(0)->after('total_price');
            if (!Schema::hasColumn('orders', 'discount_amount'))
                $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
            if (!Schema::hasColumn('orders', 'discount_type'))
                $table->string('discount_type')->nullable()->after('discount_amount');
            if (!Schema::hasColumn('orders', 'coupon_code'))
                $table->string('coupon_code')->nullable()->after('discount_type');
            if (!Schema::hasColumn('orders', 'vat_amount'))
                $table->decimal('vat_amount', 10, 2)->default(0)->after('coupon_code');
            if (!Schema::hasColumn('orders', 'event_code'))
                $table->string('event_code')->nullable()->after('vat_amount');
            if (!Schema::hasColumn('orders', 'voucher_no'))
                $table->string('voucher_no')->nullable()->after('event_code');
            if (!Schema::hasColumn('orders', 'is_paid'))
                $table->boolean('is_paid')->default(false)->after('payment_method');
            if (!Schema::hasColumn('orders', 'paid_at'))
                $table->timestamp('paid_at')->nullable()->after('is_paid');
        });

        // Order items upgrade — only add missing columns
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'variant_id'))
                $table->foreignId('variant_id')->nullable()->constrained('variants')->onDelete('set null')->after('product_id');
            if (!Schema::hasColumn('order_items', 'addon_total'))
                $table->decimal('addon_total', 10, 2)->default(0)->after('unit_price');
            if (!Schema::hasColumn('order_items', 'addons_snapshot'))
                $table->text('addons_snapshot')->nullable()->after('addon_total');
            if (!Schema::hasColumn('order_items', 'notes'))
                $table->text('notes')->nullable()->after('addons_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
            $table->dropColumn(['unit_price', 'addon_total', 'addons_snapshot', 'notes']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'order_type', 'subtotal', 'discount_amount', 'discount_type', 'coupon_code', 'vat_amount', 'event_code', 'voucher_no', 'is_paid', 'paid_at']);
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('waiter_id');
            $table->dropConstrainedForeignId('delivery_person_id');
        });
        Schema::dropIfExists('delivery_persons');
        Schema::dropIfExists('customers');
    }
};
