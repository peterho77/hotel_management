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
        Schema::create('booking_item_discount_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_item_id')->constrained('room_booking_item')->nullOnDelete();
            $table->foreignId('discount_id')->constrained('discount')->nullOnDelete();
            $table->decimal('applied_value',8,2);
            $table->unsignedSmallInteger('applied_order');
            $table->decimal('discount_applied',9,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_item_discount');
    }
};
