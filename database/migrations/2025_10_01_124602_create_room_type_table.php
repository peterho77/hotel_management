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
        Schema::create('room_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->enum('bed_type',['queen','king','twin','single'])->default('queen');
            $table->unsignedSmallInteger('max_adults')->default(1);
            $table->unsignedSmallInteger('max_children')->default(1);
            $table->smallInteger('total_quantity');
            $table->decimal('base_price_per_night',9,2)->default(900000);
            $table->float('hourly_rate')->nullable();
            $table->float('full_day_rate')->nullable();
            $table->float('overnight_rate')->nullable();
            $table->json('amenities')->nullable();
            $table->string('status', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_type');
    }
};
