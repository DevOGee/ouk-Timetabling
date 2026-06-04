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
        Schema::table('course_unit_programme_mappings', function (Blueprint $table) {
            // Check if the lecturer_id column exists
            if (Schema::hasColumn('course_unit_programme_mappings', 'lecturer_id')) {
                // Drop the existing foreign key constraint
                $table->dropForeign(['lecturer_id']);
                
                // Rename the column
                $table->renameColumn('lecturer_id', 'user_id');
                
                // Add the new foreign key constraint
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_unit_programme_mappings', function (Blueprint $table) {
            if (Schema::hasColumn('course_unit_programme_mappings', 'user_id') && 
                !Schema::hasColumn('course_unit_programme_mappings', 'lecturer_id')) {
                // Drop the foreign key constraint
                $table->dropForeign(['user_id']);
                
                // Rename back to lecturer_id
                $table->renameColumn('user_id', 'lecturer_id');
                
                // Add back the old foreign key constraint (adjust if the original was different)
                $table->foreign('lecturer_id')
                    ->references('id')
                    ->on('lecturers')
                    ->onDelete('set null');
            }
        });
    }
};
