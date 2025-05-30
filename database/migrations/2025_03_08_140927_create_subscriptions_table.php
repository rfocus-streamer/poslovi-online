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
        // Dodaj Subscription model
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('plan_id');
            $table->string('status');
            $table->string('gateway'); // 'paypal' ili 'stripe'
            $table->string('subscription_id')->nullable(); // ID od gateway-a
            $table->string('stripe_session_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
