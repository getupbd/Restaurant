<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_variant_options');
        Schema::dropIfExists('variant_options');
        Schema::dropIfExists('variant_types');

        // 1. Create variant_types table
        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Size, Color
            $table->string('input_type')->default('dropdown'); // dropdown, button, color_picker
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Create variant_options table
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_type_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. Small, Red
            $table->string('value')->nullable(); // e.g. #FF0000
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Note: The old 'variants' table is dropped or ignored. We'll drop its foreign keys.
        // The table `product_variants` currently has `variant_id` which references `variants`.
        if (Schema::hasColumn('product_variants', 'variant_id')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropForeign(['variant_id']);
                $table->dropColumn('variant_id');
            });
        }
        
        Schema::table('product_variants', function (Blueprint $table) {
            // Add new fields for SKU and Stock if they don't exist
            if (!Schema::hasColumn('product_variants', 'sku')) {
                $table->string('sku')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('product_variants', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->after('price');
            }
        });

        // 3. Create product_variant_options pivot table
        Schema::create('product_variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('variant_option_id')->constrained('variant_options')->onDelete('cascade');
            $table->timestamps();
        });

        // Optionally, drop old variants table
        // Schema::dropIfExists('variants');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_options');
        
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['sku', 'stock_quantity']);
            $table->foreignId('variant_id')->nullable()->constrained('variants')->onDelete('cascade');
        });

        Schema::dropIfExists('variant_options');
        Schema::dropIfExists('variant_types');
    }
};
