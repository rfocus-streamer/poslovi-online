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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();

            // Koristimo referred_by umesto referred_user_id za konzistentnost
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referral_id')->constrained('users')->onDelete('cascade'); // onaj koji deli share

            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');

            $table->decimal('amount', 10, 2);
            $table->unsignedTinyInteger('percentage');
            $table->enum('status', ['pending', 'completed', 'canceled', 'paid'])->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indeksi
            $table->index(['affiliate_id', 'status']);
            $table->index(['referral_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
