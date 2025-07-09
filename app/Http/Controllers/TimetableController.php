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
        // Get current academic session
        $currentAcademicSession = \App\Models\AcademicSession::where('is_current', true)->first();
        
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
        $programme = Programme::find($request->programme_id);
        $days = Day::orderBy('id')->get();
        $timetable = collect();

        if ($request->filled(['school_id', 'programme_id', 'level'])) {
            list($yearId, $semesterId) = explode('.', $request->level);
            $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
                ->where('year_of_study_id', $yearId)
                ->where('semester_id', $semesterId)
                ->with(['courseUnit', 'lecturer', 'day'])
                ->get();
        }

        // Sort the timetable by day
        $timetable = $timetable->sortBy(function ($lesson) {
            return $lesson->day->name;
        });

        // Group and sort lessons by day
        $groupedLessons = $timetable->groupBy('day_id')->sortBy(function ($day) {
            return $day->first()->day->name;  // Sorting days alphabetically
        });

        // Return the PDF view
        $pdf = Pdf::loadView('timetable.pdf', compact('programme', 'days', 'timetable', 'groupedLessons'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('timetable.pdf');
    }
}
