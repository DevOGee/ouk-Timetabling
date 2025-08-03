<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\User;
use App\Models\Room;
use App\Models\CourseUnitProgrammeMapping;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $isTimetabler = $user->hasRole('timetabler');
        $isAdmin = $user->hasRole('admin');

        // Get academic sessions - active and current
        $academicSessions = $isTimetabler ? collect() : AcademicSession::latest()->get();
        $currentSession = AcademicSession::where('is_current', true)->first();
        $activeSession = AcademicSession::where('status', 'active')->first();
        
        // If no current session but there is an active one, use that
        if (!$currentSession && $activeSession) {
            $currentSession = $activeSession;
        }
        $programmes = $isTimetabler 
            ? Programme::where('school_id', $user->school_id)->with('school')->latest()->get()
            : Programme::with('school')->latest()->get();
            
        $instructors = $isTimetabler
            ? User::role('instructor')->where('school_id', $user->school_id)->with('title')->get()
            : User::role('instructor')->with('title')->get();
            
        $courseUnits = $isTimetabler ? collect() : CourseUnit::latest()->get();
        
        // Initialize stats array for cards
        $stats = [
            'academicSessions' => $isTimetabler ? 0 : AcademicSession::count(),
            'programmes' => $isTimetabler 
                ? $user->school->programmes()->count() 
                : Programme::count(),
            'courseUnits' => $isTimetabler ? 0 : CourseUnit::count(),
            'instructors' => $isTimetabler 
                ? User::role('instructor')->where('school_id', $user->school_id)->count()
                : User::role('instructor')->count(),
        ];
        
        // Initialize empty collection for unmapped programmes
        $unmappedProgrammes = collect();
        
        if ($currentSession) {
            // Get all programmes that are part of the current academic session
            $programmesInSession = $currentSession->programmes()
                ->when($isTimetabler, function($query) use ($user) {
                    return $query->where('school_id', $user->school_id);
                })
                ->pluck('programmes.id');
            
            if ($programmesInSession->isNotEmpty()) {
                // Get programmes that have course units mapped in this session
                $programmesWithMappedCourses = CourseUnitProgrammeMapping::where('academic_session_id', $currentSession->id)
                    ->whereNotNull('course_unit_id')
                    ->when($isTimetabler, function($query) use ($user) {
                        return $query->whereHas('programme', function($q) use ($user) {
                            $q->where('school_id', $user->school_id);
                        });
                    })
                    ->distinct('programme_id')
                    ->pluck('programme_id');
                
                // Find programmes in the session that don't have any course units mapped
                $unmappedProgrammeIds = $programmesInSession->diff($programmesWithMappedCourses);
                
                if ($unmappedProgrammeIds->isNotEmpty()) {
                    // Get the actual programme models with school relationship
                    $unmappedProgrammes = Programme::whereIn('id', $unmappedProgrammeIds)
                        ->with('school')
                        ->latest()
                        ->get();
                }
            }
        }

        return view('dashboard', [
            'academicSessions' => $academicSessions,
            'programmes' => $programmes,
            'instructors' => $instructors,
            'courseUnits' => $courseUnits,
            'currentSession' => $currentSession,
            'unmappedProgrammes' => $unmappedProgrammes,
            'stats' => $stats,
            'isTimetabler' => $isTimetabler,
            'isAdmin' => $isAdmin,
            'recentProgrammes' => $programmes->take(5) // Add recent programmes (first 5)
        ]);
    }
}
