@extends('layouts.app')

@section('title', 'Add Programme')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Add Programme</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.programmes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-control" id="department_id" name="department_id" required>
                    <option value="">Select Department</option>
                    @foreach ($departments->groupBy('school.name') as $schoolName => $schoolDepartments)
                        <optgroup label="{{ $schoolName }}">
                            @foreach ($schoolDepartments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="programme_code">Programme Code</label>
                <input type="text" name="programme_code" class="form-control" required>
            </div>


            <div class="mb-3">
                <label for="name" class="form-label">Programme Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="has_specialisations" name="has_specialisations" value="1">
                <label class="form-check-label" for="has_specialisations">This programme has specialisations</label>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('admin.programmes.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
