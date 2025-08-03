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
        
        // Get all course units assigned to this instructor with their schedules
        $courseUnits = CourseUnitProgrammeMapping::with([
                'courseUnit',
                'programme',
                'day',
                'academicSession',
                'yearOfStudy',
                'semester'
            ])
            ->where('user_id', $instructor->id)
            ->orderBy('academic_session_id', 'desc')
            ->orderBy('programme_id')
            ->orderBy('course_unit_id')
            ->get()
            ->groupBy('academic_session_id');

        return view('instructor.course-units', [
            'courseUnits' => $courseUnits,
            'instructor' => $instructor
        ]);
    }
}
