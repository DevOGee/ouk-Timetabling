<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\User;
use App\Models\Room;
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
        $stats = [
            'academicSessions' => AcademicSession::count(),
            'programmes' => Programme::count(),
            'courseUnits' => CourseUnit::count(),
            'instructors' => User::role('instructor')->count(),
            'rooms' => Room::count(),
        ];

        // Get the current academic session
        $currentSession = AcademicSession::where('is_current', true)->first();
        
        // Get recent academic sessions
        $recentSessions = AcademicSession::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'currentSession', 'recentSessions'));
    }
}
