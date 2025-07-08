<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core seeders
            RoleSeeder::class,
            UserSeeder::class,
            AdminUserSeeder::class,
            
            // Academic structure
            SchoolSeeder::class,
            YearOfStudySeeder::class,
            SemesterSeeder::class,
            AcademicSessionSeeder::class,
            
            // Course units
            CourseUnitSeeder::class,
            
            // Programmes and their relationships
            ProgrammeSeeder::class,
        ]);
    }
}
