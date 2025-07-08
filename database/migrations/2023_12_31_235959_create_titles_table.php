<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('abbreviation', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Insert some common titles with all required fields
        $now = now();
        $titles = [
            [
                'name' => 'Professor', 
                'abbreviation' => 'Prof.', 
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Associate Professor', 
                'abbreviation' => 'Assoc. Prof.', 
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Senior Lecturer', 
                'abbreviation' => 'Sr. Lect.', 
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Lecturer', 
                'abbreviation' => 'Lect.', 
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Assistant Lecturer', 
                'abbreviation' => 'Asst. Lect.', 
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Doctor', 
                'abbreviation' => 'Dr.', 
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Mister', 
                'abbreviation' => 'Mr.', 
                'sort_order' => 7,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Miss', 
                'abbreviation' => 'Ms.', 
                'sort_order' => 8,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        
        // Insert in chunks to avoid issues with large inserts
        collect($titles)->chunk(10)->each(function ($chunk) {
            DB::table('titles')->insert($chunk->toArray());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
