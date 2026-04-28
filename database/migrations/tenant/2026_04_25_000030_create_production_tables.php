<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Production header — converting ingredients into finished food
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('production_no')->unique();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');  // Food item produced
            $table->foreignId('variant_id')->nullable()->constrained('variants')->onDelete('set null'); // Size/portion
            $table->date('production_date');
            $table->decimal('qty_produced', 12, 4)->default(1);   // How many units produced
            $table->decimal('total_cost', 12, 2)->default(0);     // Sum of ingredient costs
            $table->decimal('cost_per_unit', 12, 4)->default(0);  // total_cost / qty_produced
            $table->string('status')->default('completed');        // completed, wastage
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // Production line items — ingredients consumed in this production run
        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('restrict');
            $table->string('unit');
            $table->decimal('qty_required', 12, 4);    // Quantity needed from storage
            $table->decimal('current_rate', 10, 2);    // Price per unit at time of production
            $table->decimal('total_cost', 12, 2);      // qty_required * current_rate
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_items');
        Schema::dropIfExists('productions');
    }
};
