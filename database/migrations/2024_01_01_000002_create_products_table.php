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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->bigInteger('price'); // VND, no decimals
            $table->bigInteger('sale_price')->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('short_desc')->nullable();
            $table->text('long_desc')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->index(['slug', 'sku', 'is_active']);
            $table->index(['is_active', 'created_at']);
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
