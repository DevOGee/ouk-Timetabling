<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AcademicSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $currentYear = $now->year;
        
        // Create academic sessions for the past 2 years, current year, and next year
        for ($year = $currentYear - 2; $year <= $currentYear + 1; $year++) {
            $startDate = Carbon::create($year, 9, 1); // September 1st
            $endDate = Carbon::create($year + 1, 8, 31); // August 31st next year
            
            $isCurrent = $year === $currentYear;
            
            AcademicSession::updateOrCreate(
                ['code' => $year . '/' . ($year + 1)],
                [
                    'name' => 'Academic Year ' . $year . '/' . ($year + 1),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $isCurrent ? 'active' : ($year < $currentYear ? 'completed' : 'upcoming'),
                    'is_current' => $isCurrent,
                    'description' => 'Academic session for the year ' . $year . '-' . ($year + 1),
                ]
            );
        }
    }
}
