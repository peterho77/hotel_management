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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique(); // Mã hàng hóa (SP0009)
            $table->string('barcode')->nullable(); // Mã vạch
            $table->string('name'); 
            
            $table->enum('category', ['drink','food']);
            
            // Quản lý tồn kho (Định mức tồn)
            $table->integer('min_inventory')->default(0); // Định mức tồn nhỏ nhất
            $table->integer('max_inventory')->nullable(); // Định mức tồn lớn nhất (100,000)
            
            // Giá cả
            $table->decimal('cost_price', 15, 2)->default(0); // Giá vốn (20,000)
            $table->decimal('selling_price', 15, 2)->default(0); // Giá bán (25,000)
            
            // Thông tin khác
            $table->float('weight')->nullable(); // Trọng lượng
            $table->string('location')->nullable(); // Vị trí
            $table->text('description')->nullable(); // Mô tả
            $table->text('note')->nullable(); // Ghi chú
            
            // Trạng thái
            $table->boolean('is_active')->default(true); // Bán trực tiếp (tích xanh)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
