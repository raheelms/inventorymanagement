<?php

use App\Enums\ArticleStatus;
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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('color');
            $table->longText(column: 'description')->nullable();
            $table->foreignId(column: 'user_id')->constrained()->cascadeOnDelete();
            $table->json(column: 'images')->nullable();
            $table->foreignId(column: 'media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean(column: 'is_visible')->default(value: true);
            $table->boolean('published')->default(false);
            $table->enum('status', [ArticleStatus::values()])->default(ArticleStatus::DRAFT)->nullable();
            $table->json('tags')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
