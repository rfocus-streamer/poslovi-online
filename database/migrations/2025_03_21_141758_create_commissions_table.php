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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('buyer_id');
            $table->decimal('amount', 12, 2); // Ukupna vrednost projekta
            $table->decimal('percentage', 5, 2); // Procent provizije
            $table->decimal('commission_amount', 12, 2); // amount * percentage
            $table->decimal('seller_amount', 12, 2); // prodavceva zarada od projekta
            $table->timestamps();

            // Strani ključevi
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
