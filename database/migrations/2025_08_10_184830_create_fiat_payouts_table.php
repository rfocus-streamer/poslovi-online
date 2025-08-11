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
        Schema::create('fiat_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('request_date');
            $table->date('payed_date')->nullable();
            $table->enum('status', ['requested', 'completed', 'rejected'])->default('requested');
            $table->enum('payment_method', ['paypal', 'card', 'bank']);
            $table->text('payment_details');
            $table->decimal('deposits', 10, 2);
            $table->string('card_number')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('card_expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiat_payouts');
    }
};
