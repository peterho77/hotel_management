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
        Schema::create('rate_option_discount', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_rate_option_id')->constrained('room_rate_option')->nullOnDelete();
            $table->foreignId('discount_id')->constrained('discount')->nullOnDelete();
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_option_discount');
    }
};
