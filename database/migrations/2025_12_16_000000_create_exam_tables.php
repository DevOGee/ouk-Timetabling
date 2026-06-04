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
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')->constrained()->cascadeOnDelete();
            // Link to the specific class instance (mapping)
            $table->foreignId('course_unit_programme_mapping_id')
                  ->nullable()
                  ->constrained('course_unit_programme_mappings')
                  ->nullOnDelete();
                  
            // Redundant pointers for easier querying/reporting
            $table->foreignId('course_unit_id')->constrained()->cascadeOnDelete();
            
            // The instructor/invigilator assigned
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Scheduling details
            $table->date('exam_date')->nullable();
            $table->time('start_time')->nullable();
            $table->integer('duration_minutes')->default(120);
            
            // Optional room assignment (if rooms table exists, otherwise just nullable for future)
            // Assuming no rooms table for now based on file list, or maybe just integer
            $table->integer('room_id')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_schedules');
    }
};
