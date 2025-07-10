<?php

namespace App\Http\Controllers;

use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Programme;
use Illuminate\Http\Request;

class LessonSlotController extends Controller
{
    public function store(Request $request, Programme $programme, CourseUnit $courseUnit)
    {
        $request->validate([
            'day_id' => 'required|exists:days,id',
            'morning_start_time' => 'nullable|date_format:H:i',
            'morning_duration' => 'nullable|integer|min:1',
            'evening_start_time' => 'nullable|date_format:H:i',
            'evening_duration' => 'nullable|integer|min:1',
        ]);

        // Ensure at least one slot is provided
        if (
            ! $request->filled('morning_start_time') &&
            ! $request->filled('evening_start_time')
        ) {
            return back()->withErrors(['error' => 'Please provide at least a morning or evening slot.']);
        }

        $mapping = CourseUnitProgrammeMapping::where('programme_id', $programme->id)
            ->where('course_unit_id', $courseUnit->id)
            ->firstOrFail();

        $mapping->update([
            'day_id' => $request->day_id,
            'morning_start_time' => $request->morning_start_time,
            'morning_duration' => $request->morning_duration,
            'evening_start_time' => $request->evening_start_time,
            'evening_duration' => $request->evening_duration,
        ]);

        return redirect()->route('programmes.show', $programme)->with('success', 'Slot assigned successfully.');
    }

    public function update(Request $request, Programme $programme, CourseUnit $courseUnit, CourseUnitProgrammeMapping $lessonSlot)
    {
        $request->validate([
            'day_id' => 'required|exists:days,id',
            'morning_start_time' => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',
            'morning_duration' => 'nullable|integer|min:1',
            'evening_start_time' => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',
            'evening_duration' => 'nullable|integer|min:1',
        ]);

        // dd('Update method hit!', $request->all());

        if (
            ! $request->filled('morning_start_time') &&
            ! $request->filled('evening_start_time')
        ) {
            return back()->withErrors(['error' => 'Please provide at least a morning or evening slot.']);
        }

        // Ensure the mapping belongs to this programme and course unit
        if (
            $lessonSlot->programme_id !== $programme->id ||
            $lessonSlot->course_unit_id !== $courseUnit->id
        ) {
            abort(403, 'Invalid mapping reference');
        }

        $lessonSlot->update([
            'day_id' => $request->day_id,
            'morning_start_time' => $request->morning_start_time,
            'morning_duration' => $request->morning_duration,
            'evening_start_time' => $request->evening_start_time,
            'evening_duration' => $request->evening_duration,
        ]);

        // Get the academic session from the request or any other source if available
        $academicSessionId = $request->input('academic_session_id') ?? session('current_academic_session_id');
        
        if ($academicSessionId) {
            return redirect()->route('admin.academic-sessions.programmes.scheduling.show', [
                'academicSession' => $academicSessionId,
                'programme' => $programme->id
            ])->with('success', 'Slot updated successfully.');
        }
        
        // Fallback to the programme show page if no academic session is available
        return redirect()->route('programmes.show', $programme)->with('success', 'Slot updated successfully.');
    }
}
