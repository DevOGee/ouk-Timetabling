<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core seeders
            TitleSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            AdminUserSeeder::class,
            FacultyUserSeeder::class,
            
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
