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
        Schema::create('room_rate_policy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_rate_option_id')->constrained('room_rate_option')->nullOnDelete();
            $table->foreignId('rate_policy_id')->constrained('rate_policy')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_rate_policy');
    }
};
