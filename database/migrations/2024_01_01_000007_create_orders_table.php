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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->datetime('receive_at');
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'canceled'])->default('pending');
            $table->enum('payment_method', ['cod', 'bank_transfer', 'momo'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->bigInteger('subtotal'); // VND
            $table->bigInteger('discount')->default(0); // VND
            $table->bigInteger('shipping_fee')->default(0); // VND
            $table->bigInteger('grand_total'); // VND
            $table->datetime('placed_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['placed_at', 'status']);
            $table->index(['user_id', 'placed_at']);
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
