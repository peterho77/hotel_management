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
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('avatar')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable()->default('Việt Nam');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->foreignId('customer_type_id')->default(1)->constrained('customer_type')->nullOnDelete();
            $table->foreignId('customer_group_id')->default(2)->constrained('customer_group')->nullOnDelete();
            $table->string('note')->nullable();
            $table->boolean('has_account')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
