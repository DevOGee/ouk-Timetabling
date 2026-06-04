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

use App\Models\ExamSchedule;
use App\Models\Exam;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Day;

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

        // --- Happening Today Logic ---
        $today = Carbon::today();
        $isExamPeriod = false;
        $eventType = 'none';
        $todaysEvents = collect();

        // Check for active and published Exam Schedule covering today
        $activeExamSchedule = ExamSchedule::where('is_active', true)
            ->where('is_published', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if ($activeExamSchedule) {
            $isExamPeriod = true;
            $eventType = 'exam';
            
            $query = Exam::with(['courseUnit', 'invigilator', 'schedule'])
                ->where('exam_schedule_id', $activeExamSchedule->id)
                ->whereDate('exam_date', $today)
                ->orderBy('start_time');

            // If timetabler, filter by their school's programmes? 
            // Exams structure is a bit flat, might need joining for strict school filtering.
            // For now, listing all exams is safer/simpler for admin dashboard.
            
            $todaysEvents = $query->take(10)->get();

        } else {
            // Check for Classes (Timetable Slots)
            $eventType = 'class';
            $dayName = $today->format('l'); // e.g., "Monday"
            $day = Day::where('name', $dayName)->first();

            if ($day && $currentSession) {
                // Fetch TimeTable Mappings for today
                $query = CourseUnitProgrammeMapping::with(['courseUnit', 'programme', 'instructor'])
                    ->where('day_id', $day->id)
                    ->where('academic_session_id', $currentSession->id)
                    ->where(function ($q) {
                        $q->whereNotNull('morning_start_time')
                          ->orWhereNotNull('evening_start_time');
                    });

                if ($isTimetabler) {
                     $query->whereHas('programme', function($q) use ($user) {
                         $q->where('school_id', $user->school_id);
                     });
                }
                
                $mappings = $query->get();

                // Process mappings into events (splitting morning/evening if both exist)
                foreach ($mappings as $mapping) {
                    // Add morning slot if exists
                    if ($mapping->morning_start_time) {
                        $todaysEvents->push((object)[
                            'start_time' => $mapping->morning_start_time,
                            'duration' => $mapping->morning_duration,
                            'courseUnit' => $mapping->courseUnit,
                            'programme' => $mapping->programme,
                            'type' => 'Morning',
                            'room_id' => null, // Placeholder if needed
                            'invigilator' => null // Consistent structure
                        ]);
                    }
                    
                    // Add evening slot if exists
                    if ($mapping->evening_start_time) {
                        $todaysEvents->push((object)[
                            'start_time' => $mapping->evening_start_time,
                            'duration' => $mapping->evening_duration,
                            'courseUnit' => $mapping->courseUnit,
                            'programme' => $mapping->programme,
                            'type' => 'Evening',
                            'room_id' => null,
                            'invigilator' => null
                        ]);
                    }
                }

                // Sort by start time and limit
                $todaysEvents = $todaysEvents->sortBy('start_time')->take(10);
            }
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'currentSession' => $currentSession,
            'isTimetabler' => $isTimetabler,
            'user' => $user,
            'todaysEvents' => $todaysEvents,
            'eventType' => $eventType,
            'isExamPeriod' => $isExamPeriod
        ]);
    }
}

