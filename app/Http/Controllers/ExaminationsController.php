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
            ->whereDate('end_date', '>=', now())
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

        // Base Query with Structural Filters
        $query = $activeSchedule->exams()
            ->with(['courseUnit.programmes' => function ($query) use ($activeSchedule) {
                // Filter pivot by academic session to only show relevant programmes
                $query->wherePivot('academic_session_id', $activeSchedule->academic_session_id);
            }])
            ->whereNotNull('exam_date') // Hide unscheduled
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

        // 1. Get ALL dates for Calendar (ignoring time/view windows)
        // This ensures the JS knows about ALL possible dates for index calculation if we switch views
        $calendarQuery = clone $query;
        $allExamDatesList = $calendarQuery->reorder()
             ->orderBy('exam_date')
             ->select('exam_date')
             ->distinct()
             ->pluck('exam_date')
             ->map(fn($d) => $d instanceof \DateTimeInterface ? $d->format('Y-m-d') : $d)
             ->values();
             
        $examDates = $allExamDatesList->toArray(); 

        // 2. Apply Date/View Filters for the actual Table List
        if ($request->filled('start_date')) {
            $query->whereDate('exam_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('exam_date', '<=', $request->end_date);
        }

        // Handle 'Show All' vs 'Upcoming' pivot
        if ($request->input('view') !== 'all') {
            $query->whereDate('exam_date', '>=', now()->startOfDay());
        }

        // 3. Pagination Logic (on the FILTERED set)
        $dateQuery = clone $query;
        
        // Manual Pagination for 100% Accuracy
        $filteredUniqueDates = $dateQuery->reorder()
            ->orderBy('exam_date')
            ->select('exam_date')
            ->distinct()
            ->pluck('exam_date')
            ->map(fn($d) => $d instanceof \DateTimeInterface ? $d->format('Y-m-d') : $d)
            ->values();
            
        // Setup Paginator
        $page = $request->input('page', 1);
        $perPage = 1;
        $slicedDates = $filteredUniqueDates->slice(($page - 1) * $perPage, $perPage)->values();
        
        $paginatedDates = new \Illuminate\Pagination\LengthAwarePaginator(
            $slicedDates,
            $filteredUniqueDates->count(), // Total pages = Total filtered dates
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Fetch exams ONLY for the dates on this page
        $pageDateValues = $paginatedDates->items();
        
        $exams = $query->whereIn('exam_date', $pageDateValues)
            ->get()
            ->groupBy(function($exam) {
                return $exam->exam_date ? $exam->exam_date->format('Y-m-d') : 'Unscheduled';
            });

        // Get filter data for dropdowns
        $schools = \App\Models\School::with('programmes')->get(); 
        
        $levels = [];
        $years = \App\Models\YearOfStudy::orderBy('name')->get();
        $semesters = \App\Models\Semester::orderBy('name')->get();
        
        foreach ($years as $year) {
            foreach ($semesters as $semester) {
                $levels[] = (object)[
                    'id' => $year->id . '.' . $semester->id,
                    'name' => $year->name . '.' . $semester->name,
                ];
            }
        }

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

        return view('examinations.index', compact('activeSchedule', 'exams', 'paginatedDates', 'schools', 'levels', 'validMappings', 'examDates', 'filteredUniqueDates'));
    }
}
