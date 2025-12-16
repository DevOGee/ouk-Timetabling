<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Exam;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExamImport;

class ExamController extends Controller
{
    public function index()
    {
        $schedules = ExamSchedule::with('academicSession')->latest()->paginate(10);
        return view('admin.exams.index', compact('schedules'));
    }

    public function create()
    {
        $sessions = AcademicSession::latest()->get();
        return view('admin.exams.create', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $schedule = ExamSchedule::create($request->all());

        return redirect()->route('admin.exams.show', $schedule)
            ->with('success', 'Exam schedule created successfully.');
    }

    public function show(ExamSchedule $examSchedule)
    {
        $examSchedule->load(['exams.courseUnit', 'exams.mapping.programme', 'exams.invigilator']);
        
        $exams = $examSchedule->exams()
            ->join('course_units', 'exams.course_unit_id', '=', 'course_units.id')
            ->select('exams.*') // Avoid column collisions
            ->orderBy('exams.exam_date')
            ->orderBy('exams.start_time')
            ->orderBy('course_units.code')
            ->orderBy('exams.start_time')
            ->orderBy('course_units.code')
            ->paginate(20);

        return view('admin.exams.show', compact('examSchedule', 'exams'));
    }

    public function rollover(Request $request, ExamSchedule $examSchedule)
    {
        set_time_limit(300); // Increase limit to 300 seconds (5 minutes)

        // Get all active mappings for the session
        $mappings = CourseUnitProgrammeMapping::where('academic_session_id', $examSchedule->academic_session_id)
            ->with(['courseUnit', 'instructor'])
            ->get();

        $count = 0;
        
        DB::transaction(function () use ($mappings, $examSchedule, &$count) {
            // Track processed course units to handle duplicates within the current batch
            $processedCourseUnits = [];

            foreach ($mappings as $mapping) {
                // Skip if we've already processed this course unit in this batch
                if (in_array($mapping->course_unit_id, $processedCourseUnits)) {
                    continue;
                }

                // Check if already exists in the database to prevent dupes
                $exists = Exam::where('exam_schedule_id', $examSchedule->id)
                    ->where('course_unit_id', $mapping->course_unit_id)
                    ->exists();

                if (!$exists) {
                    Exam::create([
                        'exam_schedule_id' => $examSchedule->id,
                        'course_unit_programme_mapping_id' => $mapping->id, // Link to the first mapping found
                        'course_unit_id' => $mapping->course_unit_id,
                        'user_id' => $mapping->user_id, // Default invigilator is the instructor of the first mapping
                        // Date/Time left null for manual scheduling
                    ]);
                    $count++;
                    $processedCourseUnits[] = $mapping->course_unit_id;
                }
            }
        });

        return back()->with('success', "Rolled over {$count} course units into the exam schedule.");
    }
    
    public function import(Request $request, ExamSchedule $examSchedule)
    {
        set_time_limit(300); // Increase limit to 300 seconds (5 minutes)

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        Excel::import(new ExamImport($examSchedule->id, $examSchedule->academic_session_id), $request->file('file'));

        return back()->with('success', 'Exam schedule imported successfully.');
    }

    public function updateSlot(Request $request, Exam $exam)
    {
        $request->validate([
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable', // Flexible validation, strict could be date_format:H:i
            'duration_minutes' => 'nullable|integer|min:1',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
        $exam->update([
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'duration_minutes' => $request->duration_minutes ?? 120,
            'user_id' => $request->user_id,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Slot updated']);
    }

    public function destroy(ExamSchedule $examSchedule)
    {
        $examSchedule->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam schedule deleted.');
    }
}
