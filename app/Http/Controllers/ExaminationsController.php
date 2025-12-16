<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use Illuminate\Http\Request;

class ExaminationsController extends Controller
{
    public function index(Request $request)
    {
        $activeSchedule = ExamSchedule::where('is_active', true)
            ->where('is_published', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now()) // Optional: logic to find "current" schedule
            ->first();

        // If no currently active one by date, just get the one marked is_active AND published
        if (!$activeSchedule) {
            $activeSchedule = ExamSchedule::where('is_active', true)
                ->where('is_published', true)
                ->latest()
                ->first();
        }

        if (!$activeSchedule) {
            return view('examinations.no-schedule');
        }

        $query = $activeSchedule->exams()
            ->with(['courseUnit.programmes' => function ($query) use ($activeSchedule) {
                // Filter pivot by academic session to only show relevant programmes
                $query->wherePivot('academic_session_id', $activeSchedule->academic_session_id);
            }])
            ->orderBy('exam_date')
            ->orderBy('start_time');

        // Filter by School
        if ($request->filled('school')) {
            $query->whereHas('courseUnit.programmes', function ($q) use ($request) {
                $q->where('school_id', $request->school);
            });
        }

        // Filter by Programme
        if ($request->filled('programme')) {
            $query->whereHas('courseUnit.programmes', function ($q) use ($request) {
                $q->where('programmes.id', $request->programme);
            });
        }

        // Filter by Level (Year.Semester)
        if ($request->filled('level')) {
            $parts = explode('.', $request->level);
            if(count($parts) == 2) {
                $yearId = $parts[0];
                $semesterId = $parts[1];
                
                $query->whereHas('mapping', function($q) use ($yearId, $semesterId) {
                    $q->where('year_of_study_id', $yearId)
                      ->where('semester_id', $semesterId);
                });
            }
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('exam_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('exam_date', '<=', $request->end_date);
        }

        $exams = $query->get()
            ->groupBy(function($exam) {
                return $exam->exam_date ? $exam->exam_date->format('Y-m-d') : 'Unscheduled';
            });

        // Get filter data
        $schools = \App\Models\School::with('programmes')->get(); 
        
        // Construct combined levels (Year.Semester e.g., 1.1, 1.2)
        // AND Determine which levels are valid for which programme
        $levels = [];
        $years = \App\Models\YearOfStudy::orderBy('name')->get();
        $semesters = \App\Models\Semester::orderBy('name')->get();
        
        // Build the full list of potential levels (for the dropdown text)
        foreach ($years as $year) {
            foreach ($semesters as $semester) {
                $levels[] = (object)[
                    'id' => $year->id . '.' . $semester->id,
                    'name' => $year->name . '.' . $semester->name,
                ];
            }
        }

        // Fetch valid Programme -> Level mappings for the current session
        // This ensures dependent filtering works correctly
        $validMappings = \App\Models\CourseUnitProgrammeMapping::where('academic_session_id', $activeSchedule->academic_session_id)
            ->select('programme_id', 'year_of_study_id', 'semester_id')
            ->distinct()
            ->get()
            ->map(function($mapping) {
                return [
                    'programme_id' => $mapping->programme_id,
                    'level_id' => $mapping->year_of_study_id . '.' . $mapping->semester_id
                ];
            })
            ->groupBy('programme_id');

        return view('examinations.index', compact('activeSchedule', 'exams', 'schools', 'levels', 'validMappings'));
    }
}
