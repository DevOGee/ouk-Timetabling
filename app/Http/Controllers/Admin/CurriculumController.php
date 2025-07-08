<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Curriculum;
use App\Models\Lecturer;
use App\Models\Programme;
use App\Models\School;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurriculumController extends Controller
{
    /**
     * The number of items to show per page in pagination.
     */
    protected $perPage = 15;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('role:admin');
    }

    /**
     * Display a listing of the curricula for a specific academic session.
     */
    public function index(AcademicSession $academicSession)
    {
        $curricula = $academicSession->curricula()
            ->withCount('programmes')
            ->latest()
            ->paginate($this->perPage);
            
        // Manually add course_units_count to each curriculum
        $curricula->each(function($curriculum) {
            $curriculum->course_units_count = $curriculum->course_unit_count;
        });
        
        // Get the previous academic session for roll-forward functionality
        $previousSession = AcademicSession::where('end_date', '<', $academicSession->start_date)
            ->orderBy('end_date', 'desc')
            ->with(['curricula' => function($query) {
                $query->withCount('programmes');
            }])
            ->first();
            
        // Manually add course_units_count to each curriculum in the previous session
        if ($previousSession) {
            $previousSession->curricula->each(function($curriculum) {
                $curriculum->course_units_count = $curriculum->course_unit_count;
            });
        }
        
        return view('admin.curricula.index', [
            'academicSession' => $academicSession,
            'curricula' => $curricula,
            'previousSession' => $previousSession
        ]);
    }

    /**
     * Show the form for creating a new curriculum.
     */
    public function create(AcademicSession $academicSession)
    {
        $this->authorize('create', [Curriculum::class, $academicSession]);
        
        // Get all programmes for the form
        $programmes = Programme::orderBy('name')->get();
        
        return view('admin.curricula.form', [
            'academicSession' => $academicSession,
            'programmes' => $programmes,
            'curriculum' => null // New curriculum
        ]);
    }

    /**
     * Store a newly created curriculum in storage.
     */
    public function store(Request $request, AcademicSession $academicSession)
    {
        $this->authorize('create', [Curriculum::class, $academicSession]);
        
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('curricula')
                    ->where('academic_session_id', $academicSession->id)
            ],
            'description' => 'nullable|string',
            'programmes' => 'required|array|min:1',
            'programmes.*' => 'exists:programmes,id',
            'is_active' => 'sometimes|boolean',
        ], [
            'programmes.required' => 'Please select at least one programme for this curriculum.',
            'programmes.min' => 'Please select at least one programme for this curriculum.'
        ]);

        // Start a database transaction
        return DB::transaction(function () use ($academicSession, $validated, $request) {
            // Create the curriculum
            $curriculum = $academicSession->curricula()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => false, // Will be set to active below if needed
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Attach programmes without requiring course units
            if (isset($validated['programmes']) && is_array($validated['programmes'])) {
                // Use syncWithoutDetaching to avoid detaching existing relationships
                $curriculum->programmes()->syncWithoutDetaching($validated['programmes']);
            }

            // If this is the first curriculum in the session or explicitly set as active, activate it
            if (($validated['is_active'] ?? false) || $academicSession->curricula()->count() === 1) {
                $curriculum->activate();
            }

            return redirect()
                ->route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum])
                ->with('success', 'Curriculum created successfully.');
        });
    }

    /**
     * Display the specified curriculum.
     */
    public function show(AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('view', $curriculum);
        
        // Load the curriculum with related data
        $curriculum->load([
            'programmes',
            'createdBy',
            'updatedBy',
        ]);
        
        // Get the previous academic session for roll-forward functionality
        $previousSession = AcademicSession::where('end_date', '<', $academicSession->start_date)
            ->orderBy('end_date', 'desc')
            ->with(['curricula' => function($query) {
                $query->withCount('programmes');
            }])
            ->first();
            
        // Manually add course_units_count to each curriculum in the previous session
        if ($previousSession) {
            $previousSession->curricula->each(function($prevCurriculum) {
                $prevCurriculum->course_units_count = $prevCurriculum->course_unit_count;
            });
        }
        
        // Get counts for the dashboard
        $stats = [
            'programme_count' => $curriculum->programmes->count(),
            'course_unit_count' => $curriculum->courseUnitProgrammeMappings()
                ->distinct('course_unit_id')
                ->count('course_unit_id'),
            'lecturer_count' => $curriculum->courseUnitProgrammeMappings()
                ->whereNotNull('lecturer_id')
                ->distinct('lecturer_id')
                ->count('lecturer_id'),
        ];
        
        // Get all programmes for the curriculum with their course units
        $programmes = $curriculum->programmes()
            ->orderBy('name')
            ->get();
            
        // Get all years of study and semesters for the filters
        $years = YearOfStudy::orderBy('id')->get();
        $semesters = Semester::orderBy('id')->get();
        
        // Get recent activity (last 5 course unit additions/modifications)
        $recentActivity = $curriculum->courseUnitProgrammeMappings()
            ->with(['courseUnit', 'programme', 'lecturer'])
            ->latest()
            ->take(5)
            ->get();
            
        // Initialize variables for the curriculum summary
        $totalCourseUnits = 0;
        $coreCourseCount = 0;
        $electiveCourseCount = 0;
        
        return view('admin.curricula.show', [
            'academicSession' => $academicSession,
            'curriculum' => $curriculum,
            'programmes' => $programmes,
            'stats' => $stats,
            'years' => $years,
            'semesters' => $semesters,
            'recentActivity' => $recentActivity,
            'totalCourseUnits' => $totalCourseUnits,
            'coreCourseCount' => $coreCourseCount,
            'electiveCourseCount' => $electiveCourseCount,
            'previousSession' => $previousSession, // Add this line to pass the previous session to the view
        ]);
    }

    /**
     * Show the form for editing the specified curriculum.
     */
    public function edit(AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('update', $curriculum);
        
        // Get all programmes and pre-select the ones associated with this curriculum
        $programmes = Programme::orderBy('name')->get();
        
        // Load the associated programme IDs for the form
        $curriculum->load('programmes');
        
        return view('admin.curricula.form', [
            'academicSession' => $academicSession,
            'curriculum' => $curriculum,
            'programmes' => $programmes,
        ]);
    }

    /**
     * Update the specified curriculum in storage.
     */
    public function update(Request $request, AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('update', $curriculum);
        
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('curricula')
                    ->where('academic_session_id', $academicSession->id)
                    ->ignore($curriculum->id)
            ],
            'description' => 'nullable|string',
            'programmes' => 'required|array|min:1',
            'programmes.*' => 'exists:programmes,id',
            'is_active' => 'sometimes|boolean',
        ], [
            'programmes.required' => 'Please select at least one programme for this curriculum.',
            'programmes.min' => 'Please select at least one programme for this curriculum.'
        ]);
        
        // Handle activation/deactivation
        $shouldActivate = $validated['is_active'] ?? false;
        unset($validated['is_active']); // We'll handle this separately
        
        // Start a database transaction
        return DB::transaction(function () use ($academicSession, $curriculum, $validated, $shouldActivate) {
            // Update the curriculum
            $curriculum->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_by' => auth()->id(),
            ]);
            
            // Sync programmes
            if (isset($validated['programmes']) && is_array($validated['programmes'])) {
                $curriculum->programmes()->sync($validated['programmes']);
            }
            
            // Handle activation if requested
            if ($shouldActivate && !$curriculum->is_active) {
                $curriculum->activate();
            }
            
            return redirect()
                ->route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum])
                ->with('success', 'Curriculum updated successfully.');
        });
    }

    /**
     * Remove the specified curriculum from storage.
     */
    public function destroy(AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('delete', $curriculum);
        
        // Prevent deleting the only active curriculum in a session
        if ($curriculum->is_active && $academicSession->curricula()->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'Cannot delete the only active curriculum in the session.');
        }
        
        // Use a transaction to ensure data consistency
        DB::transaction(function () use ($curriculum) {
            // Detach all programmes first
            $curriculum->programmes()->detach();
            
            // Delete all related course unit programme mappings
            $curriculum->courseUnitProgrammeMappings()->delete();
            
            // Soft delete the curriculum
            $curriculum->update([
                'deleted_by' => auth()->id(),
            ]);
            
            $curriculum->delete();
        });
        
        return redirect()
            ->route('admin.academic-sessions.curricula.index', $academicSession)
            ->with('success', 'Curriculum deleted successfully.');
    }
    
    /**
     * Roll forward a curriculum to a new academic session.
     */
    public function rollForward(Request $request, AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('rollForward', $curriculum);
        
        // Validate the target academic session
        $validated = $request->validate([
            'target_academic_session_id' => [
                'required',
                'exists:academic_sessions,id',
                'different:current_academic_session_id',
            ],
            'include_course_units' => 'sometimes|boolean',
            'include_lecturers' => 'sometimes|boolean',
        ]);
        
        $targetAcademicSession = AcademicSession::findOrFail($validated['target_academic_session_id']);
        
        // Check if a curriculum with the same name already exists in the target session
        $existingCurriculum = $targetAcademicSession->curricula()
            ->where('name', $curriculum->name)
            ->first();
            
        if ($existingCurriculum) {
            return redirect()
                ->route('admin.academic-sessions.curricula.show', [$targetAcademicSession, $existingCurriculum])
                ->with('info', 'A curriculum with this name already exists in the target academic session.');
        }
        
        // Start a database transaction
        return DB::transaction(function () use ($academicSession, $targetAcademicSession, $curriculum, $validated) {
            // Create the new curriculum
            $newCurriculum = $targetAcademicSession->curricula()->create([
                'name' => $curriculum->name,
                'description' => $curriculum->description . ' (Rolled forward from ' . $academicSession->name . ')',
                'is_active' => false,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            
            // Copy programmes
            $programmeIds = $curriculum->programmes->pluck('id')->toArray();
            $newCurriculum->programmes()->sync($programmeIds);
            
            // Copy course unit mappings if requested
            if ($validated['include_course_units'] ?? false) {
                $mappings = $curriculum->courseUnitProgrammeMappings()
                    ->with(['courseUnit', 'programme', 'yearOfStudy', 'semester'])
                    ->get();
                
                foreach ($mappings as $mapping) {
                    $newMapping = $mapping->replicate();
                    $newMapping->curriculum_id = $newCurriculum->id;
                    $newMapping->created_by = auth()->id();
                    $newMapping->updated_by = auth()->id();
                    
                    // Only include lecturer if requested and exists
                    if (!($validated['include_lecturers'] ?? false) || !$mapping->lecturer_id) {
                        $newMapping->lecturer_id = null;
                    }
                    
                    $newMapping->save();
                }
            }
            
            // If this is the first curriculum in the target session, activate it
            if ($targetAcademicSession->curricula()->count() === 1) {
                $newCurriculum->activate();
            }
            
            return redirect()
                ->route('admin.academic-sessions.curricula.show', [$targetAcademicSession, $newCurriculum])
                ->with('success', 'Curriculum rolled forward successfully.');
        });
    }

    /**
     * Set the specified curriculum as active for its academic session.
     */
    public function setActive(AcademicSession $academicSession, Curriculum $curriculum)
    {
        $this->authorize('setActive', $curriculum);
        
        // Start a database transaction
        return DB::transaction(function () use ($academicSession, $curriculum) {
            // Activate the curriculum (this will deactivate others in the same session)
            $curriculum->activate();
            
            return redirect()
                ->route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum])
                ->with('success', 'Curriculum has been set as active for this academic session.');
        });
    }
}
