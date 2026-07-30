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
        Schema::table('evaluations', function (Blueprint $table): void {
            $table->dropUnique('eval_class_enroll_course_class_disc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table): void {
            $table->unique(['class_enrollment_id', 'course_class_discipline_id'], 'eval_class_enroll_course_class_disc_unique');
        });
    }
};
