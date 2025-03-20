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
        Schema::create('additional_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // Relacija ka projektu
            $table->unsignedBigInteger('seller_id'); // Relacija ka prodavcu
            $table->decimal('amount', 10, 2); // Iznos dodatne naplate
            $table->text('reason'); // Razlog za dodatnu naplatu
            $table->enum('status', ['waiting_confirmation', 'rejected', 'completed'])->default('waiting_confirmation');
            $table->timestamps();

            // Strani ključevi
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_charges');
    }
};
