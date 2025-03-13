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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phone')->nullable(); // Omogućite NULL vrednosti
            $table->string('payment_method'); // PayPal, bankovni račun...
            $table->decimal('deposits', 10, 2)->default(0.00); // Maksimalno 10 cifara, 2 decimalna mesta
            $table->enum('role', ['buyer', 'seller', 'both', 'admin'])->default('buyer');
            $table->string('avatar')->nullable();
            $table->integer('stars')->default(0); // 0 = Novi, 1 = Level 1...
            $table->boolean('is_verified')->default(false);
            $table->integer('seller_level')->default(0); // 0 = Novi, 1 = Level 1...
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
