<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Day;
use App\Models\Programme;
use App\Models\School;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        // Get active academic session
        $currentAcademicSession = \App\Models\AcademicSession::where('status', 'active')->first();
        
        if (!$currentAcademicSession) {
            return redirect()->back()->with('error', 'No active academic session found.');
        }
        
        // Get only programmes that have published timetables in the current academic session
        $programmesWithPublishedTimetables = \App\Models\ProgrammeTimetable::where('academic_session_id', $currentAcademicSession->id)
            ->where('status', 'published')
            ->with('programme')
            ->get()
            ->pluck('programme')
            ->unique('id')
            ->sortBy('name');
            
        $schools = School::whereIn('id', $programmesWithPublishedTimetables->pluck('school_id'))->get();
        $days = Day::where('id', '<=', 5)->get();

        // Create combined levels (e.g., 1.1, 1.2, 2.1, 2.2, etc.)
        $levels = [];
        $years = YearOfStudy::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        
        foreach ($years as $year) {
            foreach ($semesters as $semester) {
                $levels[] = (object)[
                    'id' => $year->id . '.' . $semester->id,
                    'name' => $year->name . '.' . $semester->name,
                    'year_id' => $year->id,
                    'semester_id' => $semester->id
                ];
            }
        }

        // Default empty collection if no filters are applied
        $timetable = collect();
        $selectedProgramme = null;

        if ($request->filled(['school_id', 'programme_id', 'level'])) {
            $selectedProgramme = Programme::find($request->programme_id);
            
            // Verify this programme has a published timetable
            $hasPublishedTimetable = \App\Models\ProgrammeTimetable::where('programme_id', $request->programme_id)
                ->where('academic_session_id', $currentAcademicSession->id)
                ->where('status', 'published')
                ->exists();
                
            if (!$hasPublishedTimetable) {
                return redirect()->route('timetable.index')
                    ->with('error', 'No published timetable found for the selected programme in the current academic session.');
            }
            
            list($yearId, $semesterId) = explode('.', $request->level);
            
            // Get the filtered timetable
            $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
                ->where('year_of_study_id', $yearId)
                ->where('semester_id', $semesterId)
                ->where('academic_session_id', $currentAcademicSession->id)
                ->with(['courseUnit', 'lecturer', 'day'])
                ->get();
        }

        return view('timetable.index', [
            'schools' => $schools,
            'programmes' => $programmesWithPublishedTimetables,
            'days' => $days,
            'timetable' => $timetable,
            'levels' => $levels,
            'selectedProgramme' => $selectedProgramme,
            'currentAcademicSession' => $currentAcademicSession
        ]);
    }

    // public function exportPDF(Request $request)
    // {
    //     // Retrieve the selected Programme, Year of Study, Semester, and Academic Year based on the request
    //     $programme = Programme::find($request->programme_id);
    //     $yearOfStudy = YearOfStudy::find($request->year_of_study_id);
    //     $semester = Semester::find($request->semester_id);
    //     $academicYear = AcademicYear::first();

    //     // Retrieve timetable data based on filters
    //     $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
    //         ->where('year_of_study_id', $request->year_of_study_id)
    //         ->where('semester_id', $request->semester_id)
    //         ->with(['courseUnit', 'instructor', 'day'])
    //         ->get();

    //     // Retrieve days for the timetable (assuming the days are predefined)
    //     $days = Day::where('id', '<=', 5)->get();

    //     // Generate the PDF
    //     $pdf = Pdf::loadView('timetable.pdf', compact(
    //         'programme',
    //         'timetable',
    //         'days',
    //         'yearOfStudy',
    //         'semester',
    //         'academicYear'
    //     ));

    //     // Export as a downloadable PDF
    //     return $pdf->download('timetable.pdf');
    // }

    public function exportPDF(Request $request)
    {
        // Get the programme with school relationship
        $programme = Programme::with('school')
            ->findOrFail($request->programme_id);
            
        // Get all days ordered by ID
        $days = Day::orderBy('id')->get();
        $timetable = collect();
        $levelName = 'N/A';
        $semesterName = 'N/A';
        $yearOfStudy = null;
        $semester = null;

        if ($request->filled(['school_id', 'programme_id', 'level'])) {
            list($yearId, $semesterId) = explode('.', $request->level);
            
            // Get level and semester names
            $yearOfStudy = \App\Models\YearOfStudy::find($yearId);
            $semester = \App\Models\Semester::find($semesterId);
            
            if ($yearOfStudy) {
                $levelName = $yearOfStudy->name;
            }
            
            if ($semester) {
                $semesterName = $semester->name;
            }
            
            // Get the timetable data with all necessary relationships
            $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
                ->where('year_of_study_id', $yearId)
                ->where('semester_id', $semesterId)
                ->with([
                    'courseUnit' => function($query) {
                        $query->with(['instructors.title']);
                    },
                    'day',
                    'lecturer.title',
                    'academicSession'
                ])
                ->get()
                ->map(function ($lesson) {
                    // Add formatted time fields for the view
                    if ($lesson->morning_start_time) {
                        $start = \Carbon\Carbon::parse($lesson->morning_start_time);
                        $end = $start->copy()->addMinutes($lesson->morning_duration);
                        
                        $lesson->formatted_start_time = $start->format('g:i A');
                        $lesson->formatted_end_time = $end->format('g:i A');
                        $lesson->duration = $lesson->morning_duration;
                        $lesson->start_minutes = $start->hour * 60 + $start->minute;
                        $lesson->end_minutes = $end->hour * 60 + $end->minute;
                        $lesson->session = 'Morning';
                    } elseif ($lesson->evening_start_time) {
                        $start = \Carbon\Carbon::parse($lesson->evening_start_time);
                        $end = $start->copy()->addMinutes($lesson->evening_duration);
                        
                        $lesson->formatted_start_time = $start->format('g:i A');
                        $lesson->formatted_end_time = $end->format('g:i A');
                        $lesson->duration = $lesson->evening_duration;
                        $lesson->start_minutes = $start->hour * 60 + $start->minute;
                        $lesson->end_minutes = $end->hour * 60 + $end->minute;
                        $lesson->session = 'Evening';
                    }
                    
                    // Add instructor name from the lecturer relationship
                    if ($lesson->lecturer) {
                        $lesson->instructor_name = $lesson->lecturer->full_name;
                    } else {
                        $lesson->instructor_name = 'TBA';
                    }
                    
                    return $lesson;
                });
        }

        // Filter out weekends
        $weekDays = $days->filter(function($day) {
            return !in_array(strtolower($day->name), ['saturday', 'sunday']);
        });

        // Prepare data for the view
        $data = [
            'programme' => $programme,
            'days' => $weekDays,
            'timetable' => $timetable,
            'levelName' => $levelName,
            'semesterName' => $semesterName,
            'academicYear' => now()->format('Y') . '/' . (now()->format('y') + 1),
            'weekDays' => $weekDays,
        ];

        // Generate PDF with proper options
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('timetable.pdf', $data)
            ->setPaper('A4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('dpi', 150)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('fontHeightRatio', 0.9);

        $filename = sprintf(
            'Timetable_%s_Level_%s_%s_%s.pdf',
            str_replace(' ', '_', $programme->name),
            $levelName,
            $semesterName,
            now()->format('Y-m-d')
        );
        
        return $pdf->download($filename);
    }
}
