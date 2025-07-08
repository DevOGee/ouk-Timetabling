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
        // Get counts for dashboard cards
        $academicSessions = AcademicSession::latest()->get();
        $programmes = Programme::with('school')->latest()->get();
        $instructors = User::role('instructor')->with('title')->get();
        $courseUnits = CourseUnit::latest()->get();

        // Get current academic session
        $currentSession = AcademicSession::where('is_current', true)->first();
        
        // Initialize empty collection for unmapped programmes
        $unmappedProgrammes = collect();
        
        if ($currentSession) {
            // Get all programmes that are part of the current academic session
            $programmesInSession = $currentSession->programmes()->pluck('programmes.id');
            
            // Get programmes that have course units mapped in this session
            $programmesWithMappedCourses = CourseUnitProgrammeMapping::where('academic_session_id', $currentSession->id)
                ->whereNotNull('course_unit_id')
                ->distinct('programme_id')
                ->pluck('programme_id');
            
            // Find programmes in the session that don't have any course units mapped
            $unmappedProgrammeIds = $programmesInSession->diff($programmesWithMappedCourses);
            
            // Get the actual programme models with school relationship
            $unmappedProgrammes = Programme::whereIn('id', $unmappedProgrammeIds)
                ->with('school')
                ->latest()
                ->take(5)
                ->get();
        }

        // Get recent programmes (last 5)
        $recentProgrammes = Programme::with('school')
            ->latest()
            ->take(5)
            ->get();
            
        // Get current academic session
        $currentSession = AcademicSession::where('is_current', true)->first();

        return view('dashboard', compact(
            'academicSessions',
            'programmes',
            'instructors',
            'courseUnits',
            'unmappedProgrammes',
            'recentProgrammes',
            'currentSession'
        ));
    }
}
