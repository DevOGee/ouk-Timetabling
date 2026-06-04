<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\Exam;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class ExamReportController extends Controller
{
    /**
     * Display exam conflicts report
     */
    public function conflicts(Request $request)
    {
        // Get active schedule or requested one
        $scheduleId = $request->input('exam_schedule_id');
        
        $activeSchedule = $scheduleId 
            ? ExamSchedule::findOrFail($scheduleId)
            : ExamSchedule::where('is_active', true)->latest()->first();
            
        // Get all schedules for dropdown
        $examSchedules = ExamSchedule::orderBy('start_date', 'desc')->get();
        
        $conflicts = collect();
        
        if ($activeSchedule) {
            // Eager load everything needed for conflict checking
            // We need mapping (level/programme/semester), courseUnit, invigilator
            $exams = Exam::where('exam_schedule_id', $activeSchedule->id)
                ->whereNotNull('exam_date')
                ->whereNotNull('start_time')
                ->with(['courseUnit', 'invigilator', 'mapping.yearOfStudy', 'mapping.semester', 'mapping.programme'])
                ->get();
                
            // Group by Date for initial filtering
            $examsByDate = $exams->groupBy(function($exam) {
                return $exam->exam_date->format('Y-m-d');
            });
            
            foreach ($examsByDate as $date => $daysExams) {
                // Check every pair of exams on this day
                for ($i = 0; $i < $daysExams->count(); $i++) {
                    for ($j = $i + 1; $j < $daysExams->count(); $j++) {
                        $examA = $daysExams[$i];
                        $examB = $daysExams[$j];
                        
                        // 1. Check Time Overlap
                        if ($this->hasTimeOverlap($examA, $examB)) {
                            
                            // 2. Check Invigilator Conflict
                            if ($examA->user_id && $examB->user_id && 
                                $examA->user_id == $examB->user_id) {
                                
                                $conflicts->push((object)[
                                    'type' => 'Invigilator Conflict',
                                    'date' => $examA->exam_date,
                                    'time_a' => $this->formatTimeRange($examA),
                                    'time_b' => $this->formatTimeRange($examB),
                                    'exam_a' => $examA,
                                    'exam_b' => $examB,
                                    'detail' => $examA->invigilator->name . ' is assigned to both exams.'
                                ]);
                            }
                            
                            // 3. Check Student Conflict (Same Programme & Level)
                            if ($examA->mapping && $examB->mapping) {
                                $mapA = $examA->mapping;
                                $mapB = $examB->mapping;
                                
                                // Conflict if: Same Programme AND Same Level (Year) AND Same Semester
                                // AND neither is a retake/elective that allows overlap (assuming strictly enforced levels for now)
                                if ($mapA->programme_id == $mapB->programme_id && 
                                    $mapA->year_of_study_id == $mapB->year_of_study_id &&
                                    $mapA->semester_id == $mapB->semester_id) {
                                    
                                    $conflicts->push((object)[
                                        'type' => 'Student Conflict',
                                        'date' => $examA->exam_date,
                                        'time_a' => $this->formatTimeRange($examA),
                                        'time_b' => $this->formatTimeRange($examB),
                                        'exam_a' => $examA,
                                        'exam_b' => $examB,
                                        'detail' => $mapA->programme->name . ' students (' . 
                                                    $mapA->yearOfStudy->name . '.' . $mapA->semester->name . 
                                                    ') have both exams.'
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Sort conflicts by date
        $conflicts = $conflicts->sortBy('date');

        return view('admin.reports.exam-conflicts', compact('conflicts', 'activeSchedule', 'examSchedules'));
    }

    private function hasTimeOverlap($examA, $examB)
    {
        $startA = Carbon::parse($examA->start_time);
        $endA = $startA->copy()->addMinutes($examA->duration_minutes);
        
        $startB = Carbon::parse($examB->start_time);
        $endB = $startB->copy()->addMinutes($examB->duration_minutes);
        
        return $startA < $endB && $startB < $endA;
    }
    
    private function formatTimeRange($exam)
    {
        $start = Carbon::parse($exam->start_time);
        $end = $start->copy()->addMinutes($exam->duration_minutes);
        return $start->format('H:i') . ' - ' . $end->format('H:i');
    }
}
