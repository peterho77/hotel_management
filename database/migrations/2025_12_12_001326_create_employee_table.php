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
        Schema::create('employee', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('code')->nullable()->unique();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            $table->date('birth_date')->nullable();

            // CMND/CCCD
            $table->string('identity_number', 12)->unique()->nullable();

            // chi nhánh làm việc
            $table->foreignId('branch_id')
                ->default(1)
                ->constrained('branch')
                ->cascadeOnDelete();

            $table->boolean('has_account')->default(false);

            // chức danh
            $table->string('position')->nullable();

            // ngày bắt đầu làm việc
            $table->date('start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
