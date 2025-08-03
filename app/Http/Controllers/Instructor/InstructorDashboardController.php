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
            ->groupBy(function($item) {
                return $item->courseUnit->code;
            });

        return view('instructor.course-units', [
            'courseUnits' => $courseUnits,
            'instructor' => $instructor
        ]);
    }
}
