<?php

namespace App\Http\Controllers;

use App\Imports\CurriculumMappingImport;
use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Programme;
use App\Models\School;
use App\Models\Semester;
use App\Models\YearOfStudy;
use App\Models\Curriculum;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CurriculumSetupController extends Controller
{
    protected $activeSession;
    protected $activeCurriculum;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Get the active academic session
            $this->activeSession = AcademicSession::where('is_current', true)->first();
            
            if (!$this->activeSession) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'error' => 'No active academic session found. Please set an active session first.'
                    ], 400);
                }
                return redirect()->route('admin.academic-sessions.index')
                    ->with('error', 'No active academic session found. Please set an active session first.');
            }
            
            // Get the active curriculum for this session
            $this->activeCurriculum = $this->activeSession->activeCurriculum;
            
            if (!$this->activeCurriculum) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'error' => 'No active curriculum found for the current session.'
                    ], 400);
                }
                return redirect()->route('admin.academic-sessions.curricula.index', $this->activeSession)
                    ->with('error', 'No active curriculum found for the current session. Please create or activate a curriculum first.');
            }
            
            // Share active session and curriculum with all views
            view()->share([
                'activeSession' => $this->activeSession,
                'activeCurriculum' => $this->activeCurriculum
            ]);
            
            return $next($request);
        });
    }
    
    // View all programmes for the active curriculum
    public function index()
    {
        // Get schools with their programmes that have mappings in the active curriculum
        $schools = School::with(['programmes' => function($query) {
            $query->whereHas('courseUnitMappings', function($q) {
                $q->where('curriculum_id', $this->activeCurriculum->id);
            })->withCount(['courseUnitMappings' => function($q) {
                $q->where('curriculum_id', $this->activeCurriculum->id);
            }]);
        }])->get();
        
        // If no schools with programmes found, show all schools with their programmes
        if ($schools->sum('programmes_count') === 0) {
            $schools = School::with(['programmes' => function($query) {
                $query->withCount(['courseUnitMappings' => function($q) {
                    $q->where('curriculum_id', $this->activeCurriculum->id);
                }]);
            }])->get();
        }
        
        return view('curriculum.index', compact('schools'));
    }

    // Show a specific programme's curriculum
    public function show(Programme $programme)
    {
        // Get all years of study and semesters for the form
        $years = YearOfStudy::orderBy('id')->get();
        $semesters = Semester::orderBy('id')->get();
        
        // Get all course units that can be added to this programme
        $availableCourseUnits = CourseUnit::whereDoesntHave('programmes', function($query) use ($programme) {
            $query->where('programme_id', $programme->id)
                  ->where('curriculum_id', $this->activeCurriculum->id);
        })->get();
        
        // Get all course units that are already mapped to this programme in the active curriculum
        $mappedCourseUnits = $programme->courseUnitMappings()
            ->where('curriculum_id', $this->activeCurriculum->id)
            ->with(['courseUnit', 'yearOfStudy', 'semester', 'lecturer'])
            ->get()
            ->groupBy('year_of_study_id');
        
        // Get all lecturers for the lecturer dropdown
        $lecturers = \App\Models\Lecturer::orderBy('name')->get();
        
        return view('curriculum.show', compact(
            'programme', 
            'years', 
            'semesters', 
            'availableCourseUnits',
            'mappedCourseUnits',
            'lecturers'
        ));
    }

    // Add a course unit to a programme's curriculum
    public function store(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'course_unit_id' => 'required|exists:course_units,id',
            'year_of_study_id' => 'required|exists:years_of_study,id',
            'semester_id' => 'required|exists:semesters,id',
            'lecturer_id' => 'nullable|exists:lecturers,id',
        ]);
        
        try {
            // Check if this mapping already exists for this curriculum
            $exists = CourseUnitProgrammeMapping::where([
                'programme_id' => $programme->id,
                'course_unit_id' => $validated['course_unit_id'],
                'year_of_study_id' => $validated['year_of_study_id'],
                'semester_id' => $validated['semester_id'],
                'curriculum_id' => $this->activeCurriculum->id,
            ])->exists();
            
            if ($exists) {
                return back()->with('error', 'This course unit is already mapped to this programme for the selected year and semester.');
            }
            
            // Create the mapping
            $mapping = new CourseUnitProgrammeMapping([
                'programme_id' => $programme->id,
                'course_unit_id' => $validated['course_unit_id'],
                'year_of_study_id' => $validated['year_of_study_id'],
                'semester_id' => $validated['semester_id'],
                'curriculum_id' => $this->activeCurriculum->id,
                'lecturer_id' => $validated['lecturer_id'] ?? null,
                'created_by' => Auth::id(),
            ]);
            
            $mapping->save();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course unit added to programme successfully.',
                    'mapping' => $mapping->load(['courseUnit', 'yearOfStudy', 'semester', 'lecturer'])
                ]);
            }
            
            return back()->with('success', 'Course unit added to programme successfully.');
            
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding course unit to programme: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error adding course unit to programme: ' . $e->getMessage());
        }
    }

    // Delete a mapping
    public function destroy($id)
    {
        try {
            $mapping = CourseUnitProgrammeMapping::findOrFail($id);
            
            // Verify this mapping belongs to the active curriculum
            $activeCurriculum = AcademicSession::where('is_current', true)
                ->first()
                ?->activeCurriculum;
                
            if ($activeCurriculum && $mapping->curriculum_id !== $activeCurriculum->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete mappings from the active curriculum.'
                ], 403);
            }
            
            $mapping->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Mapping successfully removed.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete mapping: ' . $e->getMessage()
            ], 500);
        }
    }

    // Bulk upload course unit mappings
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);
        
        try {
            $import = new CurriculumMappingImport($this->activeCurriculum->id);
            Excel::import($import, $request->file('file'));
            
            $successCount = count($import->successes);
            $errorCount = count($import->failures);
            
            $message = "Successfully imported {$successCount} course unit mappings.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} mappings failed to import.";
            }
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'successes' => $import->successes,
                    'failures' => $import->failures
                ]);
            }
            
            return back()
                ->with('success', $message)
                ->with('success_report', $import->successes)
                ->with('error_report', $import->failures);
                
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error importing file: ' . $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    // Download a sample Excel file for bulk upload
    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="curriculum_mapping_sample.xlsx"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        // Create a new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'programme_code')
              ->setCellValue('B1', 'course_code')
              ->setCellValue('C1', 'year_of_study')
              ->setCellValue('D1', 'semester')
              ->setCellValue('E1', 'lecturer_code');
        
        // Add sample data
        $sampleData = [
            ['BSC-IT', 'CSC101', '1', '1', 'LEC001'],
            ['BSC-IT', 'CSC102', '1', '2', 'LEC002'],
            ['BSC-IT', 'CSC201', '2', '1', 'LEC001'],
            ['BSC-IT', 'CSC202', '2', '2', 'LEC002'],
        ];
        
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create a writer and output to browser
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        }, 200, $headers);
        
        return $response;
    }
}
