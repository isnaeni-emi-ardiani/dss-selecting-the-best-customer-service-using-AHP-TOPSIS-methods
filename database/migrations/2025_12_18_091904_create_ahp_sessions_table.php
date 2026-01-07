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
        Schema::create('ahp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Hasil perhitungan AHP
            $table->integer('matrix_size');
            $table->decimal('lambda_max', 10, 6)->nullable();
            $table->decimal('consistency_index', 10, 6)->nullable();
            $table->decimal('random_index', 10, 6)->nullable();
            $table->decimal('consistency_ratio', 10, 6)->nullable();
            $table->boolean('is_consistent')->default(false);
            $table->boolean('is_active')->default(false)->index();  // Active session flag

            // Snapshot dan weights
            $table->json('criteria_ids')->nullable();  // Snapshot kriteria yang digunakan
            $table->json('weights')->nullable();  // Array bobot hasil AHP

            $table->timestamps();
        });

        // Update tabel criteria_comparisons untuk link ke AHP session
        Schema::table('criteria_comparisons', function (Blueprint $table) {
            $table->foreignId('ahp_session_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->unique(['ahp_session_id', 'criterion_1_id', 'criterion_2_id'], 'criteria_comparisons_session_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criteria_comparisons', function (Blueprint $table) {
            $table->dropUnique('criteria_comparisons_session_unique');
            $table->dropForeign(['ahp_session_id']);
            $table->dropColumn('ahp_session_id');
        });

        Schema::dropIfExists('ahp_sessions');
    }
};
