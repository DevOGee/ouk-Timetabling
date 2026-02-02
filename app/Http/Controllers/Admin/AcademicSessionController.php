<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\School;
use App\Models\Department;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        // Check for admin or school_timetabler role
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !(Auth::user()->hasRole('admin') || Auth::user()->hasRole('timetabler'))) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $sessions = AcademicSession::latest()->paginate(10);
        return view('admin.academic-sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('admin.academic-sessions.create');
    }

    public function curriculumMapping()
    {
        $session = AcademicSession::where('is_current', true)->first() ?? AcademicSession::latest()->first();
        if (!$session) {
            return redirect()->route('admin.academic-sessions.index')->with('error', 'No academic session found.');
        }
        return $this->show($session, 'curriculum');
    }
    public function showCurriculum(AcademicSession $academicSession)
    {
        return $this->show($academicSession, "curriculum");
    }


    public function teachingAllocation()
    {
        $session = AcademicSession::where('is_current', true)->first() ?? AcademicSession::latest()->first();
        if (!$session) {
            return redirect()->route('admin.academic-sessions.index')->with('error', 'No academic session found.');
        }
        return $this->show($session, 'allocation');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:academic_sessions,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_current' => 'boolean',
        ]);

        $validated['status'] = $request->start_date <= now() && $request->end_date >= now() 
            ? 'active' 
            : ($request->start_date > now() ? 'upcoming' : 'completed');

        $session = AcademicSession::create($validated);

        return redirect()
            ->route('admin.academic-sessions.index')
            ->with('success', 'Academic session created successfully');
    }

    public function show(AcademicSession $academicSession, $viewMode = null)
    {
        $user = auth()->user();
        
        // Get filter parameters
        $departmentId = request('department_id');
        $search = request('search');
        
        // Get departments for filter dropdown
        $departmentsQuery = Department::query();
        
        // If user is a school_timetabler, only show their school's departments
        if ($user->hasRole('school_timetabler')) {
            $departmentsQuery->where('school_id', $user->school_id);
        }
        
        $departments = $departmentsQuery->with('school')->orderBy('name')->get();

        $otherSessions = AcademicSession::where('id', '!=', $academicSession->id)
            ->orderBy('name', 'desc')
            ->get();
            
        // Check if current session has any programmes
        $hasProgrammes = $academicSession->programmes()->exists();
        
        // Get all programmes for this academic session with their school and department
        // Use subqueries to avoid N+1 queries
        $programmesQuery = $academicSession->programmes()
            ->with(['school', 'department']);
            
        // If user is a timetabler, only show programmes from their school
        if ($user->hasRole('timetabler') || $user->hasRole('school_timetabler')) {
            $programmesQuery->where('school_id', $user->school_id);
        }
        
        // Apply department filter
        if ($departmentId && $departmentId !== 'all') {
            $programmesQuery->where('department_id', $departmentId);
        }
        
        // Apply search filter
        if ($search) {
            $programmesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('programme_code', 'like', '%' . $search . '%');
            });
        }
        
        // Add subqueries for counts
        $programmesQuery->addSelect([
            'programmes.*',
            'mapped_course_units_count' => \DB::table('course_unit_programme_mappings')
                ->selectRaw('COUNT(DISTINCT course_unit_id)')
                ->whereColumn('programme_id', 'programmes.id')
                ->where('academic_session_id', $academicSession->id),
            'incomplete_mappings' => \DB::table('course_unit_programme_mappings')
                ->selectRaw('COUNT(*)')
                ->whereColumn('programme_id', 'programmes.id')
                ->where('academic_session_id', $academicSession->id)
                ->where(function($q) {
                    $q->whereNull('user_id')
                      ->orWhereNull('day_id')
                      ->orWhereNull('morning_start_time')
                      ->orWhereNull('morning_duration')
                      ->orWhereNull('evening_start_time')
                      ->orWhereNull('evening_duration');
                }),
            'has_timetable' => \App\Models\ProgrammeTimetable::selectRaw('COUNT(*) > 0')
                ->whereColumn('programme_id', 'programmes.id')
                ->where('academic_session_id', $academicSession->id)
                ->limit(1)
        ]);
        
        // Sort by department name, then programme code
        $programmesQuery->leftJoin('departments', 'programmes.department_id', '=', 'departments.id')
            ->orderBy('departments.name', 'asc')
            ->orderBy('programmes.programme_code', 'asc');
        
        // Get all programmes (no pagination)
        $programmes = $programmesQuery->get();


        // Add timetable status (lightweight operation)
        $programmes->each(function($programme) {
            $programme->timetable_status = $this->getTimetableStatus($programme);
        });

        // Get programmes that can have timetables added (have mappings but no timetable)
        $programmesForTimetable = $academicSession->programmes()
            ->whereDoesntHave('programmeTimetables', function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            })
            ->whereHas('courseUnitMappings', function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            })
            ->with('school')
            ->orderBy('name')
            ->get();

        return view('admin.academic-sessions.show', [
            'academicSession' => $academicSession,
            'programmes' => $programmes,
            'programmesForTimetable' => $programmesForTimetable,
            'departments' => $departments,
            'selectedDepartment' => $departmentId,
            'searchTerm' => $search,
            'otherSessions' => $otherSessions,
            'hasProgrammes' => $hasProgrammes,
            'viewMode' => $viewMode
        ]);
    }
    
    /**
     * Get timetable status for a programme based on course unit mappings
     * 
     * Status is determined by checking if all required fields are filled for each course unit:
     * - Done: All required fields are filled for all course units
     * - Pending: Some required fields are filled for some course units
     * - Not Started: No required fields are filled for any course unit
     */
    protected function getTimetableStatus($programme)
    {
        $timetable = $programme->timetable;
        
        if (!$timetable) {
            return [
                'status' => 'not_created',
                'label' => 'Not Created',
                'class' => 'secondary'
            ];
        }
        
        if ($timetable->status === 'published') {
            return [
                'status' => 'published',
                'label' => 'Published',
                'class' => 'success',
                'published_at' => $timetable->published_at,
                'published_by' => $timetable->published_by
            ];
        }
        
        return [
            'status' => 'draft',
            'label' => 'Draft',
            'class' => 'warning'
        ];
    }

    /**
     * Copy programme and timetable data from one academic session to another
     */
    public function copyMappings(Request $request, AcademicSession $academicSession)
    {
        \Log::info('Starting copyMappings', [
            'target_session_id' => $academicSession->id,
            'source_session_id' => $request->input('source_session_id'),
            'all_input' => $request->all()
        ]);

        $request->validate([
            'source_session_id' => ['required', 'exists:academic_sessions,id', 
                function ($attribute, $value, $fail) use ($academicSession) {
                    if ($value == $academicSession->id) {
                        $fail('Source session cannot be the same as the target session.');
                    }
                }
            ]
        ]);

        $sourceSessionId = $request->input('source_session_id');
        
        // Begin database transaction
        DB::beginTransaction();
        
        try {
            $now = now();
            
            // 1. Copy programme mappings (academic_session_programme)
            \Log::info('Fetching source programme mappings', ['source_session_id' => $sourceSessionId]);
            
            $sourceProgrammeMappings = DB::table('academic_session_programme')
                ->where('academic_session_id', $sourceSessionId)
                ->get();
                
            \Log::info('Found source programme mappings', ['count' => $sourceProgrammeMappings->count()]);
                
            $programmeMappingsToInsert = [];
            $skippedProgrammeMappings = 0;
            
            foreach ($sourceProgrammeMappings as $mapping) {
                // Check if this mapping already exists in the target session
                $exists = DB::table('academic_session_programme')
                    ->where('academic_session_id', $academicSession->id)
                    ->where('programme_id', $mapping->programme_id)
                    ->exists();
                    
                if (!$exists) {
                    $programmeMappingsToInsert[] = [
                        'academic_session_id' => $academicSession->id,
                        'programme_id' => $mapping->programme_id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                } else {
                    $skippedProgrammeMappings++;
                }
            }
            
            \Log::info('Programme mappings to insert', [
                'to_insert' => count($programmeMappingsToInsert),
                'skipped' => $skippedProgrammeMappings
            ]);
            
            // Insert new programme mappings in bulk
            if (!empty($programmeMappingsToInsert)) {
                $inserted = DB::table('academic_session_programme')->insert($programmeMappingsToInsert);
                \Log::info('Inserted programme mappings', ['success' => $inserted]);
            }
            
            // 2. Copy programme timetables (programme_timetable)
            \Log::info('Fetching source timetables', ['source_session_id' => $sourceSessionId]);
            
            $sourceTimetables = DB::table('programme_timetable')
                ->where('academic_session_id', $sourceSessionId)
                ->get();
                
            \Log::info('Found source timetables', ['count' => $sourceTimetables->count()]);
                
            $timetablesToInsert = [];
            $skippedTimetables = 0;
            $programmeNotInSession = 0;
            
            foreach ($sourceTimetables as $timetable) {
                // Only copy if the programme is in the target session
                $programmeInSession = DB::table('academic_session_programme')
                    ->where('academic_session_id', $academicSession->id)
                    ->where('programme_id', $timetable->programme_id)
                    ->exists();
                    
                if ($programmeInSession) {
                    // Check if this timetable already exists in the target session
                    $exists = DB::table('programme_timetable')
                        ->where('academic_session_id', $academicSession->id)
                        ->where('programme_id', $timetable->programme_id)
                        ->exists();
                        
                    if (!$exists) {
                        $timetablesToInsert[] = [
                            'academic_session_id' => $academicSession->id,
                            'programme_id' => $timetable->programme_id,
                            'status' => $timetable->status ?? 'active',
                            'created_at' => $now,
                            'updated_at' => $now
                        ];
                    } else {
                        $skippedTimetables++;
                    }
                } else {
                    $programmeNotInSession++;
                }
            }
            
            \Log::info('Timetables processing summary', [
                'to_insert' => count($timetablesToInsert),
                'skipped_duplicates' => $skippedTimetables,
                'skipped_programme_not_in_session' => $programmeNotInSession
            ]);
            
            // Insert new timetables in bulk
            if (!empty($timetablesToInsert)) {
                $inserted = DB::table('programme_timetable')->insert($timetablesToInsert);
                \Log::info('Inserted timetables', ['success' => $inserted]);
            }
            
            // 3. Copy course unit programme mappings (this contains the actual timetable data)
            \Log::info('Fetching source course unit programme mappings', ['source_session_id' => $sourceSessionId]);
            
            $sourceCourseUnitMappings = DB::table('course_unit_programme_mappings')
                ->where('academic_session_id', $sourceSessionId)
                ->get();
                
            \Log::info('Found source course unit programme mappings', ['count' => $sourceCourseUnitMappings->count()]);
                
            $mappingsToInsert = [];
            $skippedMappings = 0;
            $programmeNotInSession = 0;
            
            foreach ($sourceCourseUnitMappings as $mapping) {
                // Only copy if the programme is in the target session
                $programmeInSession = DB::table('academic_session_programme')
                    ->where('academic_session_id', $academicSession->id)
                    ->where('programme_id', $mapping->programme_id)
                    ->exists();
                    
                if ($programmeInSession) {
                    // Check if this mapping already exists in the target session
                    $exists = DB::table('course_unit_programme_mappings')
                        ->where('academic_session_id', $academicSession->id)
                        ->where('programme_id', $mapping->programme_id)
                        ->where('course_unit_id', $mapping->course_unit_id)
                        ->where('year_of_study_id', $mapping->year_of_study_id)
                        ->where('semester_id', $mapping->semester_id)
                        ->exists();
                        
                    if (!$exists) {
                        $mappingsToInsert[] = [
                            'academic_session_id' => $academicSession->id,
                            'programme_id' => $mapping->programme_id,
                            'course_unit_id' => $mapping->course_unit_id,
                            'year_of_study_id' => $mapping->year_of_study_id,
                            'semester_id' => $mapping->semester_id,
                            'user_id' => null,
                            'day_id' => null,
                            'morning_start_time' => null,
                            'morning_duration' => null,
                            'evening_start_time' => null,
                            'evening_duration' => null,
                            'created_by' => auth()->id(),
                            'created_at' => $now,
                            'updated_at' => $now
                        ];
                    } else {
                        $skippedMappings++;
                    }
                } else {
                    $programmeNotInSession++;
                }
            }
            
            // Insert new course unit programme mappings in bulk
            if (!empty($mappingsToInsert)) {
                $inserted = DB::table('course_unit_programme_mappings')->insert($mappingsToInsert);
                \Log::info('Inserted course unit programme mappings', [
                    'success' => $inserted,
                    'inserted_count' => count($mappingsToInsert)
                ]);
            }
            
            DB::commit();
            
            \Log::info('Successfully copied all data', [
                'target_session_id' => $academicSession->id,
                'source_session_id' => $sourceSessionId,
                'programmes_copied' => count($programmeMappingsToInsert),
                'timetables_copied' => count($timetablesToInsert),
                'course_unit_mappings_copied' => count($mappingsToInsert)
            ]);
            
            return redirect()
                ->back()
                ->with('success', 'Programme mappings, timetables, and course unit mappings copied successfully from the selected session.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Error copying session data: ' . $e->getMessage();
            \Log::error($errorMessage, [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }
    }

    public function edit(AcademicSession $academicSession)
    {
        return view('admin.academic-sessions.edit', compact('academicSession'));
    }

    public function update(Request $request, AcademicSession $academicSession)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('academic_sessions')->ignore($academicSession->id)
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_current' => 'boolean',
            'status' => 'required|in:upcoming,active,completed,archived',
        ]);

        $academicSession->update($validated);

        return redirect()
            ->route('admin.academic-sessions.index')
            ->with('success', 'Academic session updated successfully');
    }

    public function destroy(AcademicSession $academicSession)
    {
        if ($academicSession->programmeTimetables()->exists()) {
            return back()->with('error', 'Cannot delete session with associated timetables');
        }

        $academicSession->delete();

        return redirect()
            ->route('admin.academic-sessions.index')
            ->with('success', 'Academic session deleted successfully');
    }

    public function setCurrent(AcademicSession $academicSession)
    {
        $academicSession->update(['is_current' => true]);
        
        return back()->with('success', 'Current academic session updated');
    }

    public function archive(AcademicSession $academicSession)
    {
        $academicSession->update(['status' => 'archived']);
        
        return back()->with('success', 'Academic session archived');
    }
}
