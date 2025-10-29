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
            $table->foreignId('booking_id')->nullable()->constrained('room_booking')->nullOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('room_type')->nullOnDelete();
            $table->decimal('price_per_night', 9, 2);
            $table->unsignedTinyInteger('num_of_rooms');
            $table->unsignedTinyInteger('num_of_adults');
            $table->unsignedTinyInteger('num_of_children')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_line_item');
    }
};
