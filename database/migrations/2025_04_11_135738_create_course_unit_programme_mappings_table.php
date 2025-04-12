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
        Schema::create('course_unit_programme_mappings', function (Blueprint $table) {
            $table->id();

            // Core academic mapping
            $table->foreignId('course_unit_id')->constrained('course_units')->onDelete('cascade');
            $table->foreignId('programme_id')->constrained('programmes')->onDelete('cascade');
            $table->foreignId('year_of_study_id')->constrained('years_of_study')->onDelete('cascade');
            // $table->foreignId('years_of_study')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');

            // Instructor and scheduling
            $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('set null');
            $table->foreignId('day_id')->nullable()->constrained('days')->onDelete('set null');
            // Morning slot
            $table->time('morning_start_time')->nullable();
            $table->integer('morning_duration')->nullable();

            $table->time('evening_start_time')->nullable();
            $table->integer('evening_duration')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_unit_programme_mappings');
    }
};
