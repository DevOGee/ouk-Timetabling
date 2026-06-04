<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\User;
use App\Models\CourseUnitProgrammeMapping;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;
use App\Models\Exam;
// use App\Models\LessonSlot;
use App\Models\Day;
use Carbon\Carbon;

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
        $isInstructor = $user->hasRole('instructor');

        // Get academic sessions
        $academicSessions = ($isTimetabler || $isInstructor) ? collect() : AcademicSession::latest()->get();
        
        // Get active academic session (status = 'active')
        $activeSession = AcademicSession::where('status', 'active')->first();
        
        // Get selected academic session (is_current = true)
        $selectedSession = AcademicSession::where('is_current', true)->first();
        $programmes = $isTimetabler 
            ? Programme::where('school_id', $user->school_id)->with('school')->latest()->get()
            : Programme::with('school')->latest()->get();
            
        $instructors = $isTimetabler
            ? User::role('instructor')->where('school_id', $user->school_id)->with('title')->get()
            : User::role('instructor')->with('title')->get();
            
        $courseUnits = $isTimetabler ? collect() : CourseUnit::latest()->get();
        
        // Initialize stats array for cards
        if ($isInstructor) {
            $stats = [
                'courseUnitsTeaching' => $user->assignedCourseUnits()->count(),
                'programmesTeaching' => $user->assignedProgrammes()->distinct()->count(),
            ];
        } else {
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
        }
        
        // Initialize empty collection for unmapped programmes
        $unmappedProgrammes = collect();
        
        if ($selectedSession) {
            // Get all programmes that are part of the selected academic session
            $programmesInSession = $selectedSession->programmes()
                ->when($isTimetabler, function($query) use ($user) {
                    return $query->where('school_id', $user->school_id);
                })
                ->pluck('programmes.id');
            
            if ($programmesInSession->isNotEmpty()) {
                // Get programmes that have course units mapped in this session
                $programmesWithMappedCourses = CourseUnitProgrammeMapping::where('academic_session_id', $selectedSession->id)
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

        // Prepare heatmap data for admins and timetablers
        $heatmapData = [];
        if ($selectedSession) {
            $schoolId = $isTimetabler ? $user->school_id : null;
            $heatmapData = $this->getHeatmapData($selectedSession->id, $schoolId);
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
            $todaysEvents = $query->take(5)->get();

        } else {
            // Check for Classes (Timetable Slots)
            $eventType = 'class';
            $dayName = $today->format('l'); // e.g., "Monday"
            $day = Day::where('name', $dayName)->first();

            if ($day && $selectedSession) {
                // Fetch TimeTable Mappings for today
                $query = CourseUnitProgrammeMapping::with(['courseUnit', 'programme', 'instructor'])
                    ->where('day_id', $day->id)
                    ->where('academic_session_id', $selectedSession->id)
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
                            'instructor' => $mapping->instructor,
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
                            'instructor' => $mapping->instructor,
                            'type' => 'Evening',
                            'room_id' => null,
                            'invigilator' => null
                        ]);
                    }
                }

                // Sort by start time and limit
                $todaysEvents = $todaysEvents->sortBy('start_time')->take(5);
            }
        }

        // ── Chart data ──────────────────────────────────────────────
        $chartData = [];

        if (!$isInstructor && $activeSession) {
            // Classes per day of week
            $perDay = \DB::table('course_unit_programme_mappings as cupm')
                ->join('days as d', 'd.id', '=', 'cupm.day_id')
                ->where('cupm.academic_session_id', $activeSession->id)
                ->selectRaw('d.name as day, d.id as day_id, COUNT(*) as total')
                ->groupBy('d.id', 'd.name')
                ->orderBy('d.id')
                ->get();

            $chartData['perDay'] = [
                'labels' => $perDay->pluck('day')->toArray(),
                'values' => $perDay->pluck('total')->toArray(),
            ];

            // Instructors per school
            $perSchool = \DB::table('users as u')
                ->join('schools as s', 's.id', '=', 'u.school_id')
                ->join('role_user as ru', 'ru.user_id', '=', 'u.id')
                ->join('roles as r', 'r.id', '=', 'ru.role_id')
                ->where('r.name', 'instructor')
                ->selectRaw('s.name as school, COUNT(DISTINCT u.id) as total')
                ->groupBy('s.id', 's.name')
                ->orderByDesc('total')
                ->get();

            $chartData['perSchool'] = [
                'labels' => $perSchool->pluck('school')->toArray(),
                'values' => $perSchool->pluck('total')->toArray(),
            ];

            // Programmes per school
            $progPerSchool = \DB::table('programmes as p')
                ->join('schools as s', 's.id', '=', 'p.school_id')
                ->whereNull('p.deleted_at')
                ->selectRaw('s.name as school, COUNT(p.id) as total')
                ->groupBy('s.id', 's.name')
                ->orderByDesc('total')
                ->get();

            $chartData['progPerSchool'] = [
                'labels' => $progPerSchool->pluck('school')->toArray(),
                'values' => $progPerSchool->pluck('total')->toArray(),
            ];

            // Zoom license status (all instructors)
            $instructorQuery = $isTimetabler 
                ? \App\Models\User::role('instructor')->where('school_id', $user->school_id)
                : \App\Models\User::role('instructor');

            $zoomLicensed   = (clone $instructorQuery)->whereNotNull('zoom_email')->count();
            $zoomUnlicensed = (clone $instructorQuery)->whereNull('zoom_email')->count();

            $chartData['zoom'] = [
                'licensed'   => $zoomLicensed,
                'unlicensed' => $zoomUnlicensed,
            ];
        }
        // ─────────────────────────────────────────────────────────────

        return view('dashboard', [
            'academicSessions' => $academicSessions,
            'programmes' => $programmes,
            'instructors' => $instructors,
            'courseUnits' => $courseUnits,
            'selectedSession' => $selectedSession,
            'unmappedProgrammes' => $unmappedProgrammes,
            'stats' => $stats,
            'heatmapData' => $heatmapData,
            'isTimetabler' => $isTimetabler,
            'isAdmin' => $isAdmin,
            'isInstructor' => $isInstructor,
            'recentProgrammes' => $programmes->take(5),
            'todaysEvents' => $todaysEvents,
            'eventType' => $eventType,
            'isExamPeriod' => $isExamPeriod,
            'chartData' => $chartData,
            'activeSession' => $activeSession,
        ]);
    }

    /**
     * Get heatmap data for course unit mappings
     *
     * @param int $academicSessionId
     * @return array
     */
    private function getHeatmapData($academicSessionId, $schoolId = null)
    {
        // Get all unique year_of_study_id-semester_id combinations from the mappings
        $levelCombinations = \App\Models\CourseUnitProgrammeMapping::where('academic_session_id', $academicSessionId)
            ->whereNotNull('course_unit_id')
            ->select('year_of_study_id', 'semester_id')
            ->distinct()
            ->orderBy('year_of_study_id')
            ->orderBy('semester_id')
            ->get()
            ->map(function($item) {
                return $item->year_of_study_id . '.' . $item->semester_id;
            })
            ->toArray();
            
        // If no levels found, use default levels
        $levels = !empty($levelCombinations) ? $levelCombinations : ['1.1', '1.2', '2.1', '2.2', '3.1', '3.2', '4.1', '4.2'];
        
        // Get all programmes with their course unit mappings for the current session
        $programmes = Programme::when($schoolId, function($query) use ($schoolId) {
                return $query->where('school_id', $schoolId);
            })
            ->with(['courseUnitMappings' => function($query) use ($academicSessionId) {
                $query->where('academic_session_id', $academicSessionId)
                      ->whereNotNull('course_unit_id')
                      ->with('courseUnit'); // Eager load course unit data
            }])
            ->get();

        // Initialize the heatmap data structure
        $heatmapData = [];
        
        foreach ($programmes as $programme) {
            $programmeData = [
                'id' => $programme->id,
                'name' => $programme->name,
                'code' => $programme->programme_code,
                'school' => $programme->school->name ?? 'N/A',
                'levels' => []
            ];
            
            // Initialize counts for each level
            foreach ($levels as $level) {
                $programmeData['levels'][$level] = [
                    'count' => 0,
                    'mappings' => []
                ];
            }
            
            // Group mappings by year_of_study_id and semester_id
            $mappingsByLevel = [];
            foreach ($programme->courseUnitMappings as $mapping) {
                $level = $mapping->year_of_study_id . '.' . $mapping->semester_id;
                if (!isset($mappingsByLevel[$level])) {
                    $mappingsByLevel[$level] = [];
                }
                $mappingsByLevel[$level][] = $mapping;
            }
            
            // Count course units per level and prepare mapping data
            foreach ($mappingsByLevel as $level => $mappings) {
                if (in_array($level, $levels)) {
                    $programmeData['levels'][$level]['count'] = count($mappings);
                    foreach ($mappings as $mapping) {
                        $programmeData['levels'][$level]['mappings'][] = [
                            'course_code' => $mapping->courseUnit->code ?? 'N/A',
                            'course_name' => $mapping->courseUnit->name ?? 'N/A'
                        ];
                    }
                }
            }
            
            $heatmapData[] = $programmeData;
        }
        
        return [
            'programmes' => $heatmapData,
            'levels' => $levels,
            'maxCount' => $this->getMaxCount($heatmapData, $levels)
        ];
    }
    
    /**
     * Get the maximum count of course units in any cell for scaling the heatmap
     *
     * @param array $heatmapData
     * @param array $levels
     * @return int
     */
    private function getMaxCount($heatmapData, $levels)
    {
        $maxCount = 1; // Start with at least 1 for scaling
        
        foreach ($heatmapData as $programme) {
            foreach ($levels as $level) {
                if (isset($programme['levels'][$level]['count']) && $programme['levels'][$level]['count'] > $maxCount) {
                    $maxCount = $programme['levels'][$level]['count'];
                }
            }
        }
        
        return $maxCount;
    }

    /**
     * Show all of today's events with advanced filters
     */
    public function todaysEvents(Request $request)
    {
        $user = auth()->user();
        $isTimetabler = $user->hasRole('timetabler');
        $selectedSession = AcademicSession::where('is_current', true)->first();

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
            
            $todaysEvents = $query->get();
        } else {
            $eventType = 'class';
            $dayName = $today->format('l'); // e.g., "Monday"
            $day = Day::where('name', $dayName)->first();

            if ($day && $selectedSession) {
                $query = CourseUnitProgrammeMapping::with(['courseUnit', 'programme', 'instructor'])
                    ->where('day_id', $day->id)
                    ->where('academic_session_id', $selectedSession->id)
                    ->where(function ($q) {
                        $q->whereNotNull('morning_start_time')
                          ->orWhereNotNull('evening_start_time');
                    });

                if ($isTimetabler) {
                    $query->whereHas('programme', function($q) use ($user) {
                        $q->where('school_id', $user->school_id);
                    });
                }
                
                // Advanced Filters from request
                if ($request->filled('programme_id')) {
                    $query->where('programme_id', $request->programme_id);
                }
                if ($request->filled('instructor_id')) {
                    $query->where('user_id', $request->instructor_id);
                }
                
                $mappings = $query->get();

                foreach ($mappings as $mapping) {
                    // Filter by zoom status if requested
                    $zoomStatus = $request->zoom_status;
                    $instructor = $mapping->instructor;
                    
                    if ($zoomStatus === 'licensed' && (!$instructor || !$instructor->zoom_email)) {
                        continue;
                    }
                    if ($zoomStatus === 'unlicensed' && ($instructor && $instructor->zoom_email)) {
                        continue;
                    }
                    
                    // Filter by time of day
                    $timeOfDay = $request->time_of_day;
                    
                    if ($mapping->morning_start_time) {
                        if (!$timeOfDay || $timeOfDay === 'morning') {
                            $todaysEvents->push((object)[
                                'start_time' => $mapping->morning_start_time,
                                'duration' => $mapping->morning_duration,
                                'courseUnit' => $mapping->courseUnit,
                                'programme' => $mapping->programme,
                                'instructor' => $mapping->instructor,
                                'type' => 'Morning',
                                'room_id' => null,
                                'invigilator' => null,
                                'mapping_id' => $mapping->id
                            ]);
                        }
                    }
                    
                    if ($mapping->evening_start_time) {
                        if (!$timeOfDay || $timeOfDay === 'evening') {
                            $todaysEvents->push((object)[
                                'start_time' => $mapping->evening_start_time,
                                'duration' => $mapping->evening_duration,
                                'courseUnit' => $mapping->courseUnit,
                                'programme' => $mapping->programme,
                                'instructor' => $mapping->instructor,
                                'type' => 'Evening',
                                'room_id' => null,
                                'invigilator' => null,
                                'mapping_id' => $mapping->id
                            ]);
                        }
                    }
                }

                $todaysEvents = $todaysEvents->sortBy('start_time');
            }
        }

        // Server-side paginate the collection
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page') ?: 1;
        $perPage = 10;
        $todaysEvents = new \Illuminate\Pagination\LengthAwarePaginator(
            $todaysEvents->forPage($page, $perPage)->values(),
            $todaysEvents->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Get filter data for dropdowns
        $programmes = $isTimetabler 
            ? Programme::where('school_id', $user->school_id)->get()
            : Programme::all();
        $instructors = User::role('instructor')->get();

        return view('todays-events', compact('todaysEvents', 'eventType', 'isExamPeriod', 'programmes', 'instructors'));
    }

    /**
     * Quick toggle for zoom license
     */
    public function toggleZoomLicense(User $user)
    {
        if ($user->zoom_email) {
            $user->update(['zoom_email' => null]);
            return back()->with('success', 'Zoom license removed for ' . $user->name);
        } else {
            $user->update(['zoom_email' => $user->email]);
            return back()->with('success', 'Zoom license assigned to ' . $user->name);
        }
    }
}
