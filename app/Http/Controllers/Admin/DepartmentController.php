<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('school')
            ->orderBy('school_id')
            ->orderBy('name')
            ->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $schools = \App\Models\School::orderBy('name')->get();
        return view('admin.departments.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'school_id' => 'required|exists:schools,id',
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        $schools = \App\Models\School::orderBy('name')->get();
        return view('admin.departments.edit', compact('department', 'schools'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'school_id' => 'required|exists:schools,id',
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->programmes()->exists()) {
            return back()->with('error', 'Cannot delete department with existing programmes. Please move or delete the programmes first.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully.');
    }
}
