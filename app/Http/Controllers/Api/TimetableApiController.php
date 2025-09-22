<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableApiController extends Controller
{
    /**
     * Get levels that have timetables for a specific programme
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLevelsWithTimetables(Request $request)
    {
        try {
            $request->validate([
                'school_id' => 'required|exists:schools,id',
                'programme_id' => 'required|exists:programmes,id',
            ]);

            $schoolId = (int)$request->input('school_id');
            $programmeId = (int)$request->input('programme_id');
            
            // Log the request
            \Log::info("Fetching levels for school and programme", [
                'school_id' => $schoolId,
                'programme_id' => $programmeId
            ]);
            
            // Get distinct year_of_study_id and semester_id combinations
            $levels = \DB::table('course_unit_programme_mappings as cupm')
                ->join('programmes as p', 'p.id', '=', 'cupm.programme_id')
                ->join('years_of_study as y', 'y.id', '=', 'cupm.year_of_study_id')
                ->join('semesters as s', 's.id', '=', 'cupm.semester_id')
                ->where('p.school_id', $schoolId)
                ->where('cupm.programme_id', $programmeId)
                ->select(
                    'cupm.year_of_study_id',
                    'y.name as year_name',
                    'cupm.semester_id',
                    's.name as semester_name'
                )
                ->distinct()
                ->orderBy('cupm.year_of_study_id')
                ->orderBy('cupm.semester_id')
                ->get();
            
            // Format the levels as year.semester (e.g., 1.1, 1.2, 2.1, etc.)
            $formattedLevels = $levels->map(function($level) {
                return [
                    'id' => $level->year_of_study_id . '.' . $level->semester_id,
                    'name' => $level->year_name . '.' . $level->semester_name,
                    'year_id' => $level->year_of_study_id,
                    'semester_id' => $level->semester_id
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedLevels,
                'meta' => [
                    'school_id' => $schoolId,
                    'programme_id' => $programmeId,
                    'total_levels' => $formattedLevels->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error in getLevelsWithTimetables: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'request' => $request->all()
            ], 500);
        }
    }

    /**
     * Get programmes for a specific school
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProgrammesBySchool(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        $schoolId = $request->input('school_id');
        
        $programmes = \App\Models\Programme::where('school_id', $schoolId)
            ->orderBy('programme_code')
            ->get(['id', 'programme_code', 'name']);

        return response()->json([
            'success' => true,
            'programmes' => $programmes
        ]);
    }
}
