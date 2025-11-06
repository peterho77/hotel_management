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
        Schema::create('rate_policy', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cancellation_type');
            $table->string('payment_requirement');
            $table->boolean('is_refundable');
            $table->boolean('can_change_date');
            $table->unsignedSmallInteger('change_deadline_days')->default(3);
            $table->enum('deposit_type',['percentage','fixed','none'])->default('none');
            $table->unsignedSmallInteger('deposit_value')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_policy');
    }
};
