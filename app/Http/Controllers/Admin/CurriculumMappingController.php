<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Curriculum;
use App\Models\Lecturer;
use App\Models\Programme;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CurriculumMappingController extends Controller
{
    /**
     * The number of items to show per page in pagination.
     */
    protected $perPage = 20;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('role:admin');
    }

    /**
     * Display a listing of the course unit mappings for a specific curriculum.
     */
    public function index(AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('view', $curriculum);
        
        // Get filter parameters
        $filters = [
            'programme_id' => request('programme_id'),
            'year_of_study_id' => request('year_of_study_id'),
            'semester_id' => request('semester_id'),
            'search' => request('search'),
        ];
        
        // Build the query
        $query = $curriculum->courseUnitProgrammeMappings()
            ->with(['courseUnit', 'programme', 'yearOfStudy', 'semester', 'lecturer']);
        
        // Apply filters
        if ($filters['programme_id']) {
            $query->where('programme_id', $filters['programme_id']);
        }
        
        if ($filters['year_of_study_id']) {
            $query->where('year_of_study_id', $filters['year_of_study_id']);
        }
        
        if ($filters['semester_id']) {
            $query->where('semester_id', $filters['semester_id']);
        }
        
        if ($filters['search']) {
            $search = $filters['search'];
            $query->whereHas('courseUnit', function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Get paginated results
        $mappings = $query->orderBy('programme_id')
            ->orderBy('year_of_study_id')
            ->orderBy('semester_id')
            ->orderBy('course_unit_id')
            ->paginate($this->perPage)
            ->withQueryString();
        
        // Get filter options
        $programmes = $curriculum->programmes()->orderBy('name')->get();
        $years = YearOfStudy::orderBy('id')->get();
        $semesters = Semester::orderBy('id')->get();
        $lecturers = Lecturer::orderBy('name')->get();
        
        return view('admin.curricula.mappings.index', [
            'academicSession' => $academicSession,
            'curriculum' => $curriculum,
            'mappings' => $mappings,
            'programmes' => $programmes,
            'years' => $years,
            'semesters' => $semesters,
            'lecturers' => $lecturers,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new course unit mapping.
     */
    public function create(AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('update', $curriculum);
        
        // Get available programmes for this curriculum
        $programmes = $curriculum->programmes()
            ->orderBy('name')
            ->get();
            
        // Get all programmes if none are associated yet
        if ($programmes->isEmpty()) {
            $programmes = Programme::orderBy('name')->get();
        }
        
        // Get course units that can be added to this curriculum
        $courseUnits = CourseUnit::orderBy('code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => "{$item->code} - {$item->name}"];
            });
        
        return view('admin.curricula.mappings.create', [
            'academicSession' => $academicSession,
            'curriculum' => $curriculum,
            'programmes' => $programmes,
            'courseUnits' => $courseUnits,
            'years' => YearOfStudy::orderBy('id')->get(),
            'semesters' => Semester::orderBy('id')->get(),
            'lecturers' => Lecturer::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created course unit mapping in storage.
     */
    public function store(Request $request, AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('update', $curriculum);
        
        $validated = $request->validate([
            'programme_id' => [
                'required',
                'exists:programmes,id',
                function ($attribute, $value, $fail) use ($curriculum) {
                    // Verify the programme is associated with the curriculum
                    if (!$curriculum->programmes()->where('id', $value)->exists()) {
                        $fail('The selected programme is not associated with this curriculum.');
                    }
                },
            ],
            'course_unit_id' => [
                'required',
                'exists:course_units,id',
                // Check for duplicate mapping in this curriculum
                Rule::unique('course_unit_programme_mappings')
                    ->where('programme_id', $request->programme_id)
                    ->where('year_of_study_id', $request->year_of_study_id)
                    ->where('semester_id', $request->semester_id)
                    ->where('curriculum_id', $curriculum->id)
            ],
            'year_of_study_id' => 'required|exists:years_of_study,id',
            'semester_id' => 'required|exists:semesters,id',
            'lecturer_id' => 'nullable|exists:lecturers,id',
            'is_elective' => 'boolean',
            'max_students' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Add the curriculum ID and created_by user
        $validated['curriculum_id'] = $curriculum->id;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        
        // Create the mapping
        $mapping = CourseUnitProgrammeMapping::create($validated);
        
        return redirect()
            ->route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum])
            ->with('success', 'Course unit added to curriculum successfully.');
    }

    /**
     * Show the form for editing the specified course unit mapping.
     */
    public function edit(AcademicSession $academicSession, Curriculum $curriculum, CourseUnitProgrammeMapping $mapping)
    {
        $this->authorize('update', $curriculum);
        
        // Ensure the mapping belongs to the curriculum
        if ($mapping->curriculum_id !== $curriculum->id) {
            abort(404);
        }
        
        // Get available programmes for this curriculum
        $programmes = $curriculum->programmes()
            ->orderBy('name')
            ->get();
        
        // Get course units for the programme
        $courseUnits = CourseUnit::orderBy('code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => "{$item->code} - {$item->name}"];
            });
        
        return view('admin.curricula.mappings.edit', [
            'academicSession' => $academicSession,
            'curriculum' => $curriculum,
            'mapping' => $mapping,
            'programmes' => $programmes,
            'courseUnits' => $courseUnits,
            'years' => YearOfStudy::orderBy('id')->get(),
            'semesters' => Semester::orderBy('id')->get(),
            'lecturers' => Lecturer::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified course unit mapping in storage.
     */
    public function update(Request $request, AcademicSession $academicSession, Curriculum $curriculum, CourseUnitProgrammeMapping $mapping)
    {
        $this->authorize('update', $curriculum);
        
        // Ensure the mapping belongs to the curriculum
        if ($mapping->curriculum_id !== $curriculum->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'programme_id' => [
                'required',
                'exists:programmes,id',
                function ($attribute, $value, $fail) use ($curriculum) {
                    // Verify the programme is associated with the curriculum
                    if (!$curriculum->programmes()->where('id', $value)->exists()) {
                        $fail('The selected programme is not associated with this curriculum.');
                    }
                },
            ],
            'course_unit_id' => [
                'required',
                'exists:course_units,id',
                // Check for duplicate mapping in this curriculum, excluding the current one
                Rule::unique('course_unit_programme_mappings')
                    ->where('programme_id', $request->programme_id)
                    ->where('year_of_study_id', $request->year_of_study_id)
                    ->where('semester_id', $request->semester_id)
                    ->where('curriculum_id', $curriculum->id)
                    ->ignore($mapping->id)
            ],
            'year_of_study_id' => 'required|exists:years_of_study,id',
            'semester_id' => 'required|exists:semesters,id',
            'lecturer_id' => 'nullable|exists:lecturers,id',
            'is_elective' => 'boolean',
            'max_students' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Update the mapping
        $mapping->update(array_merge($validated, [
            'updated_by' => auth()->id(),
        ]));
        
        return redirect()
            ->route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum])
            ->with('success', 'Course unit mapping updated successfully.');
    }

    /**
     * Remove the specified course unit mapping from storage.
     */
    public function destroy(AcademicSession $academicSession, Curriculum $curriculum, CourseUnitProgrammeMapping $mapping)
    {
        $this->authorize('update', $curriculum);
        
        // Ensure the mapping belongs to the curriculum
        if ($mapping->curriculum_id !== $curriculum->id) {
            abort(404);
        }
        
        $mapping->delete();
        
        return redirect()
            ->route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum])
            ->with('success', 'Course unit removed from curriculum successfully.');
    }
    
    /**
     * Get course units for a specific programme in the curriculum.
     * Used for AJAX requests.
     */
    public function getProgrammeCourseUnits(AcademicSession $academicSession, Curriculum $curriculum, Programme $programme)
    {
        $this->authorize('view', $curriculum);
        
        // Verify the programme is associated with the curriculum
        if (!$curriculum->programmes()->where('id', $programme->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'The selected programme is not associated with this curriculum.'
            ], 422);
        }
        
        // Get course units already mapped to this programme in this curriculum
        $mappedCourseUnitIds = $curriculum->courseUnitProgrammeMappings()
            ->where('programme_id', $programme->id)
            ->pluck('course_unit_id');
        
        // Get available course units (not yet mapped to this programme in this curriculum)
        $courseUnits = CourseUnit::whereNotIn('id', $mappedCourseUnitIds)
            ->orderBy('code')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->code} - {$item->name}"
                ];
            });
        
        return response()->json([
            'success' => true,
            'course_units' => $courseUnits
        ]);
    }
}
