<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\YearOfStudy;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class ProgrammeMappingController extends Controller
{
    /**
     * Show the form for selecting programmes for an academic session.
     */
    public function selectProgrammes(AcademicSession $academicSession)
    {
        $programmes = Programme::with('school')->orderBy('name')->get();
        $selectedProgrammeIds = $academicSession->programmes()->pluck('programmes.id')->toArray();
        
        return view('admin.academic-sessions.select-programmes', compact('academicSession', 'programmes', 'selectedProgrammeIds'));
    }

    /**
     * Save the selected programmes for an academic session.
     */
    public function storeProgrammes(Request $request, AcademicSession $academicSession)
    {
        $request->validate([
            'programmes' => 'required|array',
            'programmes.*' => 'exists:programmes,id'
        ]);

        // Get current programme IDs for this academic session
        $currentProgrammeIds = $academicSession->programmes()->pluck('programmes.id')->toArray();
        $newProgrammeIds = $request->programmes;
        
        // Find newly added programmes (in new but not in current)
        $addedProgrammeIds = array_diff($newProgrammeIds, $currentProgrammeIds);

        // Sync the programmes
        $academicSession->programmes()->sync($newProgrammeIds);

        // Ensure a timetable entry exists for each programme in the session
        foreach ($newProgrammeIds as $programmeId) {
            // Use updateOrCreate to ensure we have a timetable entry
            \App\Models\ProgrammeTimetable::updateOrCreate(
                [
                    'programme_id' => $programmeId,
                    'academic_session_id' => $academicSession->id
                ],
                [
                    'status' => 'draft',
                    'updated_at' => now()
                ]
            );
        }
        
        // Remove timetables for programmes that were removed
        $removedProgrammeIds = array_diff($currentProgrammeIds, $newProgrammeIds);
        if (!empty($removedProgrammeIds)) {
            \App\Models\ProgrammeTimetable::whereIn('programme_id', $removedProgrammeIds)
                ->where('academic_session_id', $academicSession->id)
                ->delete();
        }

        return redirect()
            ->route('admin.academic-sessions.show', $academicSession)
            ->with('success', 'Programmes updated successfully. You can now map course units to each programme.');
    }

    /**
     * Show the form for mapping course units to a programme in an academic session.
     */
    public function mapCourseUnits(AcademicSession $academicSession, Programme $programme)
    {
        // Get all specialisations for this programme
        $specialisations = $programme->specialisations()->orderBy('name')->get();
        
        // Get the selected specialisation filter (if any)
        $selectedSpecialisationId = request('specialisation_id');
        
        $courseUnits = CourseUnit::orderBy('code')->get();
        $yearsOfStudy = YearOfStudy::orderBy('id')->get();
        $semesters = Semester::orderBy('id')->get();
        
        // Build mappings query
        $mappingsQuery = $programme->sessionMappings($academicSession->id)
            ->with(['courseUnit', 'yearOfStudy', 'semester', 'specialisation']);
        
        // Filter by specialisation if selected
        if ($selectedSpecialisationId && $selectedSpecialisationId !== 'all') {
            if ($selectedSpecialisationId === 'core_only') {
                $mappingsQuery->core();
            } else {
                $mappingsQuery->forSpecialisation($selectedSpecialisationId);
            }
        }
        
        $mappings = $mappingsQuery->get();
        
        // Group by level and build tabs structure
        $tabs = collect();
        $mappings->groupBy(function($mapping) {
            return $mapping->yearOfStudy->name . '.' . $mapping->semester->name;
        })->each(function($levelMappings, $level) use ($tabs) {
            list($year, $semester) = explode('.', $level);
            $tabs->push([
                'id' => str_replace('.', '-', $level),
                'label' => "Level $level",
                'year' => $year,
                'semester' => $semester,
                'sortKey' => (int)$year * 10 + (int)$semester,
                'mappings' => $levelMappings
            ]);
        });
        
        // Sort tabs by level
        $tabs = $tabs->sortBy('sortKey')->values();
        
        return view('admin.academic-sessions.map-course-units', [
            'academicSession' => $academicSession,
            'programme' => $programme,
            'courseUnits' => $courseUnits,
            'yearsOfStudy' => $yearsOfStudy,
            'semesters' => $semesters,
            'specialisations' => $specialisations,
            'selectedSpecialisationId' => $selectedSpecialisationId,
            'tabs' => $tabs,
            'mappings' => $mappings
        ]);
    }

    /**
     * Show the bulk upload form.
     */
    public function showBulkUploadForm(AcademicSession $academicSession)
    {
        return view('admin.academic-sessions.bulk-upload', compact('academicSession'));
    }
    
    /**
     * Process bulk upload of course unit mappings.
     */
    public function processBulkUpload(Request $request, AcademicSession $academicSession)
    {
        $request->validate([
            'mapping_file' => 'required|file|mimes:csv,txt|max:1024'
        ]);

        try {
            $file = $request->file('mapping_file');
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0);
            
            $requiredHeaders = ['programme_code', 'course_unit_code', 'year_of_study', 'semester'];
            $headers = array_map('strtolower', $csv->getHeader());
            
            // Validate CSV headers
            foreach ($requiredHeaders as $header) {
                if (!in_array($header, $headers)) {
                    return back()->with('error', "Invalid CSV format. Missing required column: {$header}");
                }
            }
            
            $records = (new Statement())->process($csv);
            $results = [
                'processed' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => [],
                'errors' => []
            ];
            
            DB::beginTransaction();
            
            foreach ($records as $record) {
                $results['processed']++;
                
                try {
                    $programme = Programme::where('programme_code', $record['programme_code'])->first();
                    if (!$programme) {
                        $results['skipped'][] = "Programme not found: {$record['programme_code']}";
                        continue;
                    }
                    
                    $courseUnit = CourseUnit::where('code', $record['course_unit_code'])->first();
                    if (!$courseUnit) {
                        $results['skipped'][] = "Course unit not found: {$record['course_unit_code']}";
                        continue;
                    }
                    
                    $yearOfStudy = YearOfStudy::where('name', $record['year_of_study'])->first();
                    if (!$yearOfStudy) {
                        $results['skipped'][] = "Invalid year of study: {$record['year_of_study']}";
                        continue;
                    }
                    
                    $semester = Semester::where('name', $record['semester'])->first();
                    if (!$semester) {
                        $results['skipped'][] = "Invalid semester: {$record['semester']}";
                        continue;
                    }
                    
                    // Check if mapping already exists for this academic session
                    $existingMapping = DB::table('course_unit_programme_mappings')
                        ->where('academic_session_id', $academicSession->id)
                        ->where('programme_id', $programme->id)
                        ->where('course_unit_id', $courseUnit->id)
                        ->first();

                    if ($existingMapping) {
                        // Update existing mapping
                        DB::table('course_unit_programme_mappings')
                            ->where('id', $existingMapping->id)
                            ->update([
                                'year_of_study_id' => $yearOfStudy->id,
                                'semester_id' => $semester->id,
                                'updated_at' => now()
                            ]);
                        $results['updated']++;
                    } else {
                        // Create new mapping
                        DB::table('course_unit_programme_mappings')->insert([
                            'academic_session_id' => $academicSession->id,
                            'programme_id' => $programme->id,
                            'course_unit_id' => $courseUnit->id,
                            'year_of_study_id' => $yearOfStudy->id,
                            'semester_id' => $semester->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $results['created']++;
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = "Error processing row {$results['processed']}: " . $e->getMessage();
                    continue;
                }
            }
            
            DB::commit();
            
            // Generate report filename
            $timestamp = now()->format('Ymd_His');
            $reportFilename = "bulk_upload_report_{$timestamp}.csv";
            $reportPath = storage_path("app/reports/{$reportFilename}");
            
            // Ensure reports directory exists
            if (!file_exists(storage_path('app/reports'))) {
                mkdir(storage_path('app/reports'), 0755, true);
            }
            
            // Generate report content
            $reportContent = [];
            
            // Add summary
            $reportContent[] = ['Bulk Upload Report', ''];
            $reportContent[] = ['Date', now()->toDateTimeString()];
            $reportContent[] = ['Academic Session', $academicSession->name];
            $reportContent[] = [''];
            $reportContent[] = ['Summary', ''];
            $reportContent[] = ['Total Processed', $results['processed']];
            $reportContent[] = ['Created', $results['created']];
            $reportContent[] = ['Updated', $results['updated']];
            $reportContent[] = ['Skipped', count($results['skipped'])];
            $reportContent[] = ['Errors', count($results['errors'])];
            $reportContent[] = [''];
            
            // Add skipped items
            if (!empty($results['skipped'])) {
                $reportContent[] = ['Skipped Items', ''];
                $reportContent[] = ['Programme Code', 'Course Unit Code', 'Year', 'Semester', 'Reason'];
                foreach ($results['skipped'] as $item) {
                    $parts = explode(':', $item, 2);
                    $reason = trim($parts[1] ?? 'Unknown reason');
                    $code = trim($parts[0] ?? '');
                    $reportContent[] = [
                        $record['programme_code'] ?? $code,
                        $record['course_unit_code'] ?? '',
                        $record['year_of_study'] ?? '',
                        $record['semester'] ?? '',
                        $reason
                    ];
                }
                $reportContent[] = [''];
            }
            
            // Add errors
            if (!empty($results['errors'])) {
                $reportContent[] = ['Errors', ''];
                $reportContent[] = ['Row', 'Error'];
                foreach ($results['errors'] as $error) {
                    $parts = explode(':', $error, 2);
                    $reportContent[] = [
                        trim(str_replace('Error processing row', '', $parts[0] ?? '')),
                        trim($parts[1] ?? $error)
                    ];
                }
            }
            
            // Write report to file
            $file = fopen($reportPath, 'w');
            foreach ($reportContent as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
            
            // Store report filename in session
            session()->flash('report_filename', $reportFilename);
            
            $message = "Bulk upload completed. Processed: {$results['processed']}, Created: {$results['created']}, Updated: {$results['updated']}";
            
            if (!empty($results['skipped'])) {
                $skippedCount = count($results['skipped']);
                $message .= ", Skipped: {$skippedCount}";
                session()->flash('skipped_items', array_slice($results['skipped'], 0, 50));
            }
            
            if (!empty($results['errors'])) {
                $errorCount = count($results['errors']);
                $message .= ", Errors: {$errorCount}";
                session()->flash('error_items', array_slice($results['errors'], 0, 50));
            }
            
            return redirect()
                ->route('admin.academic-sessions.bulk-upload', $academicSession)
                ->with([
                    'success' => $message,
                    'show_report' => true,
                    'skipped_items' => array_slice($results['skipped'] ?? [], 0, 50),
                    'error_items' => array_slice($results['errors'] ?? [], 0, 50),
                    'report_filename' => $reportFilename
                ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing CSV file: ' . $e->getMessage());
        }
    }

    /**
     * Download the bulk upload report.
     */
    public function downloadReport(AcademicSession $academicSession, $filename)
    {
        $path = storage_path("app/reports/{$filename}");
        
        if (!file_exists($path)) {
            return back()->with('error', 'Report file not found.');
        }
        
        return response()->download($path, "bulk_upload_report_{$academicSession->name}.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Add a course unit to a programme in an academic session.
     */
    public function addCourseUnit(Request $request, AcademicSession $academicSession, Programme $programme)
    {
        $validated = $request->validate([
            'course_unit_id' => 'required|exists:course_units,id',
            'year_of_study_id' => 'required|exists:years_of_study,id',
            'semester_id' => 'required|exists:semesters,id',
            'is_core' => 'required|boolean',
            'specialisation_id' => 'nullable|exists:specialisations,id'
        ]);

        try {
            // Validation logic
            if (!$validated['is_core'] && !$validated['specialisation_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non-core courses must be assigned to a specialisation'
                ], 422);
            }
            
            if ($validated['is_core'] && $validated['specialisation_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Core courses cannot be assigned to a specific specialisation'
                ], 422);
            }

            // Check if this course unit is already mapped to this programme in this session
            $existingMapping = $programme->sessionMappings($academicSession->id)
                ->where('course_unit_id', $request->course_unit_id)
                ->first();

            if ($existingMapping) {
                // Update existing mapping
                $existingMapping->update([
                    'year_of_study_id' => $validated['year_of_study_id'],
                    'semester_id' => $validated['semester_id'],
                    'is_core' => $validated['is_core'],
                    'specialisation_id' => $validated['specialisation_id'],
                    'updated_at' => now()
                ]);
                $message = 'Course unit mapping updated successfully';
            } else {
                // Create new mapping
                $mapping = $programme->courseUnitMappings()->create([
                    'academic_session_id' => $academicSession->id,
                    'course_unit_id' => $validated['course_unit_id'],
                    'year_of_study_id' => $validated['year_of_study_id'],
                    'semester_id' => $validated['semester_id'],
                    'is_core' => $validated['is_core'],
                    'specialisation_id' => $validated['specialisation_id']
                ]);
                $message = 'Course unit added successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'mapping' => $existingMapping ? $existingMapping->load(['courseUnit', 'specialisation']) : $mapping->load(['courseUnit', 'specialisation'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add course unit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a course unit from a programme in an academic session.
     */
    public function removeCourseUnit(AcademicSession $academicSession, Programme $programme, $courseUnitId)
    {
        try {
            $deleted = $programme->sessionMappings($academicSession->id)
                ->where('course_unit_id', $courseUnitId)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course unit removed successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Course unit mapping not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove course unit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a programme from an academic session.
     */
    public function detach(AcademicSession $academicSession, Programme $programme)
    {
        DB::transaction(function () use ($academicSession, $programme) {
            // Remove all course unit mappings for this programme in the academic session
            $programme->sessionMappings($academicSession->id)->delete();
            
            // Detach the programme from the academic session
            $academicSession->programmes()->detach($programme->id);
        });

        return redirect()
            ->route('admin.academic-sessions.show', $academicSession)
            ->with('success', 'Programme removed from academic session successfully');
    }
}
