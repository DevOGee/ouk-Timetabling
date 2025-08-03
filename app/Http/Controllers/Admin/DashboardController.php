<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\Timetable;
use App\Models\User;
// Room model not available
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $isTimetabler = $user->hasRole('timetabler');
        
        // Get the current academic session
        $currentSession = AcademicSession::where('is_current', true)->first();
        
        // Initialize stats array
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
        
        // Add timetabler-specific data if needed
        if ($isTimetabler) {
            $stats['myProgrammes'] = $user->school->programmes()->count();
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'currentSession' => $currentSession,
            'isTimetabler' => $isTimetabler,
            'user' => $user
        ]);
    }
}
