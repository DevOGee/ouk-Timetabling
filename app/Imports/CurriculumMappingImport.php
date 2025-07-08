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
    protected $curriculumId;
    public $successes = [];
    public $failures = [];

    public function __construct($curriculumId)
    {
        $this->curriculumId = $curriculumId;
    }

    public function onRow(Row $row)
    {
        $data = $row->toArray();

        $programmeCode = $data['programme_code'] ?? null;
        $courseCode = $data['course_code'] ?? null;
        $year = $data['year_of_study'] ?? null;
        $semester = $data['semester'] ?? null;
        $lecturerCode = $data['lecturer_code'] ?? null;

        // Find references
        $programme = Programme::where('code', $programmeCode)->first();
        $courseUnit = CourseUnit::where('code', $courseCode)->first();
        $yearModel = YearOfStudy::where('name', $year)->orWhere('id', $year)->first();
        $semesterModel = Semester::where('name', $semester)->orWhere('id', $semester)->first();
        
        // Find lecturer if code is provided
        $lecturer = null;
        if ($lecturerCode) {
            $lecturer = \App\Models\Lecturer::where('code', $lecturerCode)->first();
        }

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
        if ($lecturerCode && !$lecturer) {
            $missing[] = "Lecturer '$lecturerCode'";
        }

        if (empty($missing)) {
            try {
                // Check if this mapping already exists in this curriculum
                $existingMapping = CourseUnitProgrammeMapping::where([
                    'programme_id' => $programme->id,
                    'course_unit_id' => $courseUnit->id,
                    'year_of_study_id' => $yearModel->id,
                    'semester_id' => $semesterModel->id,
                    'curriculum_id' => $this->curriculumId,
                ])->first();

                if ($existingMapping) {
                    $this->failures[] = "Mapping already exists for {$courseUnit->name} in {$programme->name} for the selected year and semester";
                    return;
                }

                // Create new mapping
                $mapping = new CourseUnitProgrammeMapping([
                    'programme_id' => $programme->id,
                    'course_unit_id' => $courseUnit->id,
                    'year_of_study_id' => $yearModel->id,
                    'semester_id' => $semesterModel->id,
                    'curriculum_id' => $this->curriculumId,
                    'lecturer_id' => $lecturer ? $lecturer->id : null,
                ]);

                $mapping->save();

                $this->successes[] = "Mapped {$courseUnit->name} to {$programme->name} for year {$yearModel->name}, {$semesterModel->name}" . 
                                    ($lecturer ? " (Lecturer: {$lecturer->name})" : '');
            } catch (\Exception $e) {
                $this->failures[] = "Failed to map {$courseCode} to {$programmeCode}: " . $e->getMessage();
            }
        } else {
            $this->failures[] = "Missing references: " . implode(', ', $missing);
        }    }
}
