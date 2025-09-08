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
            'recentProgrammes' => $programmes->take(5) // Add recent programmes (first 5)
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
}
