<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use League\Csv\Reader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\BulkImportUsersRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['roles', 'school'])
            ->latest()
            ->paginate(20);
            
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Process bulk user upload from CSV
     */
    public function import(BulkImportUsersRequest $request)
    {

        $file = $request->file('csv_file');
        $role = $request->input('role');
        $sendWelcomeEmail = $request->boolean('send_welcome_email');
        
        try {
            // Ensure the file is valid
            if (!$file->isValid()) {
                throw new \Exception('The uploaded file is not valid.');
            }
            // Read the CSV file
            $reader = Reader::createFromPath($file->getPathname(), 'r');
            $reader->setHeaderOffset(0);
            
            $header = array_map('strtolower', $reader->getHeader());
            $requiredFields = ['name', 'email', 'title'];
            
            // Validate CSV header
            foreach ($requiredFields as $field) {
                if (!in_array(strtolower($field), $header)) {
                    return redirect()->back()->with('error', "CSV is missing required field: {$field}");
                }
            }
            
            $records = $reader->getRecords();
            $imported = 0;
            $skipped = [];
            $rowNumber = 1; // Start from 1 to account for header
            
            foreach ($records as $record) {
                $rowNumber++;
                $record = array_change_key_case($record, CASE_LOWER);
                
                // Skip if required fields are empty
                if (empty($record['name']) || empty($record['email'])) {
                    $skipped[] = "Row {$rowNumber}: Missing required fields";
                    continue;
                }
                
                // Validate email
                if (!filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
                    $skipped[] = "Row {$rowNumber}: Invalid email format: {$record['email']}";
                    continue;
                }
                
                // Check if user already exists
                if (User::where('email', $record['email'])->exists()) {
                    $skipped[] = "Row {$rowNumber}: User with email {$record['email']} already exists";
                    continue;
                }
                
                // Find or create title
                $title = Title::firstOrCreate(
                    ['name' => ucwords(strtolower(trim($record['title'])))],
                    ['abbreviation' => strtoupper(substr(trim($record['title']), 0, 3))]
                );
                
                // Generate a random password
                $password = Str::random(12);
                
                // Create the user
                $user = User::create([
                    'name' => $record['name'],
                    'email' => $record['email'],
                    'title_id' => $title->id,
                    'password' => Hash::make($password),
                    'status' => 'active',
                ]);
                
                // Assign role
                $user->assignRole($role);
                
                // Send welcome email if requested
                if ($sendWelcomeEmail) {
                    // TODO: Uncomment and implement email sending
                    // Mail::to($user->email)->send(new WelcomeEmail($user, $password));
                }
                
                $imported++;
            }
            
            $message = "Successfully imported {$imported} users.";
            if (!empty($skipped)) {
                $message .= " Skipped " . count($skipped) . " rows with issues.";
                session()->flash('skipped_rows', $skipped);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            Log::error('Bulk user import failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to process the file. Please check the format and try again.');
        }
    }

    // Other methods (create, store, show, edit, update, destroy) would go here
    // ...
}
