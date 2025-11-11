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
        Schema::create('room_rate_discount', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_rate_option_id')->constrained('room_rate_option')->nullOnDelete();
            $table->foreignId('discount_id')->constrained('discount')->nullOnDelete();
            $table->decimal('applied_value', 8, 2); 
            $table->unsignedSmallInteger('applied_order'); 
            $table->decimal('discount_applied', 9, 2);
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_rate_discount');
    }
};
