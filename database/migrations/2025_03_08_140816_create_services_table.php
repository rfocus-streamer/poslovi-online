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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('category_id')->constrained();

            // Dodajte ovu liniju za opcionu podkategoriju
            $table->foreignId('subcategory_id')
                  ->nullable()
                  ->constrained('categories')
                  ->onDelete('set null'); // Opciono: ponašanje pri brisanju

            $table->string('title');
            $table->text('description');

            // Osnovni paket
            $table->decimal('basic_price', 10, 2);
            $table->integer('basic_delivery_days');
            $table->text('basic_inclusions');

            // Standard paket
            $table->decimal('standard_price', 10, 2);
            $table->integer('standard_delivery_days');
            $table->text('standard_inclusions');

            // Premium paket
            $table->decimal('premium_price', 10, 2);
            $table->integer('premium_delivery_days');
            $table->text('premium_inclusions');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
