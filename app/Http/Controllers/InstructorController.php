<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\Title;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class InstructorController extends Controller
{
    /**
     * Validation rules for storing an instructor.
     *
     * @return array
     */
    protected function getValidationRules($instructorId = null)
    {
        return [
            'title_id' => 'required|exists:titles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $instructorId,
            'phone' => 'nullable|string|max:20',
            'school_id' => 'nullable|exists:schools,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    
    /**
     * Handle image upload and return the path.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $instructor
     * @return string|null
     */
    protected function handleImageUpload(Request $request, ?User $instructor = null)
    {
        if (!$request->hasFile('image')) {
            return null;
        }
        
        // Delete old image if exists
        if ($instructor && $instructor->image_path) {
            Storage::disk('public')->delete($instructor->image_path);
        }
        
        return $request->file('image')->store('instructors', 'public');
    }
    /**
     * Display a listing of the instructors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        $search = $request->input('search');
        $status = $request->input('status', 'active'); // Default to showing only active

        $query = User::role('instructor')
            ->with(['title', 'school'])
            ->where('status', 'active') // Only active instructors by default
            ->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        // If status is explicitly set to 'inactive', show only inactive instructors
        if ($status === 'inactive') {
            $query->where('status', 'inactive');
        }

        $instructors = $query->paginate(10)
            ->appends($request->query());

        return view('instructors.index', compact('instructors'));
    }

    /**
     * Show the form for creating a new instructor.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $titles = Title::orderBy('sort_order')->get();
        $schools = School::orderBy('name')->get();
        
        return view('instructors.create', compact('titles', 'schools'));
    }

    /**
     * Store a newly created instructor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate($this->getValidationRules());
        
        try {
            // Handle image upload
            $imagePath = $this->handleImageUpload($request);
            
            $instructor = User::create([
                'title_id' => $validated['title_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'school_id' => $validated['school_id'] ?? null,
                'status' => $validated['status'],
                'password' => Hash::make('password'), // Default password, should be changed on first login
                'image_path' => $imagePath,
            ]);
            
            $instructor->assignRole('instructor');
            
            return redirect()
                ->route('instructors.index')
                ->with('success', 'Instructor created successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error creating instructor: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for importing instructors.
     */
    public function showUploadForm()
    {
        return view('instructors.import');
    }

    /**
     * Import instructors from Excel file.
     */
    public function importInstructors(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->route('instructors.index')
                ->with('success', 'Instructors imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing instructors: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified instructor.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $lecturer = User::role('instructor')
            ->with([
                'title', 
                'school', 
                'courseUnitProgrammeMappings' => function($query) {
                    $query->with([
                        'courseUnit', 
                        'programme', 
                        'yearOfStudy', 
                        'semester', 
                        'day'
                    ]);
                }
            ])
            ->findOrFail($id);
            
        $this->authorize('view', $lecturer);
            
        return view('instructors.show', compact('lecturer'));
    }

    /**
     * Show the form for editing the specified instructor.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $lecturer = User::role('instructor')->findOrFail($id);
        
        $this->authorize('update', $lecturer);
        
        $titles = Title::orderBy('sort_order')->get();
        $schools = School::orderBy('name')->get();
        
        return view('instructors.edit', compact('lecturer', 'titles', 'schools'));
    }

    /**
     * Update the specified instructor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $instructor = User::role('instructor')->findOrFail($id);
        
        $this->authorize('update', $instructor);
        
        $validated = $request->validate($this->getValidationRules($instructor->id));
        
        try {
            // Handle image upload
            $imagePath = $this->handleImageUpload($request, $instructor);
            
            $instructor->update([
                'title_id' => $validated['title_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'school_id' => $validated['school_id'] ?? null,
                'status' => $validated['status'],
                'image_path' => $imagePath ?? $instructor->image_path,
            ]);
            
            return redirect()
                ->route('instructors.index')
                ->with('success', 'Instructor updated successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating instructor: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate the specified instructor.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $instructor = User::role('instructor')->findOrFail($id);
        
        $this->authorize('delete', $instructor);
        
        try {
            // Start a database transaction
            \DB::beginTransaction();
            
            // Update the status to inactive
            $instructor->update([
                'status' => 'inactive'
            ]);
            
            // Commit the transaction
            \DB::commit();
            
            return redirect()
                ->route('instructors.index')
                ->with('success', 'Instructor has been deactivated successfully.');
                
        } catch (\Exception $e) {
            // Rollback the transaction on error
            \DB::rollBack();
            
            return back()
                ->with('error', 'Error deactivating instructor: ' . $e->getMessage());
        }
    }
}
