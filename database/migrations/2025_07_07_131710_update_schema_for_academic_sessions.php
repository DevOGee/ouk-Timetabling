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
        // Add academic_session_id to course_unit_programme_mappings if it doesn't exist
        if (!Schema::hasColumn('course_unit_programme_mappings', 'academic_session_id')) {
            Schema::table('course_unit_programme_mappings', function (Blueprint $table) {
                $table->foreignId('academic_session_id')
                    ->after('id')
                    ->nullable()
                    ->constrained('academic_sessions')
                    ->onDelete('cascade');
            });
        }

        // Remove the curriculum_id foreign key from timetables if it exists
        Schema::table('timetables', function (Blueprint $table) {
            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'timetables' 
                AND COLUMN_NAME = 'curriculum_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            
            foreach ($foreignKeys as $key) {
                $table->dropForeign([$key->CONSTRAINT_NAME]);
            }
            
            if (Schema::hasColumn('timetables', 'curriculum_id')) {
                $table->dropColumn('curriculum_id');
            }
        });

        // Drop the curriculum_programme pivot table if it exists
        if (Schema::hasTable('curriculum_programme')) {
            Schema::drop('curriculum_programme');
        }

        // Drop the curricula table if it exists
        if (Schema::hasTable('curricula')) {
            Schema::drop('curricula');
        }

        // Update existing course_unit_programme_mappings to use the current academic session
        if (Schema::hasColumn('course_unit_programme_mappings', 'academic_session_id')) {
            $activeSession = DB::table('academic_sessions')
                ->where('is_current', true)
                ->first();
                
            if ($activeSession) {
                DB::table('course_unit_programme_mappings')
                    ->whereNull('academic_session_id')
                    ->update(['academic_session_id' => $activeSession->id]);
                
                // Make the academic_session_id required
                Schema::table('course_unit_programme_mappings', function (Blueprint $table) {
                    $table->foreignId('academic_session_id')->nullable(false)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This is a one-way migration. Rolling back would require restoring from backup.
        // We're implementing a basic rollback that won't restore the original state completely.
        
        // Recreate the curricula table
        if (!Schema::hasTable('curricula')) {
            Schema::create('curricula', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
        
        // Recreate the curriculum_programme pivot table
        if (!Schema::hasTable('curriculum_programme')) {
            Schema::create('curriculum_programme', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_id')->constrained()->onDelete('cascade');
                $table->foreignId('programme_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['curriculum_id', 'programme_id']);
            });
        }
        
        // Add curriculum_id back to timetables
        if (!Schema::hasColumn('timetables', 'curriculum_id')) {
            Schema::table('timetables', function (Blueprint $table) {
                $table->foreignId('curriculum_id')
                    ->nullable()
                    ->constrained('curricula')
                    ->onDelete('set null');
            });
        }
        
        // Note: We don't remove academic_session_id from course_unit_programme_mappings
        // as it's a one-way migration for data integrity.
    }
};
