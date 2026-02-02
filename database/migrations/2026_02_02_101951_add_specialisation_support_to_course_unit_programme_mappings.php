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
            // Foreign key to specialisation (nullable for core courses)
            $table->foreignId('specialisation_id')
                  ->nullable()
                  ->after('programme_id')
                  ->constrained('specialisations')
                  ->onDelete('cascade');
            
            // Flag to indicate if course is core (shared across specialisations)
            $table->boolean('is_core')
                  ->default(true)
                  ->after('specialisation_id');
            
            // Index for faster queries
            $table->index(['programme_id', 'is_core', 'specialisation_id'], 'prog_core_spec_idx');
        });
        
        // Set all existing mappings as core
        DB::table('course_unit_programme_mappings')->update(['is_core' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_unit_programme_mappings', function (Blueprint $table) {
            $table->dropIndex('prog_core_spec_idx');
            $table->dropForeign(['specialisation_id']);
            $table->dropColumn(['specialisation_id', 'is_core']);
        });
    }
};
