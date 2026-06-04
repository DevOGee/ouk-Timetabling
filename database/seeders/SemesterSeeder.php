<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            ['name' => 'Semester 1'],
            ['name' => 'Semester 2'],
            ['name' => 'Summer Semester'],
        ];

        foreach ($semesters as $semester) {
            Semester::firstOrCreate(
                ['name' => $semester['name']],
                $semester
            );
        }
    }
}
