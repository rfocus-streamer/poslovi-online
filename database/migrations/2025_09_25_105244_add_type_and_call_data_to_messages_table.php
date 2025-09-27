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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('type')->default('text'); // Dodajemo kolonu type sa default vrednošću 'text'
            $table->text('call_data')->nullable(); // Dodajemo kolonu call_data koja može biti null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['type', 'call_data']); // Brišemo kolone ako rollback-ujemo migraciju
        });
    }
};
