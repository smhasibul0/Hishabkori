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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
 
            $table->decimal('qty', 10, 2)->default(1);
            $table->string('unit')->default('Pieces');
            $table->decimal('unit_cost', 12, 2)->default(0);          // before discount
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('unit_cost_after_discount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
 
            $table->decimal('profit_margin', 5, 2)->default(0);       // %
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
