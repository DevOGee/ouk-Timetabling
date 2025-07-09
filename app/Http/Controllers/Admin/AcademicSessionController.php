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
                if (request()->has('school_id') && request('school_id') !== 'all') {
                    $programmes = $programmes->filter(function($programme) {
                        return $programme->school_id == request('school_id');
                    });
                }
                
                return response()->json([
                    'html' => view('admin.academic-sessions.partials.timetables-table', [
                        'programmes' => $programmes,
                        'academicSession' => $academicSession
                    ])->render(),
                    'pagination' => (string) $programmes->links()
                ]);
            } else if ($tab === 'programmes') {
                if (request()->has('school_id') && request('school_id') !== 'all') {
                    $programmes = $programmes->filter(function($programme) {
                        return $programme->school_id == request('school_id');
                    });
                }
                
                return response()->json([
                    'html' => view('admin.academic-sessions.partials.programmes-table', [
                        'programmes' => $programmes,
                        'academicSession' => $academicSession
                    ])->render(),
                    'pagination' => (string) $programmes->links()
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
            'selectedSchool' => $firstSchoolId
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
