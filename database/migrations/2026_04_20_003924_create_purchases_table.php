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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('business_location_id')->nullable()->constrained('business_locations')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
 
            $table->string('reference_no')->unique();
            $table->dateTime('purchase_date');
            $table->enum('purchase_status', ['received', 'pending', 'ordered'])->default('pending');
 
            // Pay term
            $table->unsignedSmallInteger('pay_term_number')->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
 
            // Discount
            $table->enum('discount_type', ['none', 'fixed', 'percent'])->default('none');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);  // computed
 
            // Tax
            $table->decimal('purchase_tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
 
            // Shipping
            $table->text('shipping_details')->nullable();
            $table->decimal('shipping_charges', 12, 2)->default(0);
 
            // Totals
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('net_total', 12, 2)->default(0);
            $table->decimal('purchase_total', 12, 2)->default(0);
 
            // Payment
            $table->enum('payment_status', ['due', 'partial', 'paid'])->default('due');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_due', 12, 2)->default(0);
 
            // Extras
            $table->text('additional_notes')->nullable();
            $table->string('document')->nullable();      // file path
 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
