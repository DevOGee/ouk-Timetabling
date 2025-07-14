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
        
        $users = User::with(['roles', 'school', 'title'])
            ->latest()
            ->filter(request(['search', 'status', 'role']))
            ->paginate(15)
            ->withQueryString();
            
        $roles = Role::all();
        $schools = School::all();
        
        return view('admin.users.index', compact('users', 'roles', 'schools'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $titles = Title::orderBy('name')->get();
        $schools = School::orderBy('name')->get();
        $roles = Role::all();
        
        return view('admin.users.create', compact('titles', 'schools', 'roles'));
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
            'school_id' => ['nullable', 'exists:schools,id'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

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
            'school_id' => $validated['school_id'],
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
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('admin.users.edit', compact('user', 'titles', 'schools', 'roles', 'userRoles'));
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
            'school_id' => ['nullable', 'exists:schools,id'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $updateData = [
            'title_id' => $validated['title_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'school_id' => $validated['school_id'],
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
            \Log::error('Bulk user import failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to process the file. Please check the format and try again.');
        }
    }
    
    /**
     * Show the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load(['roles', 'school', 'title']);
        
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
