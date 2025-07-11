<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\School;
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
        
        // Manually check for admin role
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
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

    public function show(AcademicSession $academicSession)
    {
        // Get all schools that have programmes in this academic session
        $schools = School::with(['programmes' => function($query) use ($academicSession) {
            $query->whereHas('academicSessions', function($q) use ($academicSession) {
                $q->where('academic_sessions.id', $academicSession->id);
            });
        }])->get();

        $otherSessions = AcademicSession::where('id', '!=', $academicSession->id)
            ->orderBy('name', 'desc')
            ->get();
            
        // Check if current session has any programmes
        $hasProgrammes = $academicSession->programmes()->exists();

        $firstSchoolId = $schools->first() ? $schools->first()->id : null;
        
        // Get all programmes for this academic session with their school
        $programmes = $academicSession->programmes()
            ->with(['school'])
            ->get();

        // Manually count course units for each programme
        $programmes->each(function($programme) use ($academicSession) {
            // Count distinct course units for this programme in the current academic session
            $countQuery = \App\Models\CourseUnitProgrammeMapping::where('programme_id', $programme->id)
                ->where('academic_session_id', $academicSession->id)
                ->whereHas('courseUnit');
                
            $programme->mapped_course_units_count = $countQuery->distinct('course_unit_id')->count('course_unit_id');

            // Count incomplete mappings for this programme
            $programme->incomplete_mappings = \App\Models\CourseUnitProgrammeMapping::where('programme_id', $programme->id)
                ->where('academic_session_id', $academicSession->id)
                ->whereHas('courseUnit')
                ->where(function($query) {
                    $query->whereNull('user_id')
                          ->orWhereNull('day_id')
                          ->orWhereNull('morning_start_time')
                          ->orWhereNull('morning_duration')
                          ->orWhereNull('evening_start_time')
                          ->orWhereNull('evening_duration');
                })
                ->count();
                
            // Add has_timetable flag
            $programme->has_timetable = \App\Models\ProgrammeTimetable::where('programme_id', $programme->id)
                ->where('academic_session_id', $academicSession->id)
                ->exists();
            
            // Add timetable status
            $programme->timetable_status = $this->getTimetableStatus($programme);
        });

        // Get programmes that can have timetables added (have mappings but no timetable)
        $programmesWithoutTimetable = $academicSession->programmes()
            ->whereDoesntHave('programmeTimetables', function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            })
            ->whereHas('courseUnitMappings', function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            })
            ->get();
            
        // Get programmes with timetables
        $programmesWithTimetable = $academicSession->programmes()
            ->whereHas('programmeTimetables', function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            })
            ->with(['programmeTimetables' => function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            }])
            ->get();

        // Convert to paginator for consistent interface
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $perPage = 10;
        $programmes = new \Illuminate\Pagination\LengthAwarePaginator(
            $programmes->forPage($page, $perPage),
            $programmes->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        // For AJAX requests, handle filtering and pagination
        if (request()->ajax() && request()->has('tab')) {
            $tab = request('tab');
            
            if ($tab === 'timetables') {
                $filteredProgrammes = $programmes;
                
                if (request()->has('school_id') && request('school_id') !== 'all') {
                    $schoolId = request('school_id');
                    $filteredProgrammes = new \Illuminate\Pagination\LengthAwarePaginator(
                        $programmes->filter(function($programme) use ($schoolId) {
                            return $programme->school_id == $schoolId;
                        }),
                        $programmes->where('school_id', $schoolId)->count(),
                        $programmes->perPage(),
                        $programmes->currentPage(),
                        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                    );
                }
                
                return response()->json([
                    'html' => view('admin.academic-sessions.partials.timetables-table', [
                        'programmes' => $filteredProgrammes,
                        'academicSession' => $academicSession
                    ])->render(),
                    'pagination' => (string) $filteredProgrammes->links()
                ]);
            } else if ($tab === 'programmes') {
                $filteredProgrammes = $programmes;
                
                if (request()->has('school_id') && request('school_id') !== 'all') {
                    $schoolId = request('school_id');
                    $filteredProgrammes = new \Illuminate\Pagination\LengthAwarePaginator(
                        $programmes->filter(function($programme) use ($schoolId) {
                            return $programme->school_id == $schoolId;
                        }),
                        $programmes->where('school_id', $schoolId)->count(),
                        $programmes->perPage(),
                        $programmes->currentPage(),
                        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                    );
                }
                
                return response()->json([
                    'html' => view('admin.academic-sessions.partials.programmes-table', [
                        'programmes' => $filteredProgrammes,
                        'academicSession' => $academicSession
                    ])->render(),
                    'pagination' => (string) $filteredProgrammes->links()
                ]);
            }
        }
        
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
            'programmes' => $programmes->isEmpty() ? collect() : $programmes,
            'programmesWithoutTimetable' => $programmesWithoutTimetable,
            'programmesWithTimetable' => $programmesWithTimetable,
            'programmesForTimetable' => $programmesForTimetable,
            'schools' => $schools,
            'selectedSchool' => $firstSchoolId,
            'otherSessions' => $otherSessions,
            'hasProgrammes' => $hasProgrammes
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
