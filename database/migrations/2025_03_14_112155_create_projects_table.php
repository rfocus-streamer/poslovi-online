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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_number')->unique(); // Jedinstveni broj projekta
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('package'); // odabrani paket
            $table->integer('quantity')->default(1); // Količina (podrazumevano 1)
            $table->text('description')->nullable();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                'inactive',
                'in_progress',
                'rejected',
                'waiting_confirmation',
                'requires_corrections',
                'completed',
                'uncompleted'
            ])->default('inactive');
            $table->decimal('reserved_funds', 10, 2)->default(0.00);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('admin_decision', ['accepted', 'rejected', 'partially'])->nullable(); // Status prigovora (odluka podrške)
            $table->enum('admin_decision_reply', ['enabled', 'disabled'])->nullable(); // status odgovora
            $table->enum('seller_uncomplete_decision', ['accepted', 'arbitration'])->nullable(); // Status prigovora (odluka podrške)
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
