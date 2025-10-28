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
        // Insert default values for loyalty settings
        \DB::table('settings')->insert([
            ['key' => 'loyalty_orders_per_star', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'loyalty_orders_per_half_star', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'loyalty_max_stars', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'loyalty_vip_stars', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('settings')->whereIn('key', [
            'loyalty_orders_per_star',
            'loyalty_orders_per_half_star',
            'loyalty_max_stars',
            'loyalty_vip_stars'
        ])->delete();
    }
};
