<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\School;
use App\Models\Department;
use App\Models\Programme;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('school_id')->constrained()->onDelete('restrict');
        });

        // Data Migration: Create default departments for existing schools and move programmes
        $schools = \App\Models\School::all();
        foreach ($schools as $school) {
            // Create a default department for the school
            $department = \App\Models\Department::create([
                'name' => 'General Department',
                'school_id' => $school->id,
                'code' => strtoupper(substr($school->name, 0, 3)) . '-GEN'
            ]);

            // Update all programmes belonging to this school to this new department
            \App\Models\Programme::where('school_id', $school->id)->update(['department_id' => $department->id]);
        }

        // Now verify all programmes have a department_id, if so, make it required and drop school_id
        if (\App\Models\Programme::whereNull('department_id')->doesntExist()) {
             Schema::table('programmes', function (Blueprint $table) {
                // Make department_id required
                $table->foreignId('department_id')->nullable(false)->change();
                // Drop school_id
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            //
        });
    }
};
