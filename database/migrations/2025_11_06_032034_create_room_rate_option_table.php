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
        Schema::create('room_rate_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_type')->nullOnDelete();
            $table->foreignId('rate_policy_id')->constrained('room_type')->nullOnDelete();
            $table->unsignedInteger('num_adults');
            $table->unsignedInteger('num_children')->default(0);
            $table->unsignedInteger('available_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_rate_option');
    }
};
