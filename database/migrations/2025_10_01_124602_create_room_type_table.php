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
            $table->string('bed_type')->default('single');
            $table->mediumText('description')->nullable();
            $table->unsignedSmallInteger('max_adults')->default(1);
            $table->unsignedSmallInteger('max_children')->default(0);
            $table->smallInteger('total_quantity');
            $table->decimal('base_price',9,2);
            $table->decimal('hourly_rate',8,2)->nullable();
            $table->decimal('full_day_rate',9,2)->nullable();
            $table->decimal('overnight_rate',9,2)->nullable();
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
