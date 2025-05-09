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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // pun naziv (npr. "Premium")
            $table->string('slug');  // skraćenica (npr. "premium")
            $table->text('description')->nullable();  // opis
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(1); // Količina (podrazumevano 1)
            $table->enum('duration', ['monthly', 'yearly']);  // Tip paketa (mesečno ili godišnje)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
