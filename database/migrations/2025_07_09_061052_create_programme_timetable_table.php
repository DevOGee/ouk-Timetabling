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
        Schema::create('programme_timetable', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programme_id');
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('academic_session_id');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('programme_id')
                  ->references('id')
                  ->on('programmes')
                  ->onDelete('cascade');
                  
            $table->foreign('timetable_id')
                  ->references('id')
                  ->on('timetables')
                  ->onDelete('cascade');
                  
            $table->foreign('academic_session_id')
                  ->references('id')
                  ->on('academic_sessions')
                  ->onDelete('cascade');
                  
            // Add unique constraint to prevent duplicate relationships
            $table->unique(['programme_id', 'academic_session_id']);
            $table->index(['timetable_id', 'academic_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programme_timetable');
    }
}
