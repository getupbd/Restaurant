<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Customer reservations/bookings
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('booking_ref')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->integer('party_size');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->text('special_request')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, seated, completed, cancelled, no_show
            $table->text('notes')->nullable();           // internal staff notes
            $table->timestamps();
        });

        // Blocked dates — no bookings accepted
        Schema::create('unavailable_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('reason')->nullable();        // e.g., "Public Holiday", "Private Event"
            $table->timestamps();
        });

        // Reservation configuration (single row settings)
        Schema::create('reservation_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('advance_booking_days')->default(30);    // How far ahead can you book
            $table->integer('max_party_size')->default(20);          // Max guests per booking
            $table->integer('slot_duration_minutes')->default(60);   // Time slot length
            $table->boolean('auto_confirm')->default(false);         // Auto-confirm or require approval
            $table->boolean('allow_online_booking')->default(true);
            $table->json('time_slots')->nullable();                  // Available time slots as JSON array
            $table->json('blocked_days_of_week')->nullable();        // e.g., [0] = Sunday closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_settings');
        Schema::dropIfExists('unavailable_days');
        Schema::dropIfExists('reservations');
    }
};
