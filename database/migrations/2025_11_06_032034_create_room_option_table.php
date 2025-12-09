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
        Schema::create('room_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_type')->nullOnDelete();
            $table->unsignedTinyInteger('num_adults');
            $table->unsignedTinyInteger('num_children')->default(0);
            $table->unsignedInteger('available_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_option');
    }
};
