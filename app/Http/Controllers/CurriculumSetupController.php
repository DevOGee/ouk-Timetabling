<?php

namespace App\Http\Controllers;

use App\Imports\CurriculumMappingImport;
use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Programme;
use App\Models\School;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CurriculumSetupController extends Controller
{
    // View all programmes
    public function index()
    {
        $programmes = Programme::all();
        $schools = School::with('programmes')->get();

        return view('curriculum.index', compact('programmes', 'schools'));
    }

    // View mappings for a single programme
    public function show($programme_id)
    {
        $programme = Programme::with([
            'courseUnitMappings.courseUnit',
            'courseUnitMappings.yearOfStudy',
            'courseUnitMappings.semester',
            'courseUnitMappings.lecturer',
        ])->findOrFail($programme_id);

        // Get course units not yet mapped to this programme
        $mappedCourseIds = CourseUnitProgrammeMapping::where('programme_id', $programme->id)->pluck('course_unit_id')->toArray();

        $courseUnits = CourseUnit::whereNotIn('id', $mappedCourseIds)->get();

        $years = YearOfStudy::all();
        $semesters = Semester::all();

        // Sort mappings by Year and Semester IDs
        $groupedMappings = $programme->courseUnitMappings
            ->sortBy([
                fn ($item) => $item->yearOfStudy->id, // First, sort by Year (ascending order)
                fn ($item) => $item->semester->id,   // Then, sort by Semester (ascending order)
            ])
            ->groupBy(function ($item) {
                // Group by Year and Semester
                return 'Year '.$item->yearOfStudy->name.' - Semester '.$item->semester->name;
            });

        return view('curriculum.show', compact('programme', 'courseUnits', 'years', 'semesters', 'groupedMappings'));
    }

    // Map a course to programme, year, semester
    public function store(Request $request)
    {
        $validated = $request->validate([
            'programme_id' => 'required|exists:programmes,id',
            'course_unit_ids' => 'required|array',
            'course_unit_ids.*' => 'exists:course_units,id',
            'year_of_study_id' => 'required|exists:years_of_study,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        foreach ($validated['course_unit_ids'] as $courseId) {
            CourseUnitProgrammeMapping::updateOrCreate([
                'programme_id' => $validated['programme_id'],
                'course_unit_id' => $courseId,
                'year_of_study_id' => $validated['year_of_study_id'],
                'semester_id' => $validated['semester_id'],
            ]);
        }

        return back()->with('success', 'Course unit(s) mapped successfully!');
    }

    // Unmap a course from a programme
    public function destroy($id)
    {
        CourseUnitProgrammeMapping::findOrFail($id)->delete();

        return back()->with('success', 'Course mapping removed.');
    }

    // Handle CSV upload
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $import = new CurriculumMappingImport;
            Excel::import($import, $request->file('csv_file'));

            return back()->with([
                'success_report' => $import->successes,
                'error_report' => $import->failures,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Import failed: '.$e->getMessage()]);
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sample_curriculum_mapping.csv',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add header row
            fputcsv($file, ['programme_code', 'course_code', 'year_of_study', 'semester']);

            // Add sample rows
            fputcsv($file, ['BCS', 'MTH101', '1', '1']);
            fputcsv($file, ['BCS', 'CSC201', '2', '1']);
            fputcsv($file, ['BBA', 'ACC103', '1', '2']);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
