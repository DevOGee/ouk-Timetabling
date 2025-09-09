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

    public function __construct($academicSessionId, $schoolId, $showCourseNames, $showInstructors = false)
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
        $school = School::find($this->schoolId);
        $title = 'Class_schedule_' . ($school->code ?? 'export');
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
        $school = School::findOrFail($this->schoolId);
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $scheduleData = collect();

        // Get all course unit mappings for the selected school and academic session
        $withRelations = [
            'courseUnit',
            'day',
            'programme',
            'yearOfStudy',
            'semester',
            'instructor' // Instructor relationship
        ];
        
        if ($this->showInstructors) {
            $withRelations[] = 'courseUnit.instructors';
        }
        
        $mappings = CourseUnitProgrammeMapping::with($withRelations)
        ->whereHas('programme', function($query) {
            $query->where('school_id', $this->schoolId);
        })
        ->where('academic_session_id', $this->academicSessionId)
        ->get();

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
                                $showInstructors = $this->showInstructors;
                                return [
                                    'code' => $mapping->courseUnit->code,
                                    'name' => $mapping->courseUnit->name,
                                    'programme_code' => $mapping->programme->code,
                                    'instructors' => $this->showInstructors ? 
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

        return view('admin.reports.exports.class-schedules', [
            'school' => $school,
            'scheduleData' => $scheduleData,
            'days' => $days,
            'showCourseNames' => $this->showCourseNames,
            'showInstructors' => $this->showInstructors
        ]);
    }
}
