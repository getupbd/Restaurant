<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Kitchen assignment
            $table->foreignId('kitchen_id')->nullable()->constrained('kitchens')->onDelete('set null')->after('category_id');

            // Food identity
            $table->string('code')->nullable()->after('name');            // Internal food code
            $table->text('notes')->nullable()->after('description');      // Short kitchen notes

            // Inventory & Production
            $table->string('storage_unit')->nullable()->after('notes');   // e.g., "kg", "pcs", "litre"
            $table->decimal('conversion_qty', 10, 4)->nullable()->after('storage_unit'); // e.g., 1 kg = 4 portions
            $table->boolean('is_stock_validate')->default(false)->after('conversion_qty'); // Block sale if out of stock
            $table->boolean('without_production')->default(false)->after('is_stock_validate'); // Skip production step
            $table->boolean('add_as_ingredient')->default(false)->after('without_production'); // Can be used as raw ingredient

            // Marketing & Sales
            $table->decimal('offer_rate', 5, 2)->nullable()->after('price');    // Discounted price or % off
            $table->date('offer_start_date')->nullable()->after('offer_rate');
            $table->date('offer_end_date')->nullable()->after('offer_start_date');
            $table->boolean('is_special')->default(false)->after('offer_end_date');        // Mark as Chef's Special
            $table->boolean('allow_custom_qty')->default(false)->after('is_special');      // Allow fractional qty
            $table->boolean('is_visible_on_web')->default(true)->after('allow_custom_qty');
            $table->boolean('is_price_editable')->default(false)->after('is_visible_on_web'); // Allow cashier to change price
            $table->integer('cooking_time')->nullable()->after('is_price_editable');       // In minutes
            $table->decimal('vat_rate', 5, 2)->default(0)->after('cooking_time');         // VAT %
            $table->integer('position')->default(0)->after('vat_rate');                    // Display order
        });

        // Product ↔ Variant pivot (price per variant)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Product ↔ AddonGroup pivot
        Schema::create('product_addon_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('addon_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_addon_groups');
        Schema::dropIfExists('product_variants');
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kitchen_id');
            $table->dropColumn([
                'code', 'notes', 'storage_unit', 'conversion_qty',
                'is_stock_validate', 'without_production', 'add_as_ingredient',
                'offer_rate', 'offer_start_date', 'offer_end_date',
                'is_special', 'allow_custom_qty', 'is_visible_on_web',
                'is_price_editable', 'cooking_time', 'vat_rate', 'position',
            ]);
        });
    }
};
