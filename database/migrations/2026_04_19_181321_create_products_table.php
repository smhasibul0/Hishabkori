<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->json('image')->nullable();

            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();

            $table->text('description')->nullable();

            $table->decimal('purchase_price',10,2);
            $table->decimal('selling_price',10,2);

            $table->integer('quantity')->default(0);
            $table->integer('alert_quantity')->default(0);

            $table->boolean('manage_stock')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};