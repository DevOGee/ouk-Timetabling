@extends('layouts.app')

@section('title', 'Add Course Unit')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Add Course Unit</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('course_units.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="year_of_study_id" class="form-label">Year of Study</label>
                <select class="form-control" id="year_of_study_id" name="year_of_study_id" required>
                    <option value="">Select Year of Study</option>
                    @foreach ($yearsOfStudy as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="semester_id" class="form-label">Semester</label>
                <select class="form-control" id="semester_id" name="semester_id" required>
                    <option value="">Select Semester</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Course Unit Code</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Course Unit Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="color">Course Color</label>
                <input type="color" id="color" name="color" class="form-control"
                    value="{{ old('color', $courseUnit->color ?? '#ff7f50') }}">
            </div>


            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('course_units.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
