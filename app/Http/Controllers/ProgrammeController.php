<?php

namespace App\Http\Controllers;

use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Day;
use App\Models\User;
use App\Models\Programme;
use App\Models\School;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgrammeController extends Controller
{
    public function index()
    {
        $schools = School::with('departments.programmes')->get();
        $programmes = Programme::with('department.school')->get();

        return view('programmes.index', compact('programmes', 'schools'));
    }

    public function create()
    {
        $departments = Department::with('school')->get()->sortBy(['school.name', 'name']);

        return view('programmes.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|unique:programmes,name',
            'programme_code' => 'required|unique:programmes,programme_code|max:10',
            'has_specialisations' => 'sometimes|boolean',
        ]);

        $validated['has_specialisations'] = $request->has('has_specialisations');

        Programme::create($validated);

        return redirect()->route('programmes.index')->with('success', 'Programme added successfully.');
    }

    public function edit(Programme $programme)
    {
        $departments = Department::with('school')->get()->sortBy(['school.name', 'name']);

        return view('programmes.edit', compact('programme', 'departments'));
    }

    public function update(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|unique:programmes,name,'.$programme->id,
            'programme_code' => 'required|unique:programmes,programme_code,'.$programme->id.'|max:10',
            'has_specialisations' => 'sometimes|boolean',
        ]);

        $validated['has_specialisations'] = $request->has('has_specialisations');

        $programme->update($validated);

        return redirect()->route('programmes.index')->with('success', 'Programme updated successfully.');
    }

    public function destroy(Programme $programme)
    {
        $programme->delete();

        return redirect()->route('programmes.index')->with('success', 'Programme deleted successfully.');
    }

    // public function show(Programme $programme)
    // {
    //     $courseUnits = CourseUnit::whereDoesntHave('programmes', function ($query) use ($programme) {
    //         $query->where('programme_id', $programme->id);
    //     })->get();
    //     $lecturers = Lecturer::all();
    //     $days = Day::all();

    //     return view('programmes.show', compact('programme', 'courseUnits', 'lecturers', 'days'));
    // }

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

    public function getProgrammes(Request $request)
    {
        $schoolId = $request->school_id;
        $programmes = Programme::where('school_id', $schoolId)->get(['id', 'name']);

        return response()->json($programmes);
    }

    public function show(Programme $programme, CourseUnit $courseUnit = null, User $user = null)
    {
        // Fetch unmapped courses
        $courseUnits = CourseUnit::whereDoesntHave('programmeMappings', function ($query) use ($programme) {
            $query->where('programme_id', $programme->id);
        })->get();

        // Get all users with instructor role
        $instructors = User::role('instructor')->with('title')->get();
        $days = Day::all();

        $groupedMappings = $programme->courseUnitMappings()
            ->with(['courseUnit', 'instructor', 'day', 'yearOfStudy', 'semester'])
            ->get()
            ->sortBy(fn ($m) => $m->courseUnit->code)
            ->groupBy(function ($m) {
                return 'Year '.$m->yearOfStudy->name.' - Semester '.$m->semester->name;
            });

        return view('programmes.show', [
            'programme' => $programme, 
            'courseUnits' => $courseUnits, 
            'instructors' => $instructors, 
            'days' => $days, 
            'groupedMappings' => $groupedMappings,
            'selectedCourseUnit' => $courseUnit,
            'selectedUser' => $user
        ]);
    }

    /**
     * Assign an instructor to a course unit in a programme.
     */
    public function addInstructor(Request $request, Programme $programme, CourseUnit $courseUnit)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Verify the user has the instructor role
        $user = User::findOrFail($request->user_id);
        if (!$user->hasRole('instructor')) {
            return redirect()->back()->with('error', 'Selected user is not an instructor.');
        }

        $mapping = CourseUnitProgrammeMapping::where('programme_id', $programme->id)
            ->where('course_unit_id', $courseUnit->id)
            ->first();

        if ($mapping) {
            $mapping->update(['user_id' => $user->id]);
        } else {
            // Create a new mapping if one doesn't exist
            CourseUnitProgrammeMapping::create([
                'programme_id' => $programme->id,
                'course_unit_id' => $courseUnit->id,
                'user_id' => $user->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('programmes.show', $programme)->with('success', 'Instructor assigned successfully.');
    }

    /**
     * Remove an instructor from a course unit in a programme.
     */
    public function removeInstructor(Programme $programme, CourseUnit $courseUnit, User $user)
    {
        $mapping = CourseUnitProgrammeMapping::where('programme_id', $programme->id)
            ->where('course_unit_id', $courseUnit->id)
            ->where('user_id', $user->id)
            ->first();

        if ($mapping) {
            $mapping->update(['user_id' => null]);
        }

        // Redirect back to the admin programme show page
    return redirect()->route('admin.programmes.show', $programme)->with('success', 'Instructor removed successfully.');
    }
}
