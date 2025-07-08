<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProgrammesImport;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProgrammeController extends Controller
{
    /**
     * Display a listing of the programmes.
     */
    public function index()
    {
        $programmes = Programme::with('school')
            ->when(request('search'), function($query) {
                $search = '%' . request('search') . '%';
                $query->where('name', 'like', $search)
                    ->orWhere('programme_code', 'like', $search)
                    ->orWhereHas('school', function($q) use ($search) {
                        $q->where('name', 'like', $search);
                    });
            })
            ->orderBy('name')
            ->paginate(25);
            
        return view('admin.programmes.index', compact('programmes'));
    }

    /**
     * Show the form for creating a new programme.
     */
    public function create()
    {
        $programme = new Programme();
        $schools = School::orderBy('name')->get();
        return view('admin.programmes.form', compact('schools', 'programme'));
    }
    
    /**
     * Show the form for editing the specified programme.
     */
    public function edit(Programme $programme)
    {
        $schools = School::orderBy('name')->get();
        return view('admin.programmes.form', compact('programme', 'schools'));
    }

    /**
     * Store a newly created programme in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'programme_code' => 'required|string|max:50|unique:programmes,programme_code',
            'school_id' => 'required|exists:schools,id',
        ]);

        Programme::create($validated);

        return redirect()->route('admin.programmes.index')
            ->with('success', 'Programme created successfully.');
    }
    
    /**
     * Update the specified programme in storage.
     */
    public function update(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'programme_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('programmes', 'programme_code')->ignore($programme->id)
            ],
            'school_id' => 'required|exists:schools,id',
        ]);

        $programme->update($validated);

        return redirect()->route('admin.programmes.index')
            ->with('success', 'Programme updated successfully.');
    }
    
    /**
     * Remove the specified programme from storage.
     */
    public function destroy(Programme $programme)
    {
        // Check if the programme has any course unit mappings
        if ($programme->courseUnitMappings()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete programme because it has associated course units.');
        }
        
        $programme->delete();
        
        return redirect()->route('admin.programmes.index')
            ->with('success', 'Programme deleted successfully.');
    }

    /**
     * Show the bulk upload form.
     */
    public function showBulkUploadForm()
    {
        $schools = School::orderBy('name')->get();
        return view('admin.programmes.bulk-upload', compact('schools'));
    }

    /**
     * Process the bulk upload of programmes.
     */
    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240', // Max 10MB
            'school_id' => 'required|exists:schools,id',
        ]);

        try {
            $import = new ProgrammesImport($request->school_id);
            
            // Import the data
            Excel::import($import, $request->file('file'));
            
            // Get import statistics
            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            
            // Prepare success message
            $message = "Successfully imported {$importedCount} programmes.";
            
            // Add warning if some rows were skipped
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} rows were skipped due to errors.";
                
                // If there are specific errors, add them to the session
                if ($import->hasErrors()) {
                    $errors = collect($import->getErrors())->map(function ($error) {
                        return "Row {$error['row']}: " . implode(' ', $error['errors']);
                    })->toArray();
                    
                    return back()
                        ->with('warning', $message)
                        ->with('import_errors', $errors);
                }
            }
            
            return redirect()->route('admin.programmes.index')
                ->with('success', $message);
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }
            
            return back()
                ->with('error', 'There were validation errors in your file.')
                ->with('import_errors', $errors)
                ->withInput();
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error importing programmes: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download the bulk upload template.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="programmes_import_template_' . now()->format('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            
            // Add headers with descriptions
            fputcsv($file, ['programme_code', 'name'], ',');
            
            // Add example rows
            fputcsv($file, ['BSC-IT', 'Bachelor of Science in Information Technology'], ',');
            fputcsv($file, ['BBA', 'Bachelor of Business Administration'], ',');
            fputcsv($file, ['BED-ARTS', 'Bachelor of Education (Arts)'], ',');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
