<?php

namespace App\Imports;

use App\Models\CourseUnit;
use App\Models\Lecturer;
use App\Models\Programme;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CourseMappingImport implements ToCollection, WithHeadingRow
{
    private $rowCount = 0; // Count successfully processed rows

    private $skippedRows = 0; // Count skipped rows

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $programmeCode = trim($row['programme_code']);
            $courseCode = trim($row['course_code']);
            $instructorEmail = isset($row['instructor_email']) ? trim($row['instructor_email']) : null;

            // Find Programme and Course in the database
            $programme = Programme::where('programme_code', $programmeCode)->first();
            $course = CourseUnit::where('code', $courseCode)->first();
            $lecturer = $instructorEmail ? Lecturer::where('email', $instructorEmail)->first() : null;

            // Skip if programme or course does not exist
            if (! $programme || ! $course) {
                $this->skippedRows++;

                continue;
            }

            // Attach course to programme if not already mapped
            if (! $programme->courseUnits()->where('course_unit_id', $course->id)->exists()) {
                $programme->courseUnits()->attach($course->id);
            }

            // Assign instructor to course if provided
            if ($lecturer && ! $course->instructors()->wherePivot('programme_id', $programme->id)->where('lecturer_id', $lecturer->id)->exists()) {
                $course->instructors()->attach($lecturer->id, ['programme_id' => $programme->id]);
            }

            $this->rowCount++; // Increment successful count
        }
    }

    /**
     * Get the number of successfully processed rows.
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * Get the number of skipped rows.
     */
    public function getSkippedRows()
    {
        return $this->skippedRows;
    }
}
