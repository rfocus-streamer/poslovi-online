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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // Relacija ka projektu
            $table->unsignedBigInteger('user_id'); // Relacija ka prodavcu
            $table->unsignedBigInteger('service_id')->nullable(); // Relacija ka servisu (opciono)
            $table->text('message'); // Tekst prigovora
            $table->string('attachment')->nullable(); // Prilog (opciono)
            $table->timestamps();

            // Strani ključevi
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
