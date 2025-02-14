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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable();
            $table->dateTime('purchase_date')->nullable();

            $table->decimal('total_amount', 10, 2)->nullable();            
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('taxes', 10, 2)->nullable();
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->decimal('shipping_amount', 10, 2)->nullable();
            $table->string(column: 'currency')->nullable();

            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->string('shipping_method')->nullable();
            $table->string('status')->default('in transit');
            $table->longText('notes')->nullable();
            $table->json('data')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
