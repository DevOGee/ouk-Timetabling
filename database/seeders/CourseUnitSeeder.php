<?php

namespace Database\Seeders;

use App\Models\CourseUnit;
use Illuminate\Database\Seeder;

class CourseUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseUnits = [
            ['code' => 'CSC 209', 'name' => 'Database Systems', 'color' => '#3BB994'],
            ['code' => 'CDS 801', 'name' => 'Digital Services Operational Models', 'color' => '#279EFF'],
            ['code' => 'CDS 803', 'name' => 'Advanced Business and Management Communication', 'color' => '#D83F31'],
            ['code' => 'CDS 807', 'name' => 'Digital Infrastructure and Services', 'color' => '#FF7F50'],
            ['code' => 'CIT 101', 'name' => 'Fundamentals of Computer Technology', 'color' => '#6C3428'],
            ['code' => 'CIT 103', 'name' => 'Fundamentals of Computer Programming', 'color' => '#3BB994'],
            ['code' => 'CIT 107', 'name' => 'ICT and Digital Tools in Agriculture', 'color' => '#279EFF'],
            ['code' => 'CIT 121', 'name' => 'Web Development', 'color' => '#D83F31'],
            ['code' => 'CIT 212', 'name' => 'System Analysis and Design', 'color' => '#FF7F50'],
            ['code' => 'CIT 221', 'name' => 'Computer Maintenance and Networking', 'color' => '#6C3428'],
            ['code' => 'CIT 223', 'name' => 'Internet Application Programming', 'color' => '#3BB994'],
            ['code' => 'CSA 804', 'name' => 'Responsible and Explainable AI', 'color' => '#279EFF'],
            ['code' => 'CSC 101', 'name' => 'Introduction to Computing Systems', 'color' => '#D83F31'],
        ];

        foreach ($courseUnits as $courseUnit) {
            CourseUnit::firstOrCreate(
                ['code' => $courseUnit['code']],
                $courseUnit
            );
        }
    }
}
