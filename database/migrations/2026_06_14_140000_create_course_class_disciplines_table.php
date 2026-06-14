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
        Schema::create('course_class_disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            // Unique combination of course_class and discipline
            $table->unique(['course_class_id', 'discipline_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_class_disciplines');
    }
};
