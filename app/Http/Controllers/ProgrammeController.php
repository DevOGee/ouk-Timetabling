<?php

namespace App\Http\Controllers;

use App\Models\CourseUnit;
use App\Models\Day;
use App\Models\Lecturer;
use App\Models\Programme;
use App\Models\School;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function index()
    {
        $programmes = Programme::with('school')->get();

        return view('programmes.index', compact('programmes'));
    }

    public function create()
    {
        $schools = School::all();

        return view('programmes.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|unique:programmes,name',
            'programme_code' => 'required|unique:programmes,programme_code|max:10',
        ]);

        Programme::create($request->all());

        return redirect()->route('programmes.index')->with('success', 'Programme added successfully.');
    }

    public function edit(Programme $programme)
    {
        $schools = School::all();

        return view('programmes.edit', compact('programme', 'schools'));
    }

    public function update(Request $request, Programme $programme)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|unique:programmes,name,'.$programme->id,
            'programme_code' => 'required|unique:programmes,programme_code,'.$programme->id.'|max:10',
        ]);

        $programme->update($request->all());

        return redirect()->route('programmes.index')->with('success', 'Programme updated successfully.');
    }

    public function destroy(Programme $programme)
    {
        $programme->delete();

        return redirect()->route('programmes.index')->with('success', 'Programme deleted successfully.');
    }

    public function show(Programme $programme)
    {
        $courseUnits = CourseUnit::whereDoesntHave('programmes', function ($query) use ($programme) {
            $query->where('programme_id', $programme->id);
        })->get();
        $lecturers = Lecturer::all();
        $days = Day::all();

        return view('programmes.show', compact('programme', 'courseUnits', 'lecturers', 'days'));
    }

    public function addCourseUnit(Request $request, Programme $programme)
    {
        $request->validate([
            'course_unit_id' => 'required|array',
            'course_unit_id.*' => 'exists:course_units,id', // Validate each selected ID
        ]);

        // Filter out existing course units to avoid duplicates
        $courseUnitsToAdd = collect($request->course_unit_id)
            ->filter(fn ($id) => ! $programme->courseUnits()->where('course_unit_id', $id)->exists());

        if ($courseUnitsToAdd->isNotEmpty()) {
            $programme->courseUnits()->attach($courseUnitsToAdd);
        }

        return redirect()->route('programmes.show', $programme)->with(
            'success',
            count($courseUnitsToAdd).' Course Unit(s) added to Programme successfully.'
        );
    }

    public function removeCourseUnit(Programme $programme, CourseUnit $courseUnit)
    {
        $programme->courseUnits()->detach($courseUnit);

        return redirect()->route('programmes.show', $programme)->with('success', 'Course Unit removed from Programme successfully.');
    }

    public function addInstructor(Request $request, Programme $programme, CourseUnit $courseUnit)
    {
        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
        ]);

        if (! $courseUnit->instructors()->wherePivot('programme_id', $programme->id)->where('lecturer_id', $request->lecturer_id)->exists()) {
            $courseUnit->instructors()->attach($request->lecturer_id, ['programme_id' => $programme->id]);
        }

        return redirect()->route('programmes.show', $programme)->with('success', 'Instructor assigned successfully.');
    }

    public function removeInstructor(Programme $programme, CourseUnit $courseUnit, Lecturer $lecturer)
    {
        $courseUnit->instructors()->wherePivot('programme_id', $programme->id)->detach($lecturer);

        return redirect()->route('programmes.show', $programme)->with('success', 'Instructor removed successfully.');
    }
}
