<?php

namespace App\Http\Controllers;

use App\Imports\CourseMappingImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class CourseMappingController extends Controller
{
    /**
     * Show the upload form.
     */
    public function showUploadForm()
    {
        return view('course_mapping.upload');
    }

    /**
     * Handle the import.
     */
    public function importCourseMappings(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        try {
            $import = new CourseMappingImport;
            Excel::import($import, $request->file('file'));

            $successCount = $import->getRowCount();
            $skippedCount = $import->getSkippedRows();

            return redirect()->route('course_mapping.upload')
                ->with('success', "{$successCount} Course Mappings imported successfully! {$skippedCount} rows were skipped due to missing programme or course codes.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error importing file: '.$e->getMessage()]);
        }
    }

    public function downloadSampleCsv()
    {
        $csvData = "programme_code,course_code,instructor_email\n";
        $csvData .= "CS101,MATH101,lecturer1@university.ac.ke\n";
        $csvData .= "CS101,COMP201,lecturer2@university.ac.ke\n";
        $csvData .= "ENG202,ENG101,\n";
        $csvData .= "ENG202,ENG102,lecturer3@university.ac.ke\n";

        $fileName = 'sample_course_mapping.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }
}
