<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('color', 20)->nullable()->after('slug');          // Display color (hex)
            $table->string('image')->nullable()->after('color');              // Category image
            $table->string('icon')->nullable()->after('image');               // Optional icon name
            $table->decimal('offer_rate', 5, 2)->nullable()->after('icon');  // Discount %
            $table->date('offer_start_date')->nullable()->after('offer_rate');
            $table->date('offer_end_date')->nullable()->after('offer_start_date');
            $table->integer('position')->default(0)->after('offer_end_date'); // Listing order
            $table->boolean('show_on_web')->default(true)->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'color', 'image', 'icon',
                'offer_rate', 'offer_start_date', 'offer_end_date',
                'position', 'show_on_web',
            ]);
        });
    }
};
