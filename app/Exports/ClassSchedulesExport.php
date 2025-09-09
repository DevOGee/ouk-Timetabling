<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\School;
use App\Models\YearOfStudy;
use App\Models\Semester;

class ClassSchedulesExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $academicSessionId;
    protected $schoolId;
    protected $showCourseNames;
    protected $showInstructors;

    public function __construct($academicSessionId, $schoolId = 'all', $showCourseNames = false, $showInstructors = false)
    {
        $this->academicSessionId = $academicSessionId;
        $this->schoolId = $schoolId;
        $this->showCourseNames = $showCourseNames;
        $this->showInstructors = $showInstructors;
    }

    /**
     * Get the title for the sheet.
     *
     * @return string
     */
    public function title(): string
    {
        $title = 'Class_schedule_';
        
        if ($this->schoolId !== 'all') {
            $school = School::find($this->schoolId);
            $title .= $school->code ?? 'school';
        } else {
            $title .= 'all_schools';
        }
        
        // Ensure the title is valid for Excel (no invalid characters and max 31 chars)
        $title = preg_replace('/[\/\\\*\?\[\]:]/', '', $title); // Remove invalid Excel sheet name characters
        return mb_substr($title, 0, 31);
    }

    /**
     * Get the view that should be rendered.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $school = $this->schoolId !== 'all' ? School::findOrFail($this->schoolId) : null;
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $scheduleData = collect();

        // Get all course unit mappings for the selected school and academic session
        // Always load these relationships
        $withRelations = [
            'courseUnit',
            'day',
            'programme',
            'yearOfStudy',
            'semester',
            'instructor' // Main instructor for the mapping
        ];
        
        // Conditionally load course unit instructors if needed
        if ($this->showInstructors) {
            $withRelations[] = 'courseUnit.instructors';
        }
        
        $query = CourseUnitProgrammeMapping::with($withRelations)
            ->where('academic_session_id', $this->academicSessionId);
            
        if ($this->schoolId !== 'all') {
            $query->whereHas('programme', function($q) {
                $q->where('school_id', $this->schoolId);
            });
        }
        
        $mappings = $query->get();

        // Group by programme
        $groupedByProgramme = $mappings->groupBy('programme_id');
        
        foreach ($groupedByProgramme as $programmeId => $programmeMappings) {
            $programme = $programmeMappings->first()->programme;
            $programmeData = [
                'programme_code' => $programme->code,
                'programme_name' => $programme->name,
                'schedules' => []
            ];
            
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
                            $rowData['days'][$dayName] = $dayMappings->map(function($mapping) {
$instructors = [];
                                
                                if ($this->showInstructors) {
                                    // Check both direct instructor relationship and any additional instructors
                                    $allInstructors = collect();
                                    
                                    // Add the main instructor if exists
                                    if ($mapping->instructor) {
                                        $allInstructors->push([
                                            'name' => $mapping->instructor->name,
                                            'email' => $mapping->instructor->email
                                        ]);
                                    }
                                    
                                    // Add any additional instructors from the course unit if needed
                                    if ($mapping->courseUnit && $mapping->courseUnit->instructors) {
                                        foreach ($mapping->courseUnit->instructors as $instructor) {
                                            // Avoid duplicates
                                            if (!$allInstructors->contains('email', $instructor->email)) {
                                                $allInstructors->push([
                                                    'name' => $instructor->name,
                                                    'email' => $instructor->email
                                                ]);
                                            }
                                        }
                                    }
                                    
                                    $instructors = $allInstructors->toArray();
                                    
                                    \Log::debug('Instructor data for course ' . $mapping->courseUnit->code . ':', [
                                        'instructors' => $instructors
                                    ]);
                                }
                                
                                return [
                                    'code' => $mapping->courseUnit->code,
                                    'name' => $mapping->courseUnit->name,
                                    'programme_code' => $mapping->programme->code,
                                    'instructors' => $instructors
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

        return view('admin.reports.exports.class-schedules', [
            'school' => $school,
            'scheduleData' => $scheduleData,
            'days' => $days,
            'showCourseNames' => $this->showCourseNames,
            'showInstructors' => $this->showInstructors
        ]);
    }
}
