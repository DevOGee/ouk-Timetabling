<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\School;
use App\Models\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use League\Csv\Reader;
use Illuminate\Support\Str;
use App\Http\Requests\BulkImportUsersRequest;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with(['roles', 'school', 'title', 'department'])
            ->latest()
            ->filter(request(['search', 'status', 'role', 'department_id']))
            ->paginate(15)
            ->withQueryString();
            
        $roles = Role::all();
        $schools = School::all();
        // Eager load school for grouping key in filter
        $departments = \App\Models\Department::with('school')->orderBy('school_id')->orderBy('name')->get();
        
        return view('admin.users.index', compact('users', 'roles', 'schools', 'departments'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $titles = Title::orderBy('name')->get();
        // School list might not be needed directly if we are driving everything from Department
        // but keeping it for now just in case, though view will remove school select
        $schools = School::orderBy('name')->get();
        
        // Eager load school for grouping in the view
        $departments = \App\Models\Department::with('school')
            ->orderBy('school_id')
            ->orderBy('name')
            ->get();
            
        $roles = Role::all();
        
        return view('admin.users.create', compact('titles', 'schools', 'roles', 'departments'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'title_id' => ['required', 'exists:titles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            // Remove school_id validation or make it optional as we infer it
            'department_id' => ['required', 'exists:departments,id'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Infer school_id from department_id
        $department = \App\Models\Department::find($validated['department_id']);
        $schoolId = $department ? $department->school_id : null;

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile-images', 'public');
        }

        $user = User::create([
            'title_id' => $validated['title_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'school_id' => $schoolId,
            'department_id' => $validated['department_id'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
            'image_path' => $imagePath,
        ]);

        // Assign roles
        $user->roles()->sync($validated['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $titles = Title::orderBy('name')->get();
        $schools = School::orderBy('name')->get();
        // Eager load school for grouping in the view
        $departments = \App\Models\Department::with('school')
            ->orderBy('school_id')
            ->orderBy('name')
            ->get();

        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('admin.users.edit', compact('user', 'titles', 'schools', 'roles', 'userRoles', 'departments'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'title_id' => ['required', 'exists:titles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            // 'school_id' => ['nullable', 'exists:schools,id'], // Inferring from Department
            'department_id' => ['required', 'exists:departments,id'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Infer school_id
        $department = \App\Models\Department::find($validated['department_id']);
        $schoolId = $department ? $department->school_id : null;

        $updateData = [
            'title_id' => $validated['title_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'school_id' => $schoolId,
            'department_id' => $validated['department_id'],
            'status' => $validated['status'],
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image_path) {
                Storage::disk('public')->delete($user->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('profile-images', 'public');
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        
        // Sync roles
        $user->roles()->sync($validated['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        // Delete user's image if exists
        if ($user->image_path) {
            Storage::disk('public')->delete($user->image_path);
        }
        
        $user->delete();
        
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
    
    /**
     * Show the user import form
     */
    public function showImportForm()
    {
        $this->authorize('create', User::class);
        $roles = Role::all();
        return view('admin.users.import', compact('roles'));
    }
    
    /**
     * Process bulk user import from CSV
     */
    public function import(BulkImportUsersRequest $request)
    {
        $this->authorize('create', User::class);
        
        $file = $request->file('csv_file');
        $roleName = $request->input('role');
        $sendWelcomeEmail = $request->boolean('send_welcome_email');
        
        // Verify the role exists
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            return redirect()->back()->with('error', "The specified role '{$roleName}' does not exist.");
        }
        
        // Initialize detailed report
        $report = [
            'total_rows' => 0,
            'imported' => 0,
            'skipped' => [],
            'errors' => [],
            'successful_imports' => []
        ];
        
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
                    $error = "CSV is missing required field: {$field}";
                    \Log::error('Bulk import failed: ' . $error);
                    return redirect()->back()
                        ->with('error', $error)
                        ->withInput();
                }
            }
            
            $records = $reader->getRecords();
            $report['total_rows'] = count(iterator_to_array($records));
            $records = $reader->getRecords(); // Reset iterator
            
            // Start a database transaction
            \DB::beginTransaction();
            
            try {
                foreach ($records as $index => $record) {
                    $rowNumber = $index + 2; // +2 because of 0-based index and header row
                    $record = array_change_key_case($record, CASE_LOWER);
                    
                    // Skip if required fields are empty
                    if (empty($record['name']) || empty($record['email'])) {
                        $error = "Row {$rowNumber}: Missing required fields";
                        $report['skipped'][] = $error;
                        \Log::warning($error);
                        continue;
                    }
                    
                    // Validate email
                    if (!filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
                        $error = "Row {$rowNumber}: Invalid email format: {$record['email']}";
                        $report['skipped'][] = $error;
                        \Log::warning($error);
                        continue;
                    }
                    
                    // Check if user already exists
                    if (User::where('email', $record['email'])->exists()) {
                        $error = "Row {$rowNumber}: User with email {$record['email']} already exists";
                        $report['skipped'][] = $error;
                        \Log::warning($error);
                        continue;
                    }
                    
                    try {
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
                        
                        // Assign role using the existing relationship
                        $user->roles()->sync([$role->id]);
                        
                        // Verify the role was assigned
                        if (!$user->roles->contains('id', $role->id)) {
                            throw new \Exception("Failed to assign role '{$role->name}' to user '{$user->email}'");
                        }
                        
                        // Add to successful imports
                        $report['successful_imports'][] = [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $role->name,
                            'password' => $password // Only for display in the report
                        ];
                        
                        // Send welcome email if requested
                        if ($sendWelcomeEmail) {
                            // TODO: Uncomment and implement email sending
                            // Mail::to($user->email)->send(new WelcomeEmail($user, $password));
                        }
                        
                        $report['imported']++;
                        \Log::info("Successfully imported user: {$user->email} with role: {$role->name}");
                        
                    } catch (\Exception $e) {
                        $error = "Row {$rowNumber}: Error processing user - " . $e->getMessage();
                        $report['skipped'][] = $error;
                        $report['errors'][] = $error;
                        \Log::error($error);
                        \Log::error($e->getTraceAsString());
                        continue;
                    }
                }
                
                // Commit the transaction if we got here
                \DB::commit();
                
                // Prepare the response
                $message = "Import completed. Successfully imported {$report['imported']} out of {$report['total_rows']} users with role '{$role->name}'.";
                
                if (!empty($report['skipped'])) {
                    $message .= " " . count($report['skipped']) . " rows were skipped.";
                    session()->flash('skipped_rows', $report['skipped']);
                }
                
                // Store the detailed report in the session
                session()->flash('import_report', [
                    'total' => $report['total_rows'],
                    'imported' => $report['imported'],
                    'skipped' => count($report['skipped']),
                    'successful_imports' => $report['successful_imports'],
                    'role' => $role->name
                ]);
                
                return redirect()->route('admin.users.index')
                    ->with('success', $message);
                    
            } catch (\Exception $e) {
                // Rollback the transaction on error
                \DB::rollBack();
                throw $e; // Re-throw to be caught by the outer try-catch
            }
                
        } catch (\Exception $e) {
            $error = 'Bulk user import failed: ' . $e->getMessage();
            \Log::error($error);
            \Log::error($e->getTraceAsString());
            
            $errorMessage = 'Failed to process the file. ';
            $errorMessage .= 'Error: ' . $e->getMessage();
            
            // Add any additional error context
            if (!empty($report['errors'])) {
                $errorMessage .= '\n\nAdditional errors:\n' . implode("\n", array_slice($report['errors'], 0, 5));
                if (count($report['errors']) > 5) {
                    $errorMessage .= '\n... and ' . (count($report['errors']) - 5) . ' more errors.';
                }
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }
    
    /**
     * Show the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load(['roles', 'school', 'title', 'department', 
            'courseUnitProgrammeMappings' => function($query) {
                $query->with([
                    'courseUnit', 
                    'programme', 
                    'yearOfStudy', 
                    'semester', 
                    'day'
                ]);
            }
        ]);
        
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);
        
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);
        
        return back()->with('success', 'User status updated successfully');
    }
}
