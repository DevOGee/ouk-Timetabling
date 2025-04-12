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
        $schools = School::all();
        $programmes = Programme::all();
        $years = YearOfStudy::all();
        $semesters = Semester::all();
        $days = Day::where('id', '<=', 5)->get();

        // Fetch the Year of Study, Semester, and Academic Year from the request
        $yearOfStudy = YearOfStudy::find($request->year_of_study_id);
        $semester = Semester::find($request->semester_id);
        $academicYear = AcademicYear::find($request->academic_year_id);

        // Default empty collection if no filters are applied
        $timetable = collect();

        if ($request->filled(['school_id', 'programme_id', 'year_of_study_id', 'semester_id'])) {
            // Get the filtered timetable
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
            'timetable',
            'yearOfStudy',       // Pass Year of Study to view
            'semester',          // Pass Semester to view
            'academicYear'       // Pass Academic Year to view
        ));
    }

    public function exportPDF(Request $request)
    {
        // Retrieve the selected Programme, Year of Study, Semester, and Academic Year based on the request
        $programme = Programme::find($request->programme_id);
        $yearOfStudy = YearOfStudy::find($request->year_of_study_id);
        $semester = Semester::find($request->semester_id);
        $academicYear = AcademicYear::first();

        // Retrieve timetable data based on filters
        $timetable = CourseUnitProgrammeMapping::where('programme_id', $request->programme_id)
            ->where('year_of_study_id', $request->year_of_study_id)
            ->where('semester_id', $request->semester_id)
            ->with(['courseUnit', 'lecturer', 'day'])
            ->get();

        // Retrieve days for the timetable (assuming the days are predefined)
        $days = Day::where('id', '<=', 5)->get();

        // Generate the PDF
        $pdf = Pdf::loadView('timetable.pdf', compact(
            'programme',
            'timetable',
            'days',
            'yearOfStudy',
            'semester',
            'academicYear'
        ));

        // Export as a downloadable PDF
        return $pdf->download('timetable.pdf');
    }
}
