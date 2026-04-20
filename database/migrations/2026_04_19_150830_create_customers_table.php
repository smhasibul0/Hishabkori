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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->unique()->nullable();
            $table->string('business_name')->nullable();
            $table->string('prefix')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->unique();
            $table->string('tax_number')->nullable();
            $table->string('credit_limit')->nullable();
            $table->string('pay_term')->nullable();
            $table->string('openning_balance')->nullable();
            $table->string('advance_balance')->nullable();
            $table->string('added_on')->nullable();
            $table->string('customer_group')->nullable();
            $table->text('address')->nullable();
            $table->string('sale_due')->nullable();
            $table->string('sale_return_due')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
