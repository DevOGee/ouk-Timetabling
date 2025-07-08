<?php

namespace Database\Seeders;

use App\Models\YearOfStudy;
use Illuminate\Database\Seeder;

class YearOfStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            ['name' => 'First Year'],
            ['name' => 'Second Year'],
            ['name' => 'Third Year'],
            ['name' => 'Fourth Year'],
            ['name' => 'Postgraduate'],
        ];

        foreach ($years as $year) {
            YearOfStudy::firstOrCreate(
                ['name' => $year['name']],
                $year
            );
        }
    }
}
