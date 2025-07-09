<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
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
        $schools = \App\Models\School::whereHas('programmes', function($query) use ($academicSession) {
            $query->whereHas('academicSessions', function($q) use ($academicSession) {
                $q->where('academic_session_id', $academicSession->id);
            });
        })->orderBy('name')->get();

        // Get the first school ID for default selection
        $firstSchoolId = $schools->first() ? $schools->first()->id : null;
        
        // Base query for programmes in this academic session
        $programmesQuery = $academicSession->programmes()
            ->with(['school', 'courseUnitMappings' => function($q) use ($academicSession) {
                $q->where('academic_session_id', $academicSession->id);
            }]);
            
        // Base query for programmes in this academic session with timetable status
        $timetableQuery = $academicSession->programmes()
            ->with(['school'])
            ->withCount([
                'courseUnitMappings as course_units_count' => function($q) use ($academicSession) {
                    $q->where('academic_session_id', $academicSession->id);
                },
                'courseUnitMappings as incomplete_mappings' => function($q) use ($academicSession) {
                    $q->where('academic_session_id', $academicSession->id)
                      ->where(function($query) {
                          $query->whereNull('user_id')
                                ->orWhereNull('day_id')
                                ->orWhereNull('morning_start_time')
                                ->orWhereNull('morning_duration')
                                ->orWhereNull('evening_start_time')
                                ->orWhereNull('evening_duration');
                      });
                }
            ])
            ->select('programmes.*')
            ->addSelect([
                'has_timetable' => \App\Models\Timetable::selectRaw('COUNT(*)')
                    ->join('programme_timetable', 'timetables.id', '=', 'programme_timetable.timetable_id')
                    ->whereColumn('programme_timetable.programme_id', 'programmes.id')
                    ->where('timetables.academic_session_id', $academicSession->id)
            ]);
            
        // For AJAX requests, handle filtering and pagination
        if (request()->ajax() && request()->has('tab')) {
            $tab = request('tab');
            
            if ($tab === 'timetables') {
                if (request()->has('school_id') && request('school_id') !== 'all') {
                    $timetableQuery->where('school_id', request('school_id'));
                }
                
                $programmes = $timetableQuery->paginate(10);
                
                // Eager load timetables with pivot data for the current academic session
                $programmes->load(['timetables' => function($query) use ($academicSession) {
                    $query->withPivot(['status', 'published_at', 'academic_session_id'])
                          ->wherePivot('academic_session_id', $academicSession->id);
                }]);
                
                // Ensure each programme has the timetables relationship properly loaded
                $programmes->each(function($programme) use ($academicSession) {
                    if (!$programme->relationLoaded('timetables')) {
                        $programme->setRelation('timetables', collect([]));
                    }
                    
                    // Ensure pivot data is accessible
                    $programme->timetables->each(function($timetable) {
                        if (!isset($timetable->pivot)) {
                            $timetable->setRelation('pivot', (object) [
                                'status' => 'draft',
                                'published_at' => null,
                                'academic_session_id' => null
                            ]);
                        }
                    });
                });
                
                return response()->json([
                    'html' => view('admin.academic-sessions.partials.timetables-table', [
                        'programmes' => $programmes,
                        'academicSession' => $academicSession
                    ])->render(),
                    'pagination' => (string) $programmes->links()
                ]);
            } else if ($tab === 'programmes') {
                $programmesQuery = $academicSession->programmes()
                    ->with(['school', 'courseUnitMappings' => function($q) use ($academicSession) {
                        $q->where('academic_session_id', $academicSession->id);
                    }]);
                
                if (request()->has('school_id') && request('school_id') !== 'all') {
                    $programmesQuery->where('school_id', request('school_id'));
                }
                
                $programmes = $programmesQuery->paginate(10);
                
                return response()->json([
                    'html' => view('admin.academic-sessions.partials.programmes-table', [
                        'programmes' => $programmes,
                        'academicSession' => $academicSession
                    ])->render(),
                    'pagination' => (string) $programmes->links()
                ]);
            }
        }
        
        // For initial page load, get all programmes with timetable status (paginated)
        $programmes = $timetableQuery->paginate(10);
        $programmes->each(function($programme) {
            $programme->timetable_status = $this->getTimetableStatus($programme);
        });
        
        // Get programmes that can have timetables added (have mappings but no timetable)
        $programmesForTimetable = $academicSession->programmes()
            ->whereNotExists(function($query) use ($academicSession) {
                $query->select(DB::raw(1))
                      ->from('programme_timetable')
                      ->join('timetables', 'timetables.id', '=', 'programme_timetable.timetable_id')
                      ->whereColumn('programme_timetable.programme_id', 'programmes.id')
                      ->where('timetables.academic_session_id', $academicSession->id);
            })
            ->whereHas('courseUnitMappings', function($q) use ($academicSession) {
                $q->where('academic_session_id', $academicSession->id);
            })
            ->get();
        
        return view('admin.academic-sessions.show', [
            'academicSession' => $academicSession,
            'programmes' => $programmes->isEmpty() ? collect() : $programmes,
            'programmesForTimetable' => $programmesForTimetable,
            'schools' => $schools,
            'selectedSchool' => $firstSchoolId
        ]);
    }
    
    /**
     * Get timetable status for a programme
     */
    protected function getTimetableStatus($programme)
    {
        // Check if programme has any timetables
        if ($programme->timetables->isNotEmpty()) {
            $timetable = $programme->timetables->first();
            $isPublished = $timetable->pivot->status === 'published' && $timetable->pivot->published_at !== null;
            
            if ($isPublished) {
                return [
                    'status' => 'published',
                    'label' => 'Published',
                    'class' => 'success',
                    'has_timetable' => true,
                    'progress' => 100
                ];
            }
            
            // Check if all required fields are filled
            $requiredFields = [
                'exam_dates' => !empty($timetable->exam_dates),
                'exam_venues' => !empty($timetable->exam_venues),
                'exam_times' => !empty($timetable->exam_times),
                'timetable_file' => !empty($timetable->timetable_file)
            ];
            
            $completedFields = count(array_filter($requiredFields));
            $totalFields = count($requiredFields);
            $progress = round(($completedFields / $totalFields) * 100);
            
            if ($progress === 100) {
                return [
                    'status' => 'ready',
                    'label' => 'Ready to Publish',
                    'class' => 'info',
                    'has_timetable' => true,
                    'progress' => $progress
                ];
            }
            
            return [
                'status' => 'in_progress',
                'label' => 'In Progress',
                'class' => 'warning',
                'has_timetable' => true,
                'progress' => $progress
            ];
        }
        
        // No timetable exists yet
        return [
            'status' => 'not_started',
            'label' => 'Not Started',
            'class' => 'secondary',
            'has_timetable' => false,
            'progress' => 0
        ];
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
        if ($academicSession->timetables()->exists()) {
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
