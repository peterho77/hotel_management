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
        Schema::create('retail_order', function (Blueprint $table) {
            $table->id();

            // mã hóa đơn
            $table->string('code')->nullable()->unique();

            $table->foreignId('customer_id')->nullable()
                ->constrained('customer')
                ->onDelete('cascade');

            $table->foreignId('creator_id')
                ->constrained('employee')
                ->onDelete('cascade');

            // can expand booking source
            $table->string('source_type')->nullable();

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retail_order');
    }
};
