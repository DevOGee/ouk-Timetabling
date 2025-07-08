<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
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
        $firstSchoolId = $schools->first()?->id;
        
        // Base query for programmes in this academic session
        $programmesQuery = $academicSession->programmes()
            ->with(['school'])
            ->select([
                'programmes.id',
                'programmes.name',
                'programmes.programme_code',
                'programmes.school_id',
                'programmes.created_at',
                'programmes.updated_at',
                \DB::raw('(SELECT COUNT(*) FROM course_unit_programme_mappings 
                          WHERE course_unit_programme_mappings.programme_id = programmes.id 
                          AND course_unit_programme_mappings.academic_session_id = ' . $academicSession->id . ') as course_units_count')
            ]);
            
        // Check if this is an AJAX request
        if (request()->ajax()) {
            $schoolId = request('school_id');
            
            // Filter by school if specified and not 'all'
            if (!empty($schoolId) && $schoolId !== 'all') {
                $programmesQuery->where('programmes.school_id', $schoolId);
            }
            
            $programmes = $programmesQuery->paginate(10);
            
            return response()->json([
                'html' => view('admin.academic-sessions.partials.programmes-table', [
                    'academicSession' => $academicSession,
                    'programmes' => $programmes
                ])->render(),
                'pagination' => (string) $programmes->links()
            ]);
        }
        
        // For initial page load, get all programmes (will be replaced by AJAX)
        $programmes = $programmesQuery->paginate(10);
        
        return view('admin.academic-sessions.show', [
            'academicSession' => $academicSession,
            'programmes' => $programmes,
            'schools' => $schools,
            'selectedSchool' => $firstSchoolId
        ]);
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
