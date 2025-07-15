<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\ProgrammeTimetable;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Illuminate\Http\Request;

class TimetableManagementController extends Controller
{
    public function index()
    {
        // Get the active academic session
        $academicSession = AcademicSession::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->first();
            
        if (!$academicSession) {
            return view('admin.timetables.index', [
                'programs' => collect(),
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
        
        // Initialize chart data
        $chartData = [
            'published' => 0,
            'pending' => 0,
            'not_assigned' => 0,
            'labels' => ['Published (0)', 'Pending (0)', 'Not Assigned (0)'],
            'colors' => ['#28a745', '#ffc107', '#dc3545']
        ];
        
        $programs = collect();
        
        if ($academicSession) {
            // Get all programs mapped to this session with their timetable status
            $programs = Programme::with(['programmeTimetables' => function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            }])
            ->whereHas('academicSessions', function($query) use ($academicSession) {
                $query->where('academic_session_id', $academicSession->id);
            })
            ->orderBy('name')
            ->get();
            
            // Get total count of programs in the system
            $totalPrograms = Programme::count();
            $mappedCount = $programs->count();
            $notMappedCount = $totalPrograms - $mappedCount;
            
            $publishedCount = 0;
            $pendingCount = 0;
            
            foreach ($programs as $program) {
                $timetable = $program->programmeTimetables->first();
                if ($timetable) {
                    if ($timetable->status === 'published') {
                        $publishedCount++;
                    } else {
                        $pendingCount++;
                    }
                } else {
                    $pendingCount++; // Count as pending if mapped but no timetable
                }
            }
            
            $chartData['published'] = $publishedCount;
            $chartData['pending'] = $pendingCount;
            $chartData['not_assigned'] = $notMappedCount;
            
            $chartData['labels'] = [
                "Published ($publishedCount)",
                "Pending ($pendingCount)",
                "Not Assigned ($notMappedCount)"
            ];
        }
        
        return view('admin.timetables.index', [
            'programs' => $programs,
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
