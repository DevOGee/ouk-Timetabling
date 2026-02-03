<?php

namespace App\Http\Controllers;

use App\Imports\CourseUnitsImport;
use App\Models\CourseUnit;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\YearOfStudy;
use App\Models\Department;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseUnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $departmentId = $request->input('department_id');

        $query = CourseUnit::with(['yearOfStudy', 'semester', 'department']);

        // Apply search filter if query exists
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $perPage = $request->input('per_page', 10);
        $courseUnits = $query->orderBy('code')->paginate($perPage)->appends(['search' => $search, 'department_id' => $departmentId, 'per_page' => $perPage]);
        $departments = Department::orderBy('name')->get();

        return view('course_units.index', compact('courseUnits', 'search', 'departments', 'departmentId', 'perPage'));
    }

    public function show($id)
    {
        $courseUnit = CourseUnit::with(['programmes' => function($query) {
            $query->with(['users' => function($q) {
                $q->role('instructor');
            }]);
        }])->findOrFail($id);

        return view('course_units.show', compact('courseUnit'));
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
        $departments = Department::orderBy('name')->get();

        return view('course_units.create', compact('yearsOfStudy', 'semesters', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            // 'year_of_study_id' => 'required|exists:years_of_study,id',
            // 'semester_id' => 'required|exists:semesters,id',
            'color' => 'nullable|string|max:7', // Allow color input
            'department_id' => 'nullable|exists:departments,id',
        ]);

        CourseUnit::create($request->all());

        return redirect()->route('course_units.index')->with('success', 'Course Unit created successfully');
    }

    // Show form for editing
    public function edit(CourseUnit $course_unit)
    {
        $departments = Department::orderBy('name')->get();
        return view('course_units.edit', compact('course_unit', 'departments'));
    }

    // Update existing course unit
    public function update(Request $request, CourseUnit $course_unit)
    {
        $request->validate([
            'code' => 'required|unique:course_units,code,'.$course_unit->id,
            'name' => 'required|string',
            'color' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $course_unit->update($request->only('code', 'name', 'color', 'department_id'));

        return redirect()->route('course_units.index')->with('success', 'Course unit updated successfully!');
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
            fputcsv($file, ['code', 'name', 'color']);

            // Add sample data
            fputcsv($file, ['MTH101', 'Calculus I', '#ff0000']);
            fputcsv($file, ['CSC201', 'Data Structures', '#00ff00']);
            fputcsv($file, ['PHY301', 'Quantum Physics', '#0000ff']);

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $selectedIds = $request->input('selected_ids', []);
        $targetDepartmentId = $request->input('target_department_id');

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'No items selected.');
        }

        switch ($action) {
            case 'delete':
                CourseUnit::whereIn('id', $selectedIds)->delete();
                return redirect()->back()->with('success', count($selectedIds) . ' course units deleted successfully.');

            case 'move':
                if (!$targetDepartmentId) {
                    return redirect()->back()->with('error', 'Please select a target department to move course units.');
                }
                CourseUnit::whereIn('id', $selectedIds)->update(['department_id' => $targetDepartmentId]);
                return redirect()->back()->with('success', count($selectedIds) . ' course units moved successfully.');

            default:
                return redirect()->back()->with('error', 'Invalid action selected.');
        }
    }
}
