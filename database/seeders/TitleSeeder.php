<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TitleSeeder extends Seeder
{
    public function run()
    {
        // Skip if we already have titles to prevent duplicates
        if (DB::table('titles')->count() > 0) {
            $this->command->info('Titles already exist. Skipping...');
            return;
        }

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
        
        // Insert the titles in chunks to avoid issues
        collect($titles)->chunk(10)->each(function ($chunk) {
            DB::table('titles')->insert($chunk->toArray());
        });
        
        $this->command->info('Seeded ' . count($titles) . ' titles.');
    }
}
