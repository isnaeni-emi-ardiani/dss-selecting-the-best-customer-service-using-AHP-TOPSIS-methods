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
        Schema::create('decision_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete();

            // TOPSIS calculation results
            $table->decimal('d_plus', 10, 8)->nullable();   // Jarak ke solusi ideal positif (D+)
            $table->decimal('d_minus', 10, 8)->nullable();  // Jarak ke solusi ideal negatif (D-)
            $table->decimal('preference_value', 10, 8)->nullable();  // Nilai preferensi (C*)
            $table->integer('rank')->nullable();  // Ranking

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_details');
    }
};
