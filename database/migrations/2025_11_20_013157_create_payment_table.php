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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('booking')
                ->onDelete('cascade');

            // Số tiền giao dịch
            $table->decimal('amount', 12, 2);

            // Loại hình thanh toán: ví điện tử / thẻ / tiền mặt
            $table->enum('type', [
                'e_wallet',
                'bank_transfer',
                'cash',
                'card'
            ])->nullable();

            // Phương thức thanh toán cụ thể
            $table->enum('instrument', [
                'vnpay',
                'momo',
                'credit_card',
                'debit_card'
            ])->nullable();            

            // Trạng thái giao dịch
            $table->enum('status', ['pending', 'deposit_paid', 'paid', 'refunded'])->default('pending');

            // Transaction ID chung cho tất cả cổng (map theo loại)
            $table->string('transaction_id')->nullable();
            // Loại giao dịch: đặt cọc / thanh toán hết / refund
            $table->enum('transaction_type', ['deposit', 'full', 'refund'])->default('full');

            // VNPay fields
            $table->string('vnp_transaction_no')->nullable();
            $table->string('vnp_txn_ref')->nullable();
            $table->string('vnp_bank_code')->nullable();
            $table->string('vnp_card_type')->nullable();
            $table->string('vnp_response_code')->nullable();
            $table->dateTime('vnp_pay_date')->nullable();


            // MoMo fields
            $table->string('momo_order_id')->nullable();
            $table->string('momo_request_id')->nullable();
            $table->string('momo_trans_id')->nullable();
            $table->string('momo_message')->nullable();

            // Bank transfer
            $table->string('bank_ref_no')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();

            // Lưu full JSON raw response (VNPay/MoMo)
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
