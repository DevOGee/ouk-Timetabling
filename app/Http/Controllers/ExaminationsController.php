<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use Illuminate\Http\Request;

class ExaminationsController extends Controller
{
    public function index(Request $request)
    {
        $activeSchedule = ExamSchedule::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now()) // Optional: logic to find "current" schedule
            ->first();

        // If no currently active one by date, just get the one marked is_active
        if (!$activeSchedule) {
            $activeSchedule = ExamSchedule::where('is_active', true)->latest()->first();
        }

        if (!$activeSchedule) {
            return view('examinations.no-schedule');
        }

        $query = $activeSchedule->exams()
            ->with(['courseUnit.programmes' => function ($query) use ($activeSchedule) {
                // Filter pivot by academic session to only show relevant programmes
                $query->wherePivot('academic_session_id', $activeSchedule->academic_session_id);
            }])
            ->orderBy('exam_date')
            ->orderBy('start_time');

        // Filter by School
        if ($request->filled('school')) {
            $query->whereHas('courseUnit.programmes', function ($q) use ($request) {
                $q->where('school_id', $request->school);
            });
        }

        // Filter by Programme
        if ($request->filled('programme')) {
            $query->whereHas('courseUnit.programmes', function ($q) use ($request) {
                $q->where('programmes.id', $request->programme);
            });
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('exam_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('exam_date', '<=', $request->end_date);
        }

        $exams = $query->get()
            ->groupBy(function($exam) {
                return $exam->exam_date ? $exam->exam_date->format('Y-m-d') : 'Unscheduled';
            });

        // Get filter data
        $schools = \App\Models\School::with('programmes')->get(); // Eager load for dependent dropdown logic if passing to view

        return view('examinations.index', compact('activeSchedule', 'exams', 'schools'));
    }
}
