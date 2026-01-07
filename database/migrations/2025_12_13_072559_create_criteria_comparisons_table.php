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
        Schema::create('criteria_comparisons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('criterion_1_id')
                ->constrained('criteria')
                ->cascadeOnDelete();

            $table->foreignId('criterion_2_id')
                ->constrained('criteria')
                ->cascadeOnDelete();

            // nilai skala Saaty 1–9 (atau 1/nanti di-generate)
            $table->decimal('value', 8, 4);

            $table->timestamps();

            // Unique constraint TANPA ahp_session_id untuk sementara
            // Akan ditambahkan setelah ahp_sessions migration
            // $table->unique(['criterion_1_id', 'criterion_2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_comparisons');
    }
};
