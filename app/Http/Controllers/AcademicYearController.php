<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::all();

        return view('academic_years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('academic_years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|unique:academic_years,year',
        ]);

        AcademicYear::create(['year' => $request->year]);

        return redirect()->route('academic_years.index')->with('success', 'Academic Year added successfully.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academic_years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'year' => 'required|unique:academic_years,year,'.$academicYear->id,
        ]);

        $academicYear->update(['year' => $request->year]);

        return redirect()->route('academic_years.index')->with('success', 'Academic Year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('academic_years.index')->with('success', 'Academic Year deleted successfully.');
    }
}
