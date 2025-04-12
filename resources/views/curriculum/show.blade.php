@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Curriculum for {{ $programme->programme_code }} - {{ $programme->name }}</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('curriculum.map') }}" method="POST" class="mb-4">
            @csrf
            <input type="hidden" name="programme_id" value="{{ $programme->id }}">

            {{-- Course Unit (Full Width) --}}
            <div class="mb-3">
                <label for="course_unit_id" class="form-label">Course Units</label>
                <select class="form-select" name="course_unit_ids[]" multiple required>
                    <option value="">Select Course(s)</option>
                    @foreach ($courseUnits->sortBy('code') as $course)
                        <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Year, Semester, Submit --}}
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="year_of_study_id" class="form-label">Year of Study</label>
                    <select class="form-select" name="year_of_study_id" required>
                        <option value="">Select Year</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="semester_id" class="form-label">Semester</label>
                    <select class="form-select" name="semester_id" required>
                        <option value="">Select Semester</option>
                        @foreach ($semesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        Add Mapping
                    </button>
                </div>
            </div>
        </form>




        <h5 class="mt-4">Mapped Course Units</h5>

        @forelse($groupedMappings as $group => $mappings)
            <h4 class="text-primary">{{ $group }}</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mappings->sortBy(fn($m) => $m->courseUnit->code) as $mapping)
                        <tr>
                            <td>
                                <!-- Color Circle -->
                                <span
                                    style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; 
                        background-color: {{ $mapping->courseUnit->color ?? '#000000' }}; margin-right: 10px;">
                                </span>
                                {{ $mapping->courseUnit->code }} - {{ $mapping->courseUnit->name }}
                            </td>
                            <td>
                                <form action="{{ route('curriculum.unmap', $mapping->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @empty
            <p>No course units mapped yet.</p>
        @endforelse
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('curriculum.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Curriculum
            </a>
            <a href="{{ route('programmes.show', $programme->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-week"></i> View Scheduling
            </a>
        </div>
    </div>
@endsection
