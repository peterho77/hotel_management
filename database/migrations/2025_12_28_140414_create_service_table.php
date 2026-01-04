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
        Schema::create('service', function (Blueprint $table) {
            $table->id();
            // Định danh
            $table->string('code')->nullable()->unique(); // Mã hàng hóa (DV0019)
            $table->string('name'); // Tên dịch vụ (Câu cá (Lượt))

            $table->string('category')->default('service');

            // Đặc thù dịch vụ
            $table->integer('duration')->default(1); // Thời lượng (tính bằng giờ)

            // Giá cả
            $table->decimal('cost_price', 15, 2)->default(0); // Giá vốn (0)
            $table->decimal('selling_price', 15, 2)->default(0); // Giá bán (200,000)

            // Thông tin bổ sung
            $table->text('description')->nullable(); // Mô tả
            $table->text('note')->nullable(); // Ghi chú

            // Trạng thái
            $table->boolean('is_active')->default(true); // Bán trực tiếp / Ngừng kinh doanh
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};
