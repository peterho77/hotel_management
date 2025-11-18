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
        Schema::create('room', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name', 20);
            $table->foreignId('room_type_id')->constrained('room_type')->onDelete('cascade');
            $table->unsignedTinyInteger('floor')->default(1);
            $table->string('status', 50);
            $table->text('note')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branch')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room');
    }
};
