<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\CourseUnit;
use App\Models\Programme;
use App\Models\Day;
use App\Models\AcademicSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InstructorSchedulesExport;
use App\Exports\WorkloadDistributionExport;
use App\Exports\ClassSchedulesExport;
use App\Models\School;
use App\Models\YearOfStudy;
use App\Models\Semester;
use PDF;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }
    
    public function instructorSchedules(Request $request, $instructorId = null)
    {
        $instructors = User::role('instructor')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
            
        // Get all academic sessions for the dropdown
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        
        // Get the selected academic session or default to the current one
        $selectedAcademicSessionId = $request->input('academic_session_id', 
            AcademicSession::where('is_current', true)->first()?->id
        );
            
        // Check for instructor_id in request (form submission)
        $instructorId = $request->input('instructor_id', $instructorId);
        $selectedInstructor = $instructorId ? User::find($instructorId) : null;
        $schedules = collect();
        
        if ($selectedInstructor) {
            // Get the instructor's schedules with related data
            $query = CourseUnitProgrammeMapping::with([
                    'courseUnit', 
                    'programme', 
                    'day', 
                    'semester',
                    'yearOfStudy'
                ])
                ->where('user_id', $selectedInstructor->id)
                ->whereNotNull('day_id');
                
            // Filter by academic session if selected
            if ($selectedAcademicSessionId) {
                $query->where('academic_session_id', $selectedAcademicSessionId);
            }
                
            // Get all schedules and group them by day, time, and course
            $rawSchedules = $query->orderBy('day_id')
                ->orderBy('morning_start_time')
                ->get();
                
            // Group schedules by day and course
            $groupedSchedules = [];
            
            foreach ($rawSchedules as $mapping) {
                $day = $mapping->day->name ?? 'N/A';
                $courseId = $mapping->course_unit_id;
                $session = $mapping->semester->name ?? 'N/A';
                $key = "{$day}-{$courseId}-{$session}";
                
                // Initialize the schedule entry if it doesn't exist
                if (!isset($groupedSchedules[$key])) {
                    // Extract just the year number (e.g., '1' from 'Year 1')
                    $yearNumber = $mapping->yearOfStudy ? 
                        (preg_match('/\d+/', $mapping->yearOfStudy->name, $matches) ? $matches[0] : null) : 
                        null;
                    
                    // Extract just the semester number (e.g., '1' from 'Semester 1')
                    $semesterNumber = $mapping->semester ? 
                        (preg_match('/\d+/', $mapping->semester->name, $matches) ? $matches[0] : null) : 
                        null;
                    
                    $yearSemester = ($yearNumber && $semesterNumber) 
                        ? $yearNumber . '.' . $semesterNumber 
                        : 'N/A';
                        
                    $groupedSchedules[$key] = (object)[
                        'day' => $day,
                        'times' => [],
                        'course_unit' => $mapping->courseUnit,
                        'programmes' => [],
                        'session_types' => [],
                        'year_semester' => $yearSemester,
                        'academic_session' => $mapping->academicSession->name ?? 'N/A'
                    ];
                }
                
                // Add morning session if exists
                if ($mapping->morning_start_time) {
                    $morningTime = Carbon::parse($mapping->morning_start_time)->format('H:i') . ' - ' . 
                                 Carbon::parse($mapping->morning_start_time)->addMinutes($mapping->morning_duration ?? 60)->format('H:i');
                    
                    if (!in_array($morningTime, $groupedSchedules[$key]->times)) {
                        $groupedSchedules[$key]->times[] = $morningTime;
                        $groupedSchedules[$key]->session_types[] = 'Morning';
                    }
                }
                
                // Add evening session if exists
                if ($mapping->evening_start_time) {
                    $eveningTime = Carbon::parse($mapping->evening_start_time)->format('H:i') . ' - ' . 
                                  Carbon::parse($mapping->evening_start_time)->addMinutes($mapping->evening_duration ?? 60)->format('H:i');
                    
                    if (!in_array($eveningTime, $groupedSchedules[$key]->times)) {
                        $groupedSchedules[$key]->times[] = $eveningTime;
                        $groupedSchedules[$key]->session_types[] = 'Evening';
                    }
                }
                
                // Add programme if it doesn't exist
                if ($mapping->programme) {
                    $programmeExists = false;
                    foreach ($groupedSchedules[$key]->programmes as $programme) {
                        if ($programme->id === $mapping->programme->id) {
                            $programmeExists = true;
                            break;
                        }
                    }
                    if (!$programmeExists) {
                        $groupedSchedules[$key]->programmes[] = $mapping->programme;
                    }
                }
            }
            
            // Convert to array and sort by day and time
            $schedules = collect($groupedSchedules)->sortBy(function($item) {
                // Create a sortable string: day number + time
                $dayOrder = [
                    'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 
                    'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7
                ];
                $dayNum = $dayOrder[$item->day] ?? 99;
                return sprintf('%02d', $dayNum);
            })->values();
        }
        
        return view('admin.reports.instructor-schedules', [
            'instructors' => $instructors,
            'selectedInstructor' => $selectedInstructor,
            'schedules' => $schedules,
            'academicSessions' => $academicSessions,
            'selectedAcademicSessionId' => $selectedAcademicSessionId
        ]);
    }
    
    public function exportInstructorSchedules(Request $request, $format)
    {
        try {
            $instructorId = $request->input('instructor_id');
            $academicSessionId = $request->input('academic_session_id');
            
            if (!$instructorId) {
                return back()->with('error', 'No instructor selected for export.');
            }
            
            $instructor = User::findOrFail($instructorId);
            $academicSession = $academicSessionId 
                ? AcademicSession::find($academicSessionId)
                : AcademicSession::where('is_current', true)->first();
                
            $dayOrder = [
                'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 
                'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7
            ];
            
            $rawSchedules = CourseUnitProgrammeMapping::with(['courseUnit', 'programme', 'day', 'semester', 'academicSession'])
                ->where('user_id', $instructor->id)
                ->where('academic_session_id', $academicSession->id)
                ->whereNotNull('day_id')
                ->orderBy('day_id')
                ->orderBy('morning_start_time')
                ->get();
                
            // Group schedules by day, course, and session
            $groupedSchedules = [];
            
            foreach ($rawSchedules as $mapping) {
                $day = $mapping->day->name ?? 'N/A';
                $courseId = $mapping->course_unit_id;
                $session = $mapping->semester->name ?? 'N/A';
                $key = "{$day}-{$courseId}-{$session}";
                
                // Extract just the year number (e.g., '1' from 'Year 1')
                $yearNumber = $mapping->yearOfStudy ? 
                    (preg_match('/\d+/', $mapping->yearOfStudy->name, $matches) ? $matches[0] : null) : 
                    null;
                
                // Extract just the semester number (e.g., '1' from 'Semester 1')
                $semesterNumber = $mapping->semester ? 
                    (preg_match('/\d+/', $mapping->semester->name, $matches) ? $matches[0] : null) : 
                    null;
                
                $yearSemester = ($yearNumber && $semesterNumber) 
                    ? $yearNumber . '.' . $semesterNumber 
                    : 'N/A';
                
                // Initialize the schedule entry if it doesn't exist
                if (!isset($groupedSchedules[$key])) {
                    $groupedSchedules[$key] = (object)[
                        'day' => $day,
                        'times' => [],
                        'session_types' => [],
                        'course_unit' => $mapping->courseUnit,
                        'programmes' => [],
                        'year_semester' => $yearSemester,
                        'academic_session' => $mapping->academicSession->name ?? 'N/A',
                        'session' => $session
                    ];
                }
                
                // Add morning time if exists
                if ($mapping->morning_start_time) {
                    $morningTime = Carbon::parse($mapping->morning_start_time)->format('H:i') . ' - ' . 
                                 Carbon::parse($mapping->morning_start_time)->addMinutes($mapping->morning_duration ?? 60)->format('H:i');
                    $groupedSchedules[$key]->times[] = $morningTime;
                    $groupedSchedules[$key]->session_types[] = 'Morning';
                }
                
                // Add evening time if exists
                if ($mapping->evening_start_time) {
                    $eveningTime = Carbon::parse($mapping->evening_start_time)->format('H:i') . ' - ' . 
                                  Carbon::parse($mapping->evening_start_time)->addMinutes($mapping->evening_duration ?? 60)->format('H:i');
                    $groupedSchedules[$key]->times[] = $eveningTime;
                    $groupedSchedules[$key]->session_types[] = 'Evening';
                }
                
                // Add programme if it doesn't exist
                if ($mapping->programme && !in_array($mapping->programme, $groupedSchedules[$key]->programmes)) {
                    $groupedSchedules[$key]->programmes[] = $mapping->programme;
                }
            }
            
            // Convert to collection and sort by day and time
            $schedules = collect($groupedSchedules)->sortBy(function($item) use ($dayOrder) {
                $dayNum = $dayOrder[$item->day] ?? 99;
                $time = isset($item->times[0]) ? $item->times[0] : ''; // Get the first time slot for sorting
                return sprintf('%02d-%s', $dayNum, $time);
            })->values();
                
            if ($schedules->isEmpty()) {
                return back()->with('error', 'No schedules found for the selected instructor.');
            }
            
            if ($format === 'excel') {
                return Excel::download(
                    new InstructorSchedulesExport($schedules), 
                    'instructor-schedules-' . $instructor->id . '-' . Str::slug($academicSession->name) . '.xlsx'
                );
            }
            
            if ($format === 'pdf') {                
                $pdf = PDF::loadView('admin.reports.exports.instructor-schedules-pdf', [
                    'instructor' => $instructor,
                    'schedules' => $schedules
                ]);
                
                return $pdf->download('instructor-schedules-' . $instructor->id . '-' . Str::slug($academicSession->name) . '.pdf');
            }
            
            return back()->with('error', 'Invalid export format. Please choose Excel or PDF.');
            
        } catch (\Exception $e) {
            \Log::error('Export Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while generating the export: ' . $e->getMessage());
        }
    }

    /**
     * Display workload distribution report
     */
    public function workloadDistribution(Request $request)
    {
        // Get all academic sessions for the dropdown
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $schools = School::orderBy('name')->get();
        
        // Get filter parameters
        $selectedAcademicSessionId = $request->input('academic_session_id', 
            AcademicSession::where('is_current', true)->first()?->id
        );
        $selectedSchoolId = $request->input('school_id', 'all');
        
        $workloadData = collect();
        
        if ($selectedAcademicSessionId) {
            // Base query for instructors
            $query = User::role('instructor')
                ->with('school')
                ->withCount(['courseUnitMappings as total_units' => function($q) use ($selectedAcademicSessionId) {
                    $q->where('academic_session_id', $selectedAcademicSessionId);
                }])
                ->with(['courseUnitMappings' => function($q) use ($selectedAcademicSessionId) {
                    $q->where('academic_session_id', $selectedAcademicSessionId)
                      ->with(['courseUnit', 'programme']);
                }])
                ->orderBy('name');
            
            // Apply school filter if specified
            if ($selectedSchoolId !== 'all') {
                $query->where('school_id', $selectedSchoolId);
            }
            
            $workloadData = $query->get()->map(function($instructor) {
                // Skip if instructor has no school assigned
                if (!$instructor->school) {
                    return null;
                }
                
                // Get unique course units
                $uniqueUnits = $instructor->courseUnitMappings
                    ->filter(function($mapping) {
                        return $mapping->courseUnit !== null;
                    })
                    ->unique('course_unit_id')
                    ->map(function($mapping) {
                        return $mapping->courseUnit;
                    });
                
                // Format name as "Title Firstname LASTNAME"
                $name = $instructor->name;
                $nameParts = explode(' ', $name);
                $lastName = array_pop($nameParts);
                $firstName = implode(' ', $nameParts);
                $title = $instructor->title ? ($instructor->title->abbreviation ?? $instructor->title->name) : '';
                $formattedName = trim(($title ? $title . ' ' : '') . $firstName . ' ' . strtoupper($lastName));
                
                return (object)[
                    'name' => $formattedName,
                    'course_units' => $uniqueUnits->pluck('code')->implode(', '),
                    'total_units' => $uniqueUnits->count()
                ];
            })->filter(); // Remove any null entries from the main collection
        }
        
        // Debug the data being passed to the view
        \Log::info('Workload Data:', ['data' => $workloadData]);
        
        return view('admin.reports.workload-distribution', [
            'academicSessions' => $academicSessions,
            'schools' => $schools,
            'selectedAcademicSessionId' => $selectedAcademicSessionId,
            'selectedSchoolId' => $selectedSchoolId,
            'workloadData' => $workloadData
        ]);
    }
    
    /**
     * Export workload distribution report
     */
    public function exportWorkloadDistribution(Request $request, $format)
    {
        try {
            $academicSession = AcademicSession::findOrFail($request->input('academic_session_id'));
            $schoolId = $request->input('school_id', 'all');
            
            if ($format === 'excel') {
                return Excel::download(
                    new WorkloadDistributionExport($academicSession->id, $schoolId),
                    'workload-distribution-' . Str::slug($academicSession->name) . '.xlsx'
                );
            }
            
            // For PDF, we'll generate it directly here
            if ($format === 'pdf') {
                $school = $schoolId !== 'all' ? School::findOrFail($schoolId) : null;
                
                // Base query for instructors
                $query = User::role('instructor')
                    ->with('school')
                    ->withCount(['courseUnitMappings as total_units' => function($q) use ($academicSession) {
                        $q->where('academic_session_id', $academicSession->id);
                    }])
                    ->with(['courseUnitMappings' => function($q) use ($academicSession) {
                        $q->where('academic_session_id', $academicSession->id)
                          ->with(['courseUnit', 'programme']);
                    }])
                    ->orderBy('name');
                
                // Apply school filter if specified
                if ($schoolId !== 'all') {
                    $query->where('school_id', $schoolId);
                }
                
                $workloadData = $query->get()->map(function($instructor) {
                    // Skip if instructor has no school assigned
                    if (!$instructor->school) {
                        return null;
                    }
                    
                    // Get unique course units
                    $uniqueUnits = $instructor->courseUnitMappings
                        ->filter(function($mapping) {
                            return $mapping->courseUnit !== null;
                        })
                        ->unique('course_unit_id')
                        ->map(function($mapping) {
                            return $mapping->courseUnit;
                        });
                    
                    // Format name as "Title Firstname LASTNAME"
                    $name = $instructor->name;
                    $nameParts = explode(' ', $name);
                    $lastName = array_pop($nameParts);
                    $firstName = implode(' ', $nameParts);
                    $title = $instructor->title ? ($instructor->title->abbreviation ?? $instructor->title->name) : '';
                    $formattedName = trim(($title ? $title . ' ' : '') . $firstName . ' ' . strtoupper($lastName));
                    
                    return (object)[
                        'name' => $formattedName,
                        'course_units' => $uniqueUnits->pluck('code')->implode(', '),
                        'total_units' => $uniqueUnits->count()
                    ];
                })->filter(); // Remove any null entries
                
                $pdf = PDF::loadView('admin.reports.exports.workload-distribution-pdf', [
                    'academicSession' => $academicSession,
                    'school' => $school,
                    'workloadData' => $workloadData
                ]);
                
                return $pdf->download('workload-distribution-' . Str::slug($academicSession->name) . '.pdf');
            }
            
            return back()->with('error', 'Invalid export format. Please choose Excel or PDF.');
            
        } catch (\Exception $e) {
            \Log::error('Export Workload Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while generating the export: ' . $e->getMessage());
        }
    }
    
    /**
     * Display class schedules report
     */
    public function classSchedules(Request $request)
    {
        // Get filter parameters
        $academicSessionId = $request->input('academic_session_id', 
            AcademicSession::where('is_current', true)->first()?->id
        );
        
        $schoolId = $request->input('school_id', 'all');
        $showInstructors = $request->boolean('show_instructors', false);
        
        // Get filter options
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $schools = School::orderBy('name')->get();
        
        // Define days of the week we want to display
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        // Initialize the schedule data structure
        $scheduleData = collect();
        
        if ($academicSessionId) {
            // Get all course unit mappings for the selected school and academic session
            $withRelations = [
                'courseUnit',
                'day',
                'programme',
                'yearOfStudy',
                'semester',
                'instructor' // Instructor relationship
            ];
            
            if ($showInstructors) {
                $withRelations[] = 'courseUnit.instructors';
            }
            
            $query = CourseUnitProgrammeMapping::with($withRelations)
                ->where('academic_session_id', $academicSessionId);
                
            if ($schoolId !== 'all') {
                $query->whereHas('programme', function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                });
            }
            
            // Debug: Log the SQL query
            \DB::enableQueryLog();
            $mappings = $query->get();
            \Log::debug('SQL Query:', \DB::getQueryLog());
            
            // Group by programme
            $groupedByProgramme = $mappings->groupBy('programme_id');
            
            foreach ($groupedByProgramme as $programmeId => $programmeMappings) {
                $programme = Programme::withTrashed()->find($programmeId);
                if (!$programme) {
                    \Log::error('Programme not found:', ['programme_id' => $programmeId]);
                    continue;
                }
                
                // Debug log
                \Log::debug('Programme Data:', [
                    'id' => $programmeId,
                    'programme_code' => $programme->programme_code ?? 'null',
                    'name' => $programme->name ?? 'null'
                ]);
                
                $programmeData = [
                    'programme_code' => $programme->programme_code,
                    'programme_name' => $programme->name,
                    'schedules' => []
                ];
                
                if (empty($programme->programme_code)) {
                    \Log::warning('Programme code is empty for programme:', ['id' => $programme->id, 'name' => $programme->name]);
                }
                
                // Debug log the created programme data
                \Log::debug('Created Programme Data:', $programmeData);
                
                // Group by year of study
                $groupedByYear = $programmeMappings->groupBy('year_of_study_id');
                
                foreach ($groupedByYear as $yearId => $yearMappings) {
                    $yearOfStudy = YearOfStudy::find($yearId);
                    
                    // Group by semester
                    $groupedBySemester = $yearMappings->groupBy('semester_id');
                    
                    foreach ($groupedBySemester as $semesterId => $semesterMappings) {
                        $semester = Semester::find($semesterId);
                        
                        // Create row header (e.g., "1.1" for Year 1, Semester 1)
                        $rowHeader = $yearOfStudy->name . '.' . substr($semester->name, 0, 1);
                        
                        // Initialize row data with empty arrays for each day
                        $rowData = [
                            'row_header' => $rowHeader,
                            'days' => [
                                'Monday' => [],
                                'Tuesday' => [],
                                'Wednesday' => [],
                                'Thursday' => [],
                                'Friday' => []
                            ]
                        ];
                        
                        // Group by day and add course data
                        $mappingsByDay = $semesterMappings->groupBy('day.name');
                        
                        foreach ($mappingsByDay as $dayName => $dayMappings) {
                            if (array_key_exists($dayName, $rowData['days'])) {
                                $rowData['days'][$dayName] = $dayMappings->map(function($mapping) use ($showInstructors) {
                                    return [
                                        'code' => $mapping->courseUnit->code,
                                        'name' => $mapping->courseUnit->name,
                                        'programme_code' => $mapping->programme->programme_code,
                                        'instructors' => $showInstructors ? 
                                            ($mapping->instructor ? [
                                                [
                                                    'name' => $mapping->instructor->name,
                                                    'email' => $mapping->instructor->email
                                                ]
                                            ] : []) : []
                                    ];
                                })->unique('code')->sortBy('code')->values()->toArray();
                            }
                        }
                    
                    $programmeData['schedules'][] = $rowData;
                    }
                }
                
                // Sort the schedules by row header (e.g., 1.1, 1.2, 2.1, etc.)
                usort($programmeData['schedules'], function($a, $b) {
                    return strcmp($a['row_header'], $b['row_header']);
                });
                
                $scheduleData->push($programmeData);
            }
            
            // Sort the schedule data by row header (e.g., 1.1, 1.2, 2.1, etc.)
            $scheduleData = $scheduleData->sortBy('row_header');
        }
        
        return view('admin.reports.class-schedules', [
            'academicSessions' => $academicSessions,
            'selectedAcademicSessionId' => $academicSessionId,
            'schools' => $schools,
            'selectedSchoolId' => $schoolId,
            'days' => $days,
            'scheduleData' => $scheduleData,
            'showCourseNames' => $request->boolean('show_course_names', false),
            'showInstructors' => $showInstructors,
            'school' => $schoolId !== 'all' ? School::find($schoolId) : null
        ]);
    }

    /**
     * Export class schedules to Excel or PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $format  The export format (xlsx or pdf)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportClassSchedules(Request $request, string $format = 'xlsx')
    {
        $academicSessionId = $request->input('academic_session_id', 
            AcademicSession::where('is_current', true)->first()?->id
        );
        
        $schoolId = $request->input('school_id', 'all');
        $showCourseNames = $request->boolean('show_course_names', false);
        $showInstructors = $request->boolean('show_instructors', false);
        
        if (!$academicSessionId) {
            return back()->with('error', 'Please select an academic session.');
        }
        
        $school = null;
        $filename = 'class-schedules';
        
        if ($schoolId !== 'all') {
            $school = School::findOrFail($schoolId);
            $filename .= '-' . $school->code;
        } else {
            $filename .= '-all-schools';
        }
        
        $filename .= '-' . now()->format('Y-m-d');
        
        if ($format === 'pdf') {
            // Get the data for the PDF view
            $export = new \App\Exports\ClassSchedulesExport($academicSessionId, $schoolId, $showCourseNames, $showInstructors);
            $data = $export->view()->getData();
            
            // Debug: Log the instructors data
            \Log::debug('PDF Export - Show Instructors: ' . ($showInstructors ? 'true' : 'false'));
            \Log::debug('PDF Export - Schedule Data Sample: ', 
                $data['scheduleData']->first()['schedules'][0]['days'] ?? []
            );

            // Ensure we're passing the full schedule data with instructors
            $pdf = \PDF::loadView('admin.reports.exports.class-schedules-pdf', [
                'school' => $school,
                'academicSession' => AcademicSession::find($academicSessionId),
                'scheduleData' => $data['scheduleData'],
                'days' => $data['days'],
                'showCourseNames' => $showCourseNames,
                'showInstructors' => $showInstructors,
                'showInstructors' => $showInstructors
            ]);
            
            return $pdf->download($filename . '.pdf');
        }
        
        // For Excel export
        $export = new \App\Exports\ClassSchedulesExport($academicSessionId, $schoolId, $showCourseNames, $showInstructors);
        
        // Default to Excel
        $filename .= '.xlsx';
        return Excel::download($export, $filename);
    }
}
