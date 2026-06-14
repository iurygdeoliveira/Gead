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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->decimal('planning_score', 4, 2)->nullable();
            $table->decimal('posture_score', 4, 2)->nullable();
            $table->decimal('attendance_score', 4, 2)->nullable();
            $table->decimal('punctuality_score', 4, 2)->nullable();
            $table->decimal('execution_score', 4, 2)->nullable();
            $table->decimal('assessment_score', 4, 2)->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
