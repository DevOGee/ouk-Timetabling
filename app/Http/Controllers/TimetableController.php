<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\LessonSlot;
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
        // $days = Day::all();
        $days = Day::where('id', '<=', 5)->get();

        $timetable = collect();

        if ($request->has(['school_id', 'programme_id', 'year_of_study_id', 'semester_id'])) {
            $timetable = LessonSlot::whereHas('courseUnit', function ($query) use ($request) {
                $query->where('year_of_study_id', $request->year_of_study_id)
                    ->where('semester_id', $request->semester_id)
                    ->whereHas('programmes', function ($query) use ($request) {
                        $query->where('programme_id', $request->programme_id);
                    });
            })->with(['courseUnit', 'courseUnit.instructors', 'day'])->get();
        }

        return view('timetable.index', compact('schools', 'programmes', 'years', 'semesters', 'days', 'timetable'));
    }

    public function exportPDF(Request $request)
    {
        $schools = School::all();
        $programmes = Programme::all();
        $years = YearOfStudy::all();
        $semesters = Semester::all();
        $days = Day::where('id', '<=', 5)->get();

        $programme = Programme::findOrFail($request->programme_id);

        $timetable = LessonSlot::whereHas('courseUnit', function ($query) use ($request) {
            $query->where('year_of_study_id', $request->year_of_study_id)
                ->where('semester_id', $request->semester_id)
                ->whereHas('programmes', function ($query) use ($request) {
                    $query->where('programme_id', $request->programme_id);
                });
        })->with(['courseUnit', 'courseUnit.instructors', 'day'])->get();

        $pdf = Pdf::loadView('timetable.pdf', compact('schools', 'programmes', 'years', 'semesters', 'days', 'timetable', 'programme'))
            ->setPaper('A4', orientation: 'portrait');

        return $pdf->download('timetable.pdf');
    }
}
