<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CourseUnitProgrammeMapping;
use Illuminate\Support\Facades\Auth;

class InstructorDashboardController extends Controller
{
    /**
     * Display the instructor's course units with schedule
     *
     * @return \Illuminate\View\View
     */
    public function myCourseUnits()
    {
        $instructor = Auth::user();
        
        // Get the active academic session
        $activeSession = \App\Models\AcademicSession::where('status', 'active')->first();
        
        if (!$activeSession) {
            return view('instructor.course-units', [
                'courseUnits' => collect(),
                'activeSession' => null
            ]);
        }
        
        // Get all course units assigned to this instructor for the active session, grouped by course code
        $courseUnits = CourseUnitProgrammeMapping::with([
                'courseUnit' => function($query) {
                    $query->orderBy('code')
                          ->with('yearOfStudy', 'semester');
                },
                'programme',
                'yearOfStudy',
                'semester',
                'day'
            ])
            ->where('user_id', $instructor->id)
            ->where('academic_session_id', $activeSession->id)
            ->join('course_units', 'course_unit_programme_mappings.course_unit_id', '=', 'course_units.id')
            ->orderBy('course_units.code')
            ->get()
            ->groupBy(function($mapping) {
                return $mapping->courseUnit->code;
            });
            
        return view('instructor.course-units', [
            'courseUnits' => $courseUnits,
            'activeSession' => $activeSession
        ]);
    }
    
    /**
     * Display the instructor's personal timetable
     *
     * @return \Illuminate\View\View
     */
    public function myTimetable()
    {
        $instructor = Auth::user();
        
        // Get the active academic session
        $activeSession = \App\Models\AcademicSession::where('status', 'active')->first();
        
        if (!$activeSession) {
            return view('instructor.timetable', [
                'timetable' => collect(),
                'days' => collect(),
                'activeSession' => null
            ]);
        }
        
        // Get all weekdays for the timetable (Monday to Friday, IDs 1-5)
        $days = \App\Models\Day::whereIn('id', [1, 2, 3, 4, 5])->orderBy('id')->get();
        
        // Get all course units assigned to this instructor for the active session
        $timetable = CourseUnitProgrammeMapping::with([
                'courseUnit',
                'programme',
                'day',
                'yearOfStudy',
                'semester',
                'lecturer'
            ])
            ->where('user_id', $instructor->id)
            ->where('academic_session_id', $activeSession->id)
            ->where(function($query) {
                $query->whereNotNull('morning_start_time')
                      ->orWhereNotNull('evening_start_time');
            })
            ->get()
            ->map(function($mapping) {
                // Add session_start_time and session_duration for the timetable partial
                if ($mapping->morning_start_time) {
                    $mapping->session_start_time = $mapping->morning_start_time;
                    $mapping->session_duration = $mapping->morning_duration;
                    $mapping->session = 'Morning';
                } elseif ($mapping->evening_start_time) {
                    $mapping->session_start_time = $mapping->evening_start_time;
                    $mapping->session_duration = $mapping->evening_duration;
                    $mapping->session = 'Evening';
                }
                
                // Ensure the course unit has a color
                if ($mapping->courseUnit && !$mapping->courseUnit->color) {
                    // Generate a consistent color based on the course code
                    $mapping->courseUnit->color = '#' . substr(md5($mapping->courseUnit->code), 0, 6);
                }
                
                return $mapping;
            });
            
        return view('instructor.timetable', [
            'timetable' => $timetable,
            'days' => $days,
            'activeSession' => $activeSession
        ]);
    }
}
