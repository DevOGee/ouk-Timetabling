<?php

namespace App\Http\Controllers;

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
        $schools = School::all();
        $programmes = Programme::all();
        $years = YearOfStudy::all();
        $semesters = Semester::all();
        $days = Day::where('id', '<=', 5)->get();

        $timetable = collect();

        if ($request->filled(['school_id', 'programme_id', 'year_of_study_id', 'semester_id'])) {
            $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
                ->where('year_of_study_id', $request->year_of_study_id)
                ->where('semester_id', $request->semester_id)
                ->with(['courseUnit', 'lecturer', 'day'])
                ->get();
        }

        return view('timetable.index', compact(
            'schools',
            'programmes',
            'years',
            'semesters',
            'days',
            'timetable'
        ));
    }

    public function exportPDF(Request $request)
    {
        $schools = School::all();
        $programmes = Programme::all();
        $years = YearOfStudy::all();
        $semesters = Semester::all();
        $days = Day::where('id', '<=', 5)->get();

        $programme = Programme::findOrFail($request->programme_id);

        $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
            ->where('year_of_study_id', $request->year_of_study_id)
            ->where('semester_id', $request->semester_id)
            ->with(['courseUnit', 'lecturer', 'day'])
            ->get();

        $pdf = Pdf::loadView('timetable.pdf', compact(
            'schools',
            'programmes',
            'years',
            'semesters',
            'days',
            'timetable',
            'programme'
        ))->setPaper('A4', 'portrait');

        return $pdf->download('timetable.pdf');
    }
}
