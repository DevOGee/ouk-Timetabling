<?php

namespace App\Imports;

use App\Models\CourseUnit;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CourseUnitsImport implements ToModel, WithHeadingRow
{
    public $importedCount = 0; // Track successful imports

    public function model(array $row)
    {
        // Check if the course unit already exists
        $exists = CourseUnit::where('code', $row['code'])->exists();
        if ($exists) {
            return null; // Skip duplicates
        }

        // Ensure foreign keys exist before inserting
        if (
            ! YearOfStudy::where('id', $row['year_of_study_id'])->exists() ||
            ! Semester::where('id', $row['semester_id'])->exists()
        ) {
            return null;
        }

        // Increment successful import count
        $this->importedCount++;

        return new CourseUnit([
            'code' => $row['code'],
            'name' => $row['name'],
            'year_of_study_id' => $row['year_of_study_id'],
            'semester_id' => $row['semester_id'],
            'color' => $row['color'] ?? '#ff7f50', // Default color
        ]);
    }
}
