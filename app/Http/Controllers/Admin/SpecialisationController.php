<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialisation;
use App\Models\Programme;
use Illuminate\Http\Request;

class SpecialisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Specialisation::with('programme.department.school');
        $programme = null;

        if ($request->has('programme_id')) {
            $programme = Programme::with('department.school')->findOrFail($request->programme_id);
            $query->where('programme_id', $request->programme_id);
        }

        $specialisations = $query->get()->groupBy('programme_id');
        
        return view('admin.specialisations.index', compact('specialisations', 'programme'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $programmes = Programme::where('has_specialisations', true)->get();
        $selectedProgrammeId = $request->programme_id;

        return view('admin.specialisations.form', compact('programmes', 'selectedProgrammeId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'programme_id' => 'required|exists:programmes,id',
        ]);

        Specialisation::create($validated);

        return redirect()->route('admin.specialisations.index', ['programme_id' => $request->programme_id])
            ->with('success', 'Specialisation created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialisation $specialisation)
    {
        $programmes = Programme::where('has_specialisations', true)->get();
        return view('admin.specialisations.form', compact('specialisation', 'programmes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specialisation $specialisation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'programme_id' => 'required|exists:programmes,id',
        ]);

        $specialisation->update($validated);

        return redirect()->route('admin.specialisations.index', ['programme_id' => $request->programme_id])
            ->with('success', 'Specialisation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialisation $specialisation)
    {
        $specialisation->delete();

        return redirect()->back()
            ->with('success', 'Specialisation deleted successfully.');
    }
}
