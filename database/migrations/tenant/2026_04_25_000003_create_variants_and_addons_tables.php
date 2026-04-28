<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Food variants (e.g., Small, Medium, Large) - reusable across products
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // e.g., "Small", "Large", "Regular"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Addon groups (e.g., "Extra Toppings", "Sauces")
        Schema::create('addon_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_multi_select')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Addons (e.g., "Extra Cheese - 20 BDT")
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_group_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
        Schema::dropIfExists('addon_groups');
        Schema::dropIfExists('variants');
    }
};
