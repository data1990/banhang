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
        Schema::create('customer_loyalty_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->integer('total_orders')->default(0);
            $table->integer('total_spent')->default(0); // in cents
            $table->integer('stars')->default(0); // Tối đa 10 sao
            $table->boolean('is_vip')->default(false); // true khi đạt 5 sao trở lên
            $table->timestamp('last_order_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_loyalty_profiles');
    }
};
