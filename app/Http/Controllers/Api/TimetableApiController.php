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
                'department_id' => 'required|exists:departments,id',
                'programme_id' => 'required|exists:programmes,id',
                'specialisation_id' => 'nullable|exists:specialisations,id',
            ]);

            $departmentId = (int)$request->input('department_id');
            $programmeId = (int)$request->input('programme_id');
            $specialisationId = $request->input('specialisation_id') ? (int)$request->input('specialisation_id') : null;
            
            // Log the request
            \Log::info("Fetching levels for department and programme", [
                'department_id' => $departmentId,
                'programme_id' => $programmeId,
                'specialisation_id' => $specialisationId
            ]);
            
            // Get distinct year_of_study_id and semester_id combinations
            $query = \DB::table('course_unit_programme_mappings as cupm')
                ->join('programmes as p', 'p.id', '=', 'cupm.programme_id')
                ->join('years_of_study as y', 'y.id', '=', 'cupm.year_of_study_id')
                ->join('semesters as s', 's.id', '=', 'cupm.semester_id')
                ->where('p.department_id', $departmentId)
                ->where('cupm.programme_id', $programmeId);
                
            // Filter by specialisation if provided
            if ($specialisationId) {
                // Include core courses (null specialisation) OR courses for this specific specialisation
                $query->where(function($q) use ($specialisationId) {
                    $q->whereNull('cupm.specialisation_id')
                      ->orWhere('cupm.specialisation_id', $specialisationId);
                });
            }

            $levels = $query->select(
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
                    'department_id' => $departmentId,
                    'programme_id' => $programmeId,
                    'specialisation_id' => $specialisationId,
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
     * Get programmes for a specific department
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProgrammesByDepartment(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        $departmentId = $request->input('department_id');
        
        $programmes = \App\Models\Programme::where('department_id', $departmentId)
            ->orderBy('programme_code')
            ->get(['id', 'programme_code', 'name']);

        return response()->json([
            'success' => true,
            'programmes' => $programmes
        ]);
    }

    /**
     * Get specialisations for a specific programme
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecialisationsByProgramme(Request $request)
    {
        $request->validate([
            'programme_id' => 'required|exists:programmes,id',
        ]);

        $programmeId = $request->input('programme_id');
        
        $specialisations = \App\Models\Specialisation::where('programme_id', $programmeId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'specialisations' => $specialisations
        ]);
    }
}
