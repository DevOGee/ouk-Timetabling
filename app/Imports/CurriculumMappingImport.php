<?php

namespace App\Imports;

use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Programme;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class CurriculumMappingImport implements OnEachRow, WithHeadingRow
{
    public $successes = [];

    public $failures = [];

    public function onRow(Row $row)
    {
        $data = $row->toArray();

        $programmeCode = $data['programme_code'] ?? null;
        $courseCode = $data['course_code'] ?? null;
        $year = $data['year_of_study'] ?? null;
        $semester = $data['semester'] ?? null;

        // Find references
        $programme = Programme::where('code', $programmeCode)->first();
        $courseUnit = CourseUnit::where('code', $courseCode)->first();
        $yearModel = YearOfStudy::where('name', $year)->orWhere('id', $year)->first();
        $semesterModel = Semester::where('name', $semester)->orWhere('id', $semester)->first();

        // Collect errors
        $missing = [];
        if (! $programme) {
            $missing[] = "Programme '$programmeCode'";
        }
        if (! $courseUnit) {
            $missing[] = "Course '$courseCode'";
        }
        if (! $yearModel) {
            $missing[] = "Year '$year'";
        }
        if (! $semesterModel) {
            $missing[] = "Semester '$semester'";
        }

        if (empty($missing)) {
            CourseUnitProgrammeMapping::updateOrCreate([
                'programme_id' => $programme->id,
                'course_unit_id' => $courseUnit->id,
                'year_of_study_id' => $yearModel->id,
                'semester_id' => $semesterModel->id,
            ]);

            $this->successes[] = "$courseCode mapped to $programmeCode (Year $year, Semester $semester)";
        } else {
            $this->failures[] = "Row {$row->getIndex()}: ".implode(', ', $missing).' not found.';
        }
    }
}
