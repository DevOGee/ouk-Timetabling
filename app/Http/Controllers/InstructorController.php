<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\Title;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class InstructorController extends Controller
{
    /**
     * Display a listing of the instructors.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query users with instructor role and filter based on search
        $query = User::role('instructor')->with('title');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $instructors = $query->paginate(10)->appends(['search' => $search]);

        return view('instructors.index', compact('instructors', 'search'));
    }

    /**
     * Show the form for creating a new instructor.
     */
    public function create()
    {
        $titles = Title::orderBy('sort_order')->get();
        return view('instructors.create', compact('titles'));
    }

    /**
     * Store a newly created instructor in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_id' => 'required|exists:titles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'school_id' => 'nullable|exists:schools,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title_id', 'name', 'email', 'phone', 'school_id']);
        $data['password'] = Hash::make('password'); // Default password, should be changed on first login
        $data['status'] = 'active';

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('instructors', 'public');
            $data['image_path'] = $path;
        }

        $instructor = User::create($data);
        $instructor->assignRole('instructor');

        return redirect()->route('instructors.index')
            ->with('success', 'Instructor created successfully.');
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
     */
    public function show(string $id)
    {
        $instructor = User::role('instructor')->with('title', 'school')->findOrFail($id);
        return view('instructors.show', compact('instructor'));
    }

    /**
     * Show the form for editing the specified instructor.
     */
    public function edit(string $id)
    {
        $instructor = User::role('instructor')->findOrFail($id);
        $titles = Title::orderBy('sort_order')->get();
        $schools = \App\Models\School::orderBy('name')->get();
        
        return view('instructors.edit', compact('instructor', 'titles', 'schools'));
    }

    /**
     * Update the specified instructor in storage.
     */
    public function update(Request $request, string $id)
    {
        $instructor = User::role('instructor')->findOrFail($id);
        
        $request->validate([
            'title_id' => 'required|exists:titles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $instructor->id,
            'phone' => 'nullable|string|max:20',
            'school_id' => 'nullable|exists:schools,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title_id', 'name', 'email', 'phone', 'school_id', 'status']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($instructor->image_path) {
                Storage::disk('public')->delete($instructor->image_path);
            }
            $path = $request->file('image')->store('instructors', 'public');
            $data['image_path'] = $path;
        }

        $instructor->update($data);

        return redirect()->route('instructors.index')
            ->with('success', 'Instructor updated successfully.');
    }

    /**
     * Remove the specified instructor from storage.
     */
    public function destroy(string $id)
    {
        $instructor = User::role('instructor')->findOrFail($id);
        
        // Don't delete the user, just remove the instructor role
        $instructor->removeRole('instructor');
        
        // Optionally, you might want to handle the user's other data here
        
        return redirect()->route('instructors.index')
            ->with('success', 'Instructor removed successfully.');
    }
}
