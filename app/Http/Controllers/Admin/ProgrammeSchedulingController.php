<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\User;
use App\Models\Day;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgrammeSchedulingController extends Controller
{
    public function show(AcademicSession $academicSession, Programme $programme)
    {
        // Get all course unit mappings for this programme in the current academic session
        $mappings = CourseUnitProgrammeMapping::with(['courseUnit', 'instructor.title', 'day', 'semester', 'yearOfStudy'])
            ->where('programme_id', $programme->id)
            ->where('academic_session_id', $academicSession->id)
            ->get();
            
        // Group mappings by year and semester (e.g., 1.1, 1.2, 2.1, etc.)
        $groupedMappings = $mappings->groupBy(function($mapping) {
            $year = $mapping->yearOfStudy->name ?? '0';
            $semester = $mapping->semester->name ?? '0';
            return "{$year}.{$semester}";
        })->sortBy(function($items, $key) {
            // Sort by year and semester (e.g., 1.1 comes before 1.2, 2.1, etc.)
            if (str_contains($key, '.')) {
                list($year, $semester) = explode('.', $key, 2);
                return ((int)$year * 10) + (int)$semester;
            }
            return 999; // Put invalid formats at the end
        });

        // Get all instructors for selection
        $instructors = User::role('instructor')
            ->with('title')
            ->orderBy('name')
            ->get();

        // Get all days for the day dropdown
        $days = Day::orderBy('id')->get();

        return view('admin.programmes.scheduling.show', [
            'programme' => $programme,
            'academicSession' => $academicSession,
            'groupedMappings' => $groupedMappings,
            'instructors' => $instructors,
            'days' => $days,
        ]);
    }

    public function addInstructor(Request $request, AcademicSession $academicSession, Programme $programme)
    {
        $validated = $request->validate([
            'course_unit_id' => 'required|exists:course_units,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // Add instructor to the course unit in this programme and academic session
        CourseUnitProgrammeMapping::updateOrCreate(
            [
                'programme_id' => $programme->id,
                'course_unit_id' => $validated['course_unit_id'],
                'academic_session_id' => $academicSession->id,
            ],
            ['user_id' => $validated['user_id']]
        );

        return back()->with('success', 'Instructor assigned successfully');
    }

    public function removeInstructor(Request $request, AcademicSession $academicSession, Programme $programme)
    {
        $validated = $request->validate([
            'mapping_id' => 'required|exists:course_unit_programme_mappings,id',
        ]);

        $mapping = CourseUnitProgrammeMapping::findOrFail($validated['mapping_id']);
        $mapping->update(['user_id' => null]);

        return back()->with('success', 'Instructor removed successfully');
    }

    public function assignSlot(Request $request, AcademicSession $academicSession, Programme $programme)
    {
        $validated = $request->validate([
            'mapping_id' => 'required|exists:course_unit_programme_mappings,id',
            'day_id' => 'required|exists:days,id',
            'morning_start' => 'nullable|date_format:H:i',
            'morning_duration' => 'nullable|integer|min:1',
            'evening_start' => 'nullable|date_format:H:i',
            'evening_duration' => 'nullable|integer|min:1',
        ]);

        $mapping = CourseUnitProgrammeMapping::findOrFail($validated['mapping_id']);
        
        $updateData = [
            'day_id' => $validated['day_id']
        ];

        // Update morning slot if provided
        if (!empty($validated['morning_start']) && !empty($validated['morning_duration'])) {
            $updateData['morning_start_time'] = $validated['morning_start'];
            $updateData['morning_duration'] = $validated['morning_duration'];
        }

        // Update evening slot if provided
        if (!empty($validated['evening_start']) && !empty($validated['evening_duration'])) {
            $updateData['evening_start_time'] = $validated['evening_start'];
            $updateData['evening_duration'] = $validated['evening_duration'];
        }

        $mapping->update($updateData);

        return back()->with('success', 'Time slot(s) assigned successfully');
    }

    public function updateSlot(Request $request, AcademicSession $academicSession, Programme $programme, $mappingId)
    {
        $validated = $request->validate([
            'day_id' => 'required|exists:days,id',
            'morning_start' => 'nullable|date_format:H:i',
            'morning_duration' => 'nullable|integer|min:1',
            'evening_start' => 'nullable|date_format:H:i',
            'evening_duration' => 'nullable|integer|min:1',
        ]);

        $mapping = CourseUnitProgrammeMapping::findOrFail($mappingId);
        
        $updateData = [
            'day_id' => $validated['day_id']
        ];

        // Update morning session if provided
        if (!empty($validated['morning_start']) && !empty($validated['morning_duration'])) {
            $updateData['morning_start_time'] = $validated['morning_start'];
            $updateData['morning_duration'] = $validated['morning_duration'];
        } else {
            $updateData['morning_start_time'] = null;
            $updateData['morning_duration'] = null;
        }

        // Update evening session if provided
        if (!empty($validated['evening_start']) && !empty($validated['evening_duration'])) {
            $updateData['evening_start_time'] = $validated['evening_start'];
            $updateData['evening_duration'] = $validated['evening_duration'];
        } else {
            $updateData['evening_start_time'] = null;
            $updateData['evening_duration'] = null;
        }

        // If no sessions are set, clear the day_id
        if (empty($validated['morning_start']) && empty($validated['evening_start'])) {
            $updateData['day_id'] = null;
        }

        $mapping->update($updateData);

        return redirect()->route('admin.academic-sessions.programmes.scheduling.show', [
            'academicSession' => $academicSession->id,
            'programme' => $programme->id
        ])->with('success', 'Schedule updated successfully');
    }

    public function deleteSlot(AcademicSession $academicSession, Programme $programme, $mappingId)
    {
        $mapping = CourseUnitProgrammeMapping::findOrFail($mappingId);
        
        // Clear all scheduling information
        $mapping->update([
            'day_id' => null,
            'morning_start_time' => null,
            'morning_duration' => null,
            'evening_start_time' => null,
            'evening_duration' => null,
        ]);

        return back()->with('success', 'Schedule has been cleared');
    }
    
    /**
     * Handle bulk scheduling from CSV upload
     */
    /**
     * Download template with current programme's courses as CSV
     */
    public function downloadCourses(AcademicSession $academicSession, Programme $programme)
    {
        // Get all course unit mappings for this programme in the current academic session
        $mappings = CourseUnitProgrammeMapping::with(['courseUnit', 'instructor', 'day', 'yearOfStudy', 'semester'])
            ->where('programme_id', $programme->id)
            ->where('academic_session_id', $academicSession->id)
            ->orderBy('year_of_study_id')
            ->orderBy('semester_id')
            ->get();
            
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . str_replace(' ', '_', $programme->code) . '_schedule_template_' . now()->format('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($mappings) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers with instructions
            fputcsv($file, [
                'course_code',
                'instructor_email',
                'day',
                'morning_start',
                'morning_duration',
                'evening_start',
                'evening_duration'
            ]);
            
            // Helper function to format time
            $formatTime = function($time) {
                if (empty($time)) return '';
                
                // If it's a Carbon instance, format it
                if (method_exists($time, 'format')) {
                    return $time->format('H:i');
                }
                
                // If it's a string, try to parse it
                if (is_string($time)) {
                    try {
                        return \Carbon\Carbon::parse($time)->format('H:i');
                    } catch (\Exception $e) {
                        // If parsing fails, try to extract time parts
                        if (preg_match('/(\d{1,2}):(\d{2})/', $time, $matches)) {
                            return sprintf('%02d:%02d', $matches[1], $matches[2]);
                        }
                    }
                }
                
                return '';
            };
            
            // Add data rows with current values
            foreach ($mappings as $mapping) {
                fputcsv($file, [
                    $mapping->courseUnit->code ?? '',
                    $mapping->instructor->email ?? '',
                    $mapping->day->name ?? '',
                    $formatTime($mapping->morning_start_time),
                    $mapping->morning_duration ?? '',
                    $formatTime($mapping->evening_start_time),
                    $mapping->evening_duration ?? ''
                ]);
            }
            
            // Add example rows
            fputcsv($file, []); // Empty row for separation
            fputcsv($file, ['-- EXAMPLE ROWS - YOU CAN DELETE THESE LINES --']);
            fputcsv($file, [
                'CSC101',
                'instructor@example.com',
                'Monday',
                '09:00',
                '120',
                '14:00',
                '120'
            ]);
            fputcsv($file, [
                'MAT201',
                'math@example.com',
                'Tuesday',
                '10:00',
                '90',
                '',
                ''
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Handle bulk scheduling from CSV upload
     */
    public function bulkSchedule(Request $request, AcademicSession $academicSession, Programme $programme)
    {
        $request->validate([
            'schedule_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);
        
        $file = $request->file('schedule_file');
        $path = $file->getRealPath();
        
        // Read the CSV file
        $data = array_map('str_getcsv', file($path));
        
        // Remove header
        $header = array_shift($data);
        
        // Normalize header names
        $header = array_map('strtolower', $header);
        $header = array_map('trim', $header);
        
        // Map the CSV data to an array of schedules
        $schedules = [];
        $errors = [];
        
        foreach ($data as $i => $row) {
            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }
            
            // Combine with header
            $rowData = array_combine($header, $row);
            
            // Validate required fields
            if (empty($rowData['course_code'])) {
                $errors[] = "Row " . ($i + 2) . ": Missing course code";
                continue;
            }
            
            if (empty($rowData['day'])) {
                $errors[] = "Row " . ($i + 2) . ": Missing day";
                continue;
            }
            
            // Find the day by name
            $day = \App\Models\Day::whereRaw('LOWER(name) = ?', [strtolower(trim($rowData['day']))])->first();
            
            if (!$day) {
                $errors[] = "Row " . ($i + 2) . ": Invalid day name '{$rowData['day']}'. Must be a valid day name (e.g., Monday, Tuesday, etc.)";
                continue;
            }
            
            // Clean and find the course unit mapping
            $courseCode = trim(preg_replace('/\s+/', ' ', $rowData['course_code']));
            
            // First try exact match
            $mapping = CourseUnitProgrammeMapping::where('programme_id', $programme->id)
                ->where('academic_session_id', $academicSession->id)
                ->whereHas('courseUnit', function($query) use ($courseCode) {
                    $query->where('code', $courseCode);
                })
                ->first();
                
            // If not found, try case-insensitive match
            if (!$mapping) {
                $mapping = CourseUnitProgrammeMapping::where('programme_id', $programme->id)
                    ->where('academic_session_id', $academicSession->id)
                    ->whereHas('courseUnit', function($query) use ($courseCode) {
                        $query->whereRaw('LOWER(TRIM(REPLACE(code, " ", ""))) = ?', [
                            strtolower(str_replace(' ', '', $courseCode))
                        ]);
                    })
                    ->first();
            }
                
            if (!$mapping) {
                $errors[] = "Row " . ($i + 2) . ": Course unit '{$rowData['course_code']}' not found in this programme";
                continue;
            }
            
            // Helper function to normalize time format
            $normalizeTime = function($time) use (&$errors, $i) {
                if (empty($time)) return null;
                
                // Remove any non-numeric characters except colon
                $time = preg_replace('/[^0-9:]/', '', $time);
                
                // Handle various time formats
                if (preg_match('/^(\d{1,2}):?(\d{2})?$/', $time, $matches)) {
                    $hours = (int)$matches[1];
                    $minutes = isset($matches[2]) ? (int)$matches[2] : 0;
                    
                    // Handle 12-hour format if needed
                    if (isset($rowData['ampm']) && strtoupper($rowData['ampm']) === 'PM' && $hours < 12) {
                        $hours += 12;
                    } elseif (isset($rowData['ampm']) && strtoupper($rowData['ampm']) === 'AM' && $hours === 12) {
                        $hours = 0;
                    }
                    
                    // Validate hours and minutes
                    if ($hours < 0 || $hours > 23) {
                        $errors[] = "Row " . ($i + 2) . ": Invalid hour in time. Must be between 00 and 23";
                        return false;
                    }
                    
                    if ($minutes < 0 || $minutes > 59) {
                        $errors[] = "Row " . ($i + 2) . ": Invalid minutes in time. Must be between 00 and 59";
                        return false;
                    }
                    
                    return sprintf('%02d:%02d', $hours, $minutes);
                }
                
                $errors[] = "Row " . ($i + 2) . ": Could not parse time format: {$time}. Using HH:MM (24-hour) format";
                return false;
            };
            
            // Normalize times
            $morningStart = $normalizeTime($rowData['morning_start'] ?? '');
            $eveningStart = $normalizeTime($rowData['evening_start'] ?? '');
            
            if ($morningStart === false || $eveningStart === false) {
                continue; // Skip this row if time parsing failed
            }
            
            // Get instructor if provided
            if (!empty($rowData['instructor_email'])) {
                $instructor = User::where('email', trim($rowData['instructor_email']))
                    ->role('instructor')
                    ->first();
                    
                if ($instructor) {
                    $mapping->user_id = $instructor->id;
                } else {
                    $errors[] = "Row " . ($i + 2) . ": Instructor with email '{$rowData['instructor_email']}' not found";
                    // Continue with scheduling even if instructor not found
                }
            }
            
            // Prepare update data
            $updateData = [
                'day_id' => $day->id
            ];
            
            // Set morning session if provided
            if ($morningStart && !empty($rowData['morning_duration'])) {
                $updateData['morning_start_time'] = $morningStart;
                $updateData['morning_duration'] = intval($rowData['morning_duration']);
            } else {
                $updateData['morning_start_time'] = null;
                $updateData['morning_duration'] = null;
            }
            
            // Set evening session if provided
            if ($eveningStart && !empty($rowData['evening_duration'])) {
                $updateData['evening_start_time'] = $eveningStart;
                $updateData['evening_duration'] = intval($rowData['evening_duration']);
            } else {
                $updateData['evening_start_time'] = null;
                $updateData['evening_duration'] = null;
            }
            
            // If no sessions are set, clear the day_id
            if (empty($updateData['morning_start_time']) && empty($updateData['evening_start_time'])) {
                $updateData['day_id'] = null;
            }
            
            // Update the mapping
            $mapping->update($updateData);
            
            $schedules[] = $mapping->id;
        }
        
        if (!empty($errors)) {
            return back()->withErrors($errors)->with('error', 'Some errors occurred while processing the file. See details below.');
        }
        
        return back()->with('success', 'Bulk scheduling completed successfully. ' . count($schedules) . ' schedules were updated.');
    }
}
