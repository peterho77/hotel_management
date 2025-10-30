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
        Schema::create('room_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customer')->nullOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedTinyInteger('num_rooms_booked');
            $table->unsignedTinyInteger('num_of_guests');
            $table->decimal('total_price', 11, 2);
            $table->decimal('amount_paid', 11, 2);
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->decimal('balance_due', 11, 2);
            $table->enum('payment_status', ['unpaid', 'deposit_paid', 'paid', 'refunded']);
            $table->enum('payment_method', ['pay at front desk', 'online payment', 'third-party']);
            $table->enum('payment_instrument', ['cash', 'card', 'e-wallet', 'bank transfer']);
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled']);
            $table->text('special_request')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_booking');
    }
};
