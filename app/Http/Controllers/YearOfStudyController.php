<?php

namespace App\Http\Controllers;

use App\Models\YearOfStudy;
use Illuminate\Http\Request;

class YearOfStudyController extends Controller
{
    public function index()
    {
        $yearsOfStudy = YearOfStudy::all();

        return view('years_of_study.index', compact('yearsOfStudy'));
    }

    public function create()
    {
        return view('years_of_study.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:years_of_study,name',
        ]);

        YearOfStudy::create($request->all());

        return redirect()->route('years_of_study.index')->with('success', 'Year of Study added successfully.');
    }

    public function edit(YearOfStudy $yearOfStudy)
    {
        return view('years_of_study.edit', compact('yearOfStudy'));
    }

    public function update(Request $request, YearOfStudy $yearOfStudy)
    {
        $request->validate([
            'name' => 'required|unique:years_of_study,name,'.$yearOfStudy->id,
        ]);

        $yearOfStudy->update($request->all());

        return redirect()->route('years_of_study.index')->with('success', 'Year of Study updated successfully.');
    }

    public function destroy(YearOfStudy $yearOfStudy)
    {
        $yearOfStudy->delete();

        return redirect()->route('years_of_study.index')->with('success', 'Year of Study deleted successfully.');
    }
}
