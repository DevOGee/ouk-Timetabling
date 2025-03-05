<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use App\Models\Lecturer;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LecturerController extends Controller
{
    /**
     * Display a listing of the instructors.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query lecturers and filter based on search
        $query = Lecturer::with('title');

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        }

        $lecturers = $query->paginate(10)->appends(['search' => $search]);

        return view('instructors.index', compact('lecturers', 'search'));
    }

    /**
     * Show the form for creating a new lecturer.
     */
    public function create()
    {
        $titles = Title::all();

        return view('instructors.create', compact('titles'));
    }

    /**
     * Store a newly created lecturer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_id' => 'required|exists:titles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lecturers,email',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('lecturers', 'public');
        }

        Lecturer::create([
            'title_id' => $request->title_id,
            'name' => $request->name,
            'email' => $request->email,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('instructors.index')->with('success', 'Lecturer added successfully');
    }

    /**
     * Display the specified lecturer.
     */
    public function show($id)
    {
        // Find the lecturer by ID
        $lecturer = Lecturer::with(['courseUnits.programmes', 'courseUnits.lessonSlots.day'])->find($id);

        // If lecturer is not found, return 404
        if (! $lecturer) {
            abort(404, 'Lecturer not found');
        }

        return view('instructors.show', compact('lecturer'));
    }

    /**
     * Show the form for editing the specified lecturer.
     */
    // public function edit(Lecturer $lecturer)
    // {
    //     $titles = Title::all();

    //     return view('instructors.edit', compact('lecturer', 'titles'));
    // }

    public function edit($id)
    {
        $lecturer = Lecturer::find($id);

        if (! $lecturer) {
            abort(404, 'Lecturer not found');
        }

        $titles = Title::all();

        return view('instructors.edit', compact('lecturer', 'titles'));
    }

    /**
     * Update the specified lecturer in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the lecturer by ID
        $lecturer = Lecturer::find($id);

        // If lecturer not found, return 404 error
        if (! $lecturer) {
            abort(404, 'Lecturer not found.');
        }

        // Validate input data
        $request->validate([
            'title_id' => 'required|exists:titles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lecturers,email,'.$id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if (! empty($lecturer->image_path) && Storage::disk('public')->exists($lecturer->image_path)) {
                Storage::disk('public')->delete($lecturer->image_path);
            }
            $lecturer->image_path = $request->file('image')->store('lecturers', 'public');
        }

        // Update lecturer details
        $lecturer->update([
            'title_id' => $request->title_id,
            'name' => $request->name,
            'email' => $request->email,
            'image_path' => $lecturer->image_path,
        ]);

        return redirect()->route('instructors.index')->with('success', 'Lecturer updated successfully');
    }

    /**
     * Remove the specified lecturer from storage.
     */
    public function destroy($id)
    {
        // Find the lecturer by ID
        $lecturer = Lecturer::find($id);

        // If lecturer not found, return 404 error
        if (! $lecturer) {
            abort(404, 'Lecturer not found.');
        }

        // Delete lecturer image if it exists
        if (! empty($lecturer->image_path)) {
            if (Storage::disk('public')->exists($lecturer->image_path)) {
                Storage::disk('public')->delete($lecturer->image_path);
            }
        }

        // Delete the lecturer from database
        $lecturer->delete();

        return redirect()->route('instructors.index')->with('success', 'Lecturer deleted successfully');
    }

    public function showUploadForm()
    {
        return view('instructors.upload');
    }

    public function importLecturers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        Excel::import(new LecturersImport, $request->file('file'));

        return redirect()->route('instructors.index')->with('success', 'Lecturers imported successfully!');
    }
}
