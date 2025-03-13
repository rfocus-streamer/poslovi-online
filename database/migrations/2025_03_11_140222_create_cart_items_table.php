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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Korisnik koji dodaje u korpu
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // Servis koji se dodaje u korpu
            $table->integer('quantity')->default(1); // Količina (podrazumevano 1)
            $table->string('package'); // odabrani paket
            $table->timestamps(); // Datum i vreme (created_at i updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
