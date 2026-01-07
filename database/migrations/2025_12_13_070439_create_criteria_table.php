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
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();  // Auto-generated: C1, C2, dst
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['benefit', 'cost'])->default('benefit');
            // Note: weight column removed - weights stored in ahp_sessions table only
            $table->softDeletes();  // Soft delete support
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};
