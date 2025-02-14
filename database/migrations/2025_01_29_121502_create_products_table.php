<?php

use App\Enums\ProductStatus;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name');
            $table->string(column: 'slug')->unique();
            $table->longText(column: 'description')->nullable();
            $table->json('images')->default(json_encode(['/images/default_image.png']))->nullable();

            $table->decimal('price', total: 10, places:2)->default(0); // if no variants exist
            $table->decimal('discount_price', total: 10, places:2)->default(0);
            $table->datetime('discount_to')->nullable();
            $table->double('taxes')->default(0)->nullable();

            $table->string(column: 'sku')->unique();
            $table->decimal(column: 'stock')->default(1); // if no variants exist
            $table->decimal(column: 'safety_stock')->default(0);

            $table->enum('status', [ProductStatus::values()])->default(ProductStatus::DRAFT)->nullable();
            $table->boolean(column: 'is_visible')->default(value: true);
            $table->boolean(column: 'is_featured')->default(value: false);
            $table->boolean(column: 'in_stock')->default(value: true);
            $table->boolean(column: 'on_sale')->default(value: false);

            $table->json('tags')->nullable();
            $table->json('data')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date(column: 'published_at');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
