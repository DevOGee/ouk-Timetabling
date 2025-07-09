<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\YearOfStudy;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgrammeMappingController extends Controller
{
    /**
     * Show the form for selecting programmes for an academic session.
     */
    public function selectProgrammes(AcademicSession $academicSession)
    {
        $programmes = Programme::with('school')->orderBy('name')->get();
        $selectedProgrammeIds = $academicSession->programmes()->pluck('programmes.id')->toArray();
        
        return view('admin.academic-sessions.select-programmes', compact('academicSession', 'programmes', 'selectedProgrammeIds'));
    }

    /**
     * Save the selected programmes for an academic session.
     */
    public function storeProgrammes(Request $request, AcademicSession $academicSession)
    {
        $request->validate([
            'programmes' => 'required|array',
            'programmes.*' => 'exists:programmes,id'
        ]);

        // Get current programme IDs for this academic session
        $currentProgrammeIds = $academicSession->programmes()->pluck('programmes.id')->toArray();
        $newProgrammeIds = $request->programmes;
        
        // Find newly added programmes (in new but not in current)
        $addedProgrammeIds = array_diff($newProgrammeIds, $currentProgrammeIds);

        // Sync the programmes
        $academicSession->programmes()->sync($newProgrammeIds);

        // Ensure a timetable entry exists for each programme in the session
        foreach ($newProgrammeIds as $programmeId) {
            // Use updateOrCreate to ensure we have a timetable entry
            \App\Models\ProgrammeTimetable::updateOrCreate(
                [
                    'programme_id' => $programmeId,
                    'academic_session_id' => $academicSession->id
                ],
                [
                    'status' => 'draft',
                    'updated_at' => now()
                ]
            );
        }
        
        // Remove timetables for programmes that were removed
        $removedProgrammeIds = array_diff($currentProgrammeIds, $newProgrammeIds);
        if (!empty($removedProgrammeIds)) {
            \App\Models\ProgrammeTimetable::whereIn('programme_id', $removedProgrammeIds)
                ->where('academic_session_id', $academicSession->id)
                ->delete();
        }

        return redirect()
            ->route('admin.academic-sessions.show', $academicSession)
            ->with('success', 'Programmes updated successfully. You can now map course units to each programme.');
    }

    /**
     * Show the form for mapping course units to a programme in an academic session.
     */
    public function mapCourseUnits(AcademicSession $academicSession, Programme $programme)
    {
        $courseUnits = CourseUnit::orderBy('code')->get();
        $yearsOfStudy = YearOfStudy::orderBy('id')->get();
        $semesters = Semester::orderBy('id')->get();
        
        $mappings = $programme->sessionMappings($academicSession->id)
            ->with(['courseUnit', 'yearOfStudy', 'semester'])
            ->get();

        return view('admin.academic-sessions.map-course-units', 
            compact('academicSession', 'programme', 'courseUnits', 'yearsOfStudy', 'semesters', 'mappings')
        );
    }

    /**
     * Save the course unit mappings for a programme in an academic session.
     */
    public function storeCourseUnits(Request $request, AcademicSession $academicSession, Programme $programme)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.course_unit_id' => 'required|exists:course_units,id',
            'mappings.*.year_of_study_id' => 'required|exists:years_of_study,id',
            'mappings.*.semester_id' => 'required|exists:semesters,id'
        ]);

        DB::transaction(function () use ($academicSession, $programme, $request) {
            // Delete existing mappings
            $programme->sessionMappings($academicSession->id)->delete();
            
            // Add new mappings
            foreach ($request->mappings as $mapping) {
                $programme->courseUnitMappings()->create([
                    'academic_session_id' => $academicSession->id,
                    'course_unit_id' => $mapping['course_unit_id'],
                    'year_of_study_id' => $mapping['year_of_study_id'],
                    'semester_id' => $mapping['semester_id']
                ]);
            }
        });

        return redirect()
            ->route('admin.academic-sessions.show', $academicSession)
            ->with('success', 'Course unit mappings updated successfully');
    }

    /**
     * Remove a programme from an academic session.
     */
    public function detach(AcademicSession $academicSession, Programme $programme)
    {
        DB::transaction(function () use ($academicSession, $programme) {
            // Remove all course unit mappings for this programme in the academic session
            $programme->sessionMappings($academicSession->id)->delete();
            
            // Detach the programme from the academic session
            $academicSession->programmes()->detach($programme->id);
        });

        return redirect()
            ->route('admin.academic-sessions.show', $academicSession)
            ->with('success', 'Programme removed from academic session successfully');
    }
}
