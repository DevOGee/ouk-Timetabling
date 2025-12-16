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
use App\Models\LessonSlot;
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
                // Find active timetables for current session
                // Since LessonSlots belong to specific ProgrammeTimetable or similar, 
                // we assume LessonSlot links to things we can filter.
                // LessonSlot -> day_id.
                // But LessonSlot doesn't directly link to 'Active Session' easily unless we join through Programme?
                // Actually LessonSlot is usually part of a generated timetable structure.
                // Looking at LessonSlot model: it has 'programme_id'.
                // We'll just fetch slots for the day. Ideally we should filter by active session context.
                // However, without a direct link on LessonSlot to Session, we rely on the fact that 
                // typically valid slots are from current curriculum.
                // Wait, LessonSlot might be persistent.
                
                // Let's just fetch for the day for now. To be more robust we'd check if the programme is active.
                
                $query = LessonSlot::with(['courseUnit', 'programme'])
                    ->where('day_id', $day->id)
                    ->orderBy('start_time');
                
                if ($isTimetabler) {
                     $query->whereHas('programme', function($q) use ($user) {
                         $q->where('school_id', $user->school_id);
                     });
                }
                
                $todaysEvents = $query->take(10)->get();
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

