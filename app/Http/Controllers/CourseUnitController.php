<?php

namespace App\Http\Controllers;

use App\Imports\CourseUnitsImport;
use App\Models\CourseUnit;
use App\Models\Day;
use App\Models\Lecturer;
use App\Models\Programme;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseUnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = CourseUnit::with(['yearOfStudy', 'semester']);

        // Apply search filter if query exists
        if ($search) {
            $query->where('code', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%");
        }

        $courseUnits = $query->paginate(10)->appends(['search' => $search]);

        return view('course_units.index', compact('courseUnits', 'search'));
    }

    public function show(CourseUnit $courseUnit)
    {
        $lecturers = Lecturer::whereDoesntHave('courses', function ($query) use ($courseUnit) {
            $query->where('course_unit_id', $courseUnit->id);
        })->get();

        return view('course_units.show', compact('courseUnit', 'lecturers'));
    }

    // public function create()
    // {
    //     return view('course_units.create');
    // }

    public function showUploadForm()
    {
        return view('course_units.upload');
    }

    public function importCourseUnits(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        try {
            // Create an instance of the import class
            $import = new CourseUnitsImport;
            Excel::import($import, $request->file('file'));

            // Get the count of successfully added courses
            $importedCount = $import->importedCount;

            return redirect()->route('course_units.index')->with(
                'success',
                "{$importedCount} course units imported successfully!"
            );

        } catch (\Exception $e) {
            // Log::error('CSV Import Error: '.$e->getMessage());

            return back()->withErrors(['error' => 'Error importing file: '.$e->getMessage()]);
        }
    }

    public function create()
    {
        $yearsOfStudy = YearOfStudy::all();
        $semesters = Semester::all();

        return view('course_units.create', compact('yearsOfStudy', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'year_of_study_id' => 'required|exists:years_of_study,id',
            'semester_id' => 'required|exists:semesters,id',
            'color' => 'nullable|string|max:7', // Allow color input
        ]);

        CourseUnit::create($request->all());

        return redirect()->route('course_units.index')->with('success', 'Course Unit created successfully');
    }

    public function edit(CourseUnit $courseUnit)
    {
        $yearsOfStudy = YearOfStudy::all();
        $semesters = Semester::all();
        $programmes = Programme::all();
        $lecturers = Lecturer::all();
        $days = Day::all(); // Ensure you have days loaded

        return view('course_units.edit', compact('courseUnit', 'yearsOfStudy', 'semesters', 'programmes', 'lecturers', 'days'));
    }

    public function update(Request $request, CourseUnit $courseUnit)
    {
        $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'year_of_study_id' => 'required|exists:years_of_study,id',
            'semester_id' => 'required|exists:semesters,id',
            'color' => 'nullable|string|max:7', // Validate hex color
        ]);

        $courseUnit->update($request->all());

        return redirect()->route('course_units.index')->with('success', 'Course Unit updated successfully');
    }

    public function destroy(CourseUnit $courseUnit)
    {
        $courseUnit->delete();

        return redirect()->route('course_units.index')->with('success', 'Course Unit deleted successfully.');
    }

    public function downloadSampleCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sample_course_units.csv',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add CSV header row
            fputcsv($file, ['code', 'name', 'year_of_study_id', 'semester_id', 'color']);

            // Add sample data
            fputcsv($file, ['MTH101', 'Calculus I', 1, 1, '#ff0000']);
            fputcsv($file, ['CSC201', 'Data Structures', 2, 1, '#00ff00']);
            fputcsv($file, ['PHY301', 'Quantum Physics', 3, 2, '#0000ff']);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
