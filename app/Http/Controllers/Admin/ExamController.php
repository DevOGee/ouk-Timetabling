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

        if ($request->has('is_active')) {
            ExamSchedule::where('id', '!=', $schedule->id)->update(['is_active' => false]);
        }

        return redirect()->route('admin.exams.show', $schedule)
            ->with('success', 'Exam schedule created successfully.');
    }

    public function edit(ExamSchedule $examSchedule)
    {
        $sessions = AcademicSession::latest()->get();
        return view('admin.exams.edit', compact('examSchedule', 'sessions'));
    }

    public function update(Request $request, ExamSchedule $examSchedule)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $data = $request->except(['_token', '_method']);
        $data['is_active'] = $request->has('is_active');
        
        $examSchedule->update($data);

        if ($request->has('is_active')) {
            // Check if active changed true, disable others
            ExamSchedule::where('id', '!=', $examSchedule->id)->update(['is_active' => false]);
        }

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam schedule updated successfully.');
    }

    public function show(Request $request, ExamSchedule $examSchedule)
    {
        $examSchedule->load(['exams.courseUnit', 'exams.mapping.programme', 'exams.invigilator']);
        
        $query = $examSchedule->exams()
            ->join('course_units', 'exams.course_unit_id', '=', 'course_units.id')
            ->leftJoin('users', 'exams.user_id', '=', 'users.id')
            ->select('exams.*');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_units.code', 'like', "%{$search}%")
                  ->orWhere('course_units.name', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%");
            });
        }
            
        $exams = $query->orderBy('exams.exam_date')
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

        $behavior = $request->input('import_behavior', 'update');
        $import = new ExamImport($examSchedule->id, $examSchedule->academic_session_id, $behavior);
        Excel::import($import, $request->file('file'));

        $message = "Import processing complete. Updated/Created: {$import->getRowCount()}. Skipped: {$import->getSkippedCount()}.";

        if ($import->getSkippedCount() > 0) {
            $message .= " (Skipped rows likely had invalid Course Codes)";
        }

        return back()->with('success', $message);
    }

    public function updateSlot(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable', 
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $data = [];
        if ($request->has('exam_date')) $data['exam_date'] = $request->exam_date;
        if ($request->has('start_time')) $data['start_time'] = $request->start_time;
        if ($request->has('duration_minutes')) $data['duration_minutes'] = $request->duration_minutes;
        // Only update user_id if explicitly provided (prevent accidental nulling)
        if ($request->has('user_id')) $data['user_id'] = $request->user_id;

        $exam->update($data);

        return back()->with('success', 'Exam schedule updated successfully.');
    }

    public function exportUnscheduled(ExamSchedule $examSchedule)
    {
        $exams = $examSchedule->exams()
            ->where(function($query) {
                $query->whereNull('exam_date')
                      ->orWhereNull('start_time');
            })
            ->with('courseUnit')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="unscheduled_exams_' . $examSchedule->id . '.csv"',
        ];

        $callback = function() use ($exams) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['course_code', 'date', 'start_time', 'duration']);

            foreach ($exams as $exam) {
                fputcsv($file, [
                    $exam->courseUnit->code ?? '',
                    '', // Leave date blank for user to fill
                    '', // Leave time blank for user to fill
                    $exam->duration_minutes ?? 120
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(ExamSchedule $examSchedule)
    {
        $examSchedule->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam schedule deleted.');
    }

    public function toggleStatus(ExamSchedule $examSchedule)
    {
        $examSchedule->update(['is_active' => !$examSchedule->is_active]);
        $status = $examSchedule->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Exam schedule {$status} successfully.");
    }

    public function publish(ExamSchedule $examSchedule)
    {
        $examSchedule->update(['is_published' => true]);
        return back()->with('success', 'Exam schedule published successfully.');
    }

    public function unpublish(ExamSchedule $examSchedule)
    {
        $examSchedule->update(['is_published' => false]);
        return back()->with('success', 'Exam schedule unpublished successfully.');
    }
}
