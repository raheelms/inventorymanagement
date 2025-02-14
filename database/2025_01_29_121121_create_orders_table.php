<?php

use App\Models\Customer;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_id')->unique();
            $table->string('number')->nullable();

            $table->string('email');
            $table->foreignIdFor(Customer::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedBigInteger(column: 'quantity')->default(state: 1);
            $table->decimal(column: 'unit_amount', total: 10, places:2)->nullable();
            $table->decimal(column: 'total_amount', total: 10, places:2)->nullable();
            $table->integer('taxes')->nullable();
            $table->integer('discount')->nullable();
            $table->decimal(column: 'grand_total', total: 10, places:2)->nullable();
            $table->string(column: 'currency')->nullable();

            $table->string(column:'payment_method')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->string(column:'payment_intent_id')->nullable();

            $table->decimal(column: 'shipping_amount', total: 10, places:2)->nullable();
            $table->string(column: 'shipping_method')->nullable();

            $table->enum('status', ['Pending', 'Dispatched', 'Out For Delivery', 'Delivered', 'Canceled'])
                ->default('Pending');

            $table->longText(column: 'notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
