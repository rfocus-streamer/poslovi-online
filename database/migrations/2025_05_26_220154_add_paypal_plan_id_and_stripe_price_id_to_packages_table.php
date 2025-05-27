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
        Schema::table('packages', function (Blueprint $table) {
            $table->string('paypal_plan_id')->nullable()->after('duration'); // Dodaje polje posle duration
            $table->string('stripe_price_id')->nullable()->unique()->after('paypal_plan_id');
            $table->string('stripe_product_id')->nullable()->after('stripe_price_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['paypal_plan_id', 'stripe_price_id']);
        });
    }
};
