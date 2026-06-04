<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'Science and Technology',
                'code' => 'ST',
            ],
            [
                'name' => 'Business and Economics',
                'code' => 'BE',
            ],
            [
                'name' => 'Education',
                'code' => 'ED',
            ],
        ];

        foreach ($schools as $school) {
            School::updateOrCreate(
                ['name' => $school['name']],
                $school
            );
        }
    }
}
