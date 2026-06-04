<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTimetablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table should be dropped if it exists from previous migrations
        if (Schema::hasTable('timetables')) {
            Schema::dropIfExists('timetables');
        }

        // Remove the timetables table migration if it exists
        $migrationFile = database_path('migrations/2025_07_09_142500_add_published_at_to_timetables_table.php');
        if (file_exists($migrationFile)) {
            unlink($migrationFile);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We can't recreate the timetables table here as we don't have the original schema
        // This is a one-way migration
    }
}
