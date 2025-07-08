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
            // Add user_id column
            $table->foreignId('user_id')
                ->nullable()
                ->after('semester_id')
                ->constrained('users')
                ->nullOnDelete();
                
            // Copy data from lecturer_id to user_id if possible
            if (Schema::hasColumn('course_unit_programme_mappings', 'lecturer_id')) {
                // This assumes there's a way to map lecturer_id to user_id
                // You might need to adjust this based on your actual data structure
                DB::statement('UPDATE course_unit_programme_mappings SET user_id = lecturer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_unit_programme_mappings', function (Blueprint $table) {
            // Copy data back to lecturer_id if needed
            if (Schema::hasColumn('course_unit_programme_mappings', 'lecturer_id')) {
                DB::statement('UPDATE course_unit_programme_mappings SET lecturer_id = user_id');
            }
            
            // Drop the foreign key and column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
