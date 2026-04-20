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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->unique()->nullable();
            $table->string('business_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->unique();
            $table->string('tax_number')->nullable();
            $table->string('pay_term')->nullable();
            $table->string('openning_balance')->nullable();
            $table->string('advance_balance')->nullable();
            $table->string('added_on')->nullable();
            $table->text('address')->nullable();
            $table->string('purchase_due')->nullable();
            $table->string('purchase_return_due')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
