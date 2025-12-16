<?php

namespace App\Imports;

use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Programme;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ExamImport implements ToModel, WithHeadingRow
{
    protected $examScheduleId;
    protected $academicSessionId;

    public function __construct($examScheduleId, $academicSessionId)
    {
        $this->examScheduleId = $examScheduleId;
        $this->academicSessionId = $academicSessionId;
    }

    public function model(array $row)
    {
        // Headers: course_code, programme_code, date, start_time, duration
        
        $courseCode = $row['course_code'] ?? null;
        if (!$courseCode) return null;

        $courseUnit = CourseUnit::where('code', $courseCode)->first();
        if (!$courseUnit) return null; // Skip if invalid course

        // Find Programme if provided
        $programmeCode = $row['programme_code'] ?? null;
        $programme = $programmeCode ? Programme::where('code', $programmeCode)->first() : null;

        // Try to find the specific mapping
        $mappingQuery = CourseUnitProgrammeMapping::where('academic_session_id', $this->academicSessionId)
            ->where('course_unit_id', $courseUnit->id);
            
        if ($programme) {
            $mappingQuery->where('programme_id', $programme->id);
        }
        
        $mapping = $mappingQuery->first();
        
        // Prepare Data
        $examDate = isset($row['date']) ? Carbon::parse($row['date']) : null;
        $startTime = isset($row['start_time']) ? Carbon::parse($row['start_time']) : null;
        $duration = isset($row['duration']) ? (int)$row['duration'] : 120;

        // Update existing or create new
        return Exam::updateOrCreate(
            [
                'exam_schedule_id' => $this->examScheduleId,
                'course_unit_id' => $courseUnit->id,
                'course_unit_programme_mapping_id' => $mapping?->id, // Can be null if manual mapping not found
            ],
            [
                'user_id' => $mapping?->user_id, // Default to instructor
                'exam_date' => $examDate,
                'start_time' => $startTime,
                'duration_minutes' => $duration,
            ]
        );
    }
}
