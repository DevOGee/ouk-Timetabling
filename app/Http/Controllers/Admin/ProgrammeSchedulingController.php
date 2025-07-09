<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\User;
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

        return view('admin.programmes.scheduling.show', [
            'programme' => $programme,
            'academicSession' => $academicSession,
            'groupedMappings' => $groupedMappings,
            'instructors' => $instructors,
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

        return back()->with('success', 'Schedule updated successfully');
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
}
