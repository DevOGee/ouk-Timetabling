<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\ProgrammeTimetable;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class TimetableManagementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isTimetabler = $user->hasRole('timetabler');
        
        // Get the active academic session
        $academicSession = AcademicSession::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->first();
            
        if (!$academicSession) {
            return view('admin.timetables.index', [
                'programs' => collect(),
                'publishedPrograms' => collect(),
                'readyPrograms' => collect(),
                'pendingPrograms' => collect(),
                'academicSession' => null,
                'chartData' => [
                    'published' => 0,
                    'pending' => 0,
                    'not_assigned' => 0,
                    'labels' => ['Published (0)', 'Pending (0)', 'Not Assigned (0)'],
                    'colors' => ['#28a745', '#ffc107', '#dc3545']
                ]
            ]);
        }
        
        $programs = collect();
        
        // Base query for programs
        $programQuery = Programme::with(['programmeTimetables' => function($query) use ($academicSession) {
            $query->where('academic_session_id', $academicSession->id);
        }, 'courseUnitMappings' => function($query) use ($academicSession) {
            $query->where('academic_session_id', $academicSession->id);
        }])
        ->whereHas('academicSessions', function($query) use ($academicSession) {
            $query->where('academic_session_id', $academicSession->id);
        });
        
        // If user is a timetabler, only show programs from their school
        if ($isTimetabler && $user->school_id) {
            $programQuery->where('school_id', $user->school_id);
        }
        
        $programs = $programQuery->orderBy('name')->get();
        
        // Get total count of programs (filtered by school if timetabler)
        $totalProgramsQuery = Programme::query();
        if ($isTimetabler && $user->school_id) {
            $totalProgramsQuery->where('school_id', $user->school_id);
        }
        $totalPrograms = $totalProgramsQuery->count();
        $mappedCount = $programs->count();
        $notMappedCount = $totalPrograms - $mappedCount;
        
        // Categorize programs
        $publishedPrograms = collect();
        $readyPrograms = collect();
        $pendingPrograms = collect();
        
        // Chart counters
        $chartPublished = 0;
        $chartPending = 0; // Includes pending, ready, draft

        foreach ($programs as $program) {
            $timetable = $program->programmeTimetables->first();
            $mappings = $program->courseUnitMappings;
            
            $completed = 0;
            $inProgress = 0;
            
            foreach ($mappings as $mapping) {
                $hasMorning = $mapping->morning_start_time !== null && $mapping->morning_duration !== null;
                $hasEvening = $mapping->evening_start_time !== null && $mapping->evening_duration !== null;
                
                if ($hasMorning || $hasEvening) {
                    $completed++;
                } elseif ($mapping->day_id !== null || $mapping->user_id !== null) {
                    $inProgress++;
                }
            }
            
            $totalMappings = $mappings->count();
            $notStarted = $totalMappings - $completed - $inProgress;
            
            // Determine status
            $status = 'not_started';
            if ($timetable && $timetable->status === 'published') {
                $status = 'published';
            } elseif ($totalMappings === 0) {
                $status = 'not_started';
            } elseif ($completed === $totalMappings) {
                $status = 'ready';
            } elseif ($completed > 0 || $inProgress > 0) {
                $status = 'in_progress';
            }
            
            // Attach calculated status to program object for easier view rendering
            $program->calculated_status = $status;
            $program->stats = [
                'completed' => $completed,
                'inProgress' => $inProgress,
                'notStarted' => $notStarted,
                'total' => $totalMappings
            ];

            // Sort into buckets
            if ($status === 'published') {
                $publishedPrograms->push($program);
                $chartPublished++;
            } elseif ($status === 'ready') {
                $readyPrograms->push($program);
                $chartPending++;
            } else {
                $pendingPrograms->push($program);
                $chartPending++;
            }
        }
        
        $chartData = [
            'published' => $chartPublished,
            'pending' => $chartPending,
            'not_assigned' => $notMappedCount,
            'labels' => ["Published ($chartPublished)", "Pending ($chartPending)", "Not Assigned ($notMappedCount)"],
            'colors' => ['#28a745', '#ffc107', '#dc3545']
        ];
        
        $activeTab = request('tab', 'all');
        $perPage = 10;
        $page = Paginator::resolveCurrentPage('page');
        
        $targetCollection = match($activeTab) {
            'published' => $publishedPrograms,
            'ready' => $readyPrograms,
            'pending' => $pendingPrograms,
            default => $programs
        };
        
        $paginatedPrograms = new LengthAwarePaginator(
            $targetCollection->forPage($page, $perPage),
            $targetCollection->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
        
        return view('admin.timetables.index', [
            'paginatedPrograms' => $paginatedPrograms,
            'activeTab' => $activeTab,
            'counts' => [
                'all' => $programs->count(),
                'published' => $publishedPrograms->count(),
                'ready' => $readyPrograms->count(),
                'pending' => $pendingPrograms->count(),
            ],
            'academicSession' => $academicSession,
            'chartData' => $chartData
        ]);
    }
    
    public function create(Request $request)
    {
        $programmeId = $request->input('programme_id');
        $academicSessionId = $request->input('academic_session_id');
        
        if (!$programmeId || !$academicSessionId) {
            return redirect()->route('admin.timetables.manage')
                ->with('error', 'Programme ID and Academic Session ID are required.');
        }
        
        $programme = Programme::findOrFail($programmeId);
        $academicSession = AcademicSession::findOrFail($academicSessionId);
        
        // Check if a timetable already exists for this programme and academic session
        $existingTimetable = $programme->programmeTimetables()
            ->where('academic_session_id', $academicSessionId)
            ->first();
            
        if ($existingTimetable) {
            return redirect()->route('admin.timetables.manage')
                ->with('error', 'A timetable already exists for this programme and academic session.');
        }
        
        return view('admin.timetables.create', [
            'programme' => $programme,
            'academicSession' => $academicSession,
            'years' => YearOfStudy::all(),
            'semesters' => Semester::all()
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);
        
        $programme = Programme::findOrFail($validated['programme_id']);
        
        // Check if a timetable already exists for this programme and academic session
        $existingTimetable = $programme->programmeTimetables()
            ->where('academic_session_id', $validated['academic_session_id'])
            ->first();
            
        if ($existingTimetable) {
            return redirect()->route('admin.timetables.manage')
                ->with('error', 'A timetable already exists for this programme and academic session.');
        }
        
        try {
            // Create a new timetable entry in the programme_timetable table
            $timetable = new ProgrammeTimetable([
                'programme_id' => $validated['programme_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'published_at' => $validated['status'] === 'published' ? now() : null,
                'published_by' => $validated['status'] === 'published' ? auth()->id() : null,
            ]);
            
            $timetable->save();
            
            return redirect()->route('admin.timetables.manage')
                ->with('success', 'Timetable created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create timetable. ' . $e->getMessage());
        }
    }
    
    public function publish(ProgrammeTimetable $timetable)
    {
        try {
            // Publish the selected timetable without unpublishing others
            $timetable->update([
                'status' => 'published',
                'published_at' => now(),
                'published_by' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Timetable published successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish timetable. ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function unpublish(ProgrammeTimetable $timetable)
    {
        $timetable->update([
            'status' => 'draft',
            'published_at' => null,
            'published_by' => null
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Timetable unpublished successfully',
            'timetable' => $timetable->fresh()
        ]);
    }
    
    public function destroy(ProgrammeTimetable $timetable)
    {
        try {
            $timetable->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Timetable deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete timetable. ' . $e->getMessage()
            ], 500);
        }
    }
}
