<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixMigrationIssues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ensure the timetables table is dropped if it exists
        if (Schema::hasTable('timetables')) {
            Schema::dropIfExists('timetables');
        }

        // Ensure the programme_timetable table has all necessary columns
        if (!Schema::hasTable('programme_timetable')) {
            Schema::create('programme_timetable', function (Blueprint $table) {
                $table->id();
                $table->foreignId('programme_id')->constrained()->onDelete('cascade');
                $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->timestamp('published_at')->nullable();
                $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                // Ensure each programme has only one timetable per academic session
                $table->unique(['programme_id', 'academic_session_id']);
            });
        } else {
            // Add any missing columns to the programme_timetable table
            Schema::table('programme_timetable', function (Blueprint $table) {
                if (!Schema::hasColumn('programme_timetable', 'status')) {
                    $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                }
                if (!Schema::hasColumn('programme_timetable', 'published_at')) {
                    $table->timestamp('published_at')->nullable();
                }
                if (!Schema::hasColumn('programme_timetable', 'published_by')) {
                    $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is a one-way migration to fix the schema
        // No need to implement down() as we don't want to rollback these fixes
    }
}
