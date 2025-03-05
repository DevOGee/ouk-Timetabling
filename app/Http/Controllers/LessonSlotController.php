<?php

namespace App\Http\Controllers;

use App\Models\CourseUnit;
use App\Models\LessonSlot;
use App\Models\Programme;
use Illuminate\Http\Request;

class LessonSlotController extends Controller
{
    public function store(Request $request, Programme $programme, CourseUnit $courseUnit)
    {
        $request->validate([
            'day_id' => 'required|exists:days,id',
            'start_time' => 'required',
            'duration' => 'required|integer|min:1',
        ]);

        LessonSlot::create([
            'course_unit_id' => $courseUnit->id,
            'programme_id' => $programme->id,
            'day_id' => $request->day_id,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
        ]);

        return redirect()->route('programmes.show', $programme)->with('success', 'Lesson slot assigned successfully.');
    }

    public function update(Request $request, Programme $programme, CourseUnit $courseUnit, LessonSlot $lessonSlot)
    {
        $request->validate([
            'day_id' => 'required|exists:days,id',
            'start_time' => 'required',
            'duration' => 'required|integer|min:1',
        ]);

        $lessonSlot->update([
            'day_id' => $request->day_id,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
        ]);

        return redirect()->route('programmes.show', $programme)->with('success', 'Lesson slot updated successfully.');
    }
}
