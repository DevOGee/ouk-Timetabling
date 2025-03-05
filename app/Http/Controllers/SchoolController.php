<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::all();

        return view('schools.index', compact('schools'));
    }

    public function show(School $school)
    {
        $school->load('programmes'); // Load related programmes

        return view('schools.show', compact('school'));
    }

    public function create()
    {
        return view('schools.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:schools,name',
        ]);

        School::create(['name' => $request->name]);

        return redirect()->route('schools.index')->with('success', 'School added successfully.');
    }

    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|unique:schools,name,'.$school->id,
        ]);

        $school->update(['name' => $request->name]);

        return redirect()->route('schools.index')->with('success', 'School updated successfully.');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('schools.index')->with('success', 'School deleted successfully.');
    }
}
