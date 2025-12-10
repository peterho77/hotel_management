<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('room_booking_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('booking')->nullOnDelete();
            $table->foreignId('room_option_id')->nullable()->constrained('room_option')->nullOnDelete();
            $table->foreignId('assigned_room_id')->nullable()->constrained('room')->nullOnDelete();
            $table->foreignId('applied_discount_id')->nullable()->constrained('discount')->nullOnDelete();
            $table->decimal('total_base_price',9,2);
            $table->decimal('discount_amount',9,2)->nullable();
            $table->decimal('final_price',9,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_booking_item');
    }
};
