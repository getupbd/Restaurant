<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('code');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('product_variants', 'offer_rate')) {
                $table->decimal('offer_rate', 5, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('product_variants', 'offer_start_date')) {
                $table->date('offer_start_date')->nullable()->after('offer_rate');
            }
            if (!Schema::hasColumn('product_variants', 'offer_end_date')) {
                $table->date('offer_end_date')->nullable()->after('offer_start_date');
            }
            if (!Schema::hasColumn('product_variants', 'is_stock_validate')) {
                $table->boolean('is_stock_validate')->default(false)->after('stock_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'barcode',
                'offer_rate',
                'offer_start_date',
                'offer_end_date',
                'is_stock_validate'
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
};
