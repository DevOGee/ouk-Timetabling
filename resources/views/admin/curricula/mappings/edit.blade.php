@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Edit Course Unit in {{ $curriculum->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.index') }}">Academic Sessions</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.show', $academicSession) }}">{{ $academicSession->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum]) }}">{{ $curriculum->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum]) }}">Course Units</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum]) }}" 
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.academic-sessions.curricula.mappings.update', [$academicSession, $curriculum, $mapping]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="programme_id" class="form-label">Programme <span class="text-danger">*</span></label>
                        <select class="form-select @error('programme_id') is-invalid @enderror" 
                                id="programme_id" 
                                name="programme_id" 
                                required
                                {{ $mapping->exists ? 'disabled' : '' }}>
                            <option value="">Select Programme</option>
                            @foreach($programmes as $programme)
                                <option value="{{ $programme->id }}" 
                                    {{ (old('programme_id', $mapping->programme_id) == $programme->id) ? 'selected' : '' }}>
                                    {{ $programme->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('programme_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($mapping->exists)
                            <input type="hidden" name="programme_id" value="{{ $mapping->programme_id }}">
                            <div class="form-text text-muted">Programme cannot be changed after creation.</div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <label for="course_unit_id" class="form-label">Course Unit <span class="text-danger">*</span></label>
                        <select class="form-select @error('course_unit_id') is-invalid @enderror" 
                                id="course_unit_id" 
                                name="course_unit_id" 
                                required
                                {{ $mapping->exists ? 'disabled' : '' }}>
                            <option value="">Select Course Unit</option>
                            @foreach($courseUnits as $id => $name)
                                <option value="{{ $id }}" 
                                    {{ (old('course_unit_id', $mapping->course_unit_id) == $id) ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($mapping->exists)
                            <input type="hidden" name="course_unit_id" value="{{ $mapping->course_unit_id }}">
                            <div class="form-text text-muted">Course unit cannot be changed after creation. Create a new mapping instead.</div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="year_of_study_id" class="form-label">Year of Study <span class="text-danger">*</span></label>
                        <select class="form-select @error('year_of_study_id') is-invalid @enderror" 
                                id="year_of_study_id" 
                                name="year_of_study_id" 
                                required>
                            <option value="">Select Year</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" 
                                    {{ (old('year_of_study_id', $mapping->year_of_study_id) == $year->id) ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('year_of_study_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select @error('semester_id') is-invalid @enderror" 
                                id="semester_id" 
                                name="semester_id" 
                                required>
                            <option value="">Select Semester</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" 
                                    {{ (old('semester_id', $mapping->semester_id) == $semester->id) ? 'selected' : '' }}>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="user_id" class="form-label">Instructor</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" 
                                id="user_id" 
                                name="user_id">
                            <option value="">Select Instructor (Optional)</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" 
                                    {{ (old('user_id', $mapping->user_id) == $instructor->id) ? 'selected' : '' }}>
                                    {{ $instructor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input @error('is_elective') is-invalid @enderror" 
                                   type="checkbox" 
                                   id="is_elective" 
                                   name="is_elective" 
                                   value="1"
                                   {{ (old('is_elective', $mapping->is_elective) ? 'checked' : '') }}>
                            <label class="form-check-label" for="is_elective">
                                This is an elective course
                            </label>
                            @error('is_elective')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6" id="maxStudentsContainer" 
                         style="display: {{ (old('is_elective', $mapping->is_elective) ? 'block' : 'none') }};">
                        <label for="max_students" class="form-label">Maximum Students (Optional)</label>
                        <input type="number" 
                               class="form-control @error('max_students') is-invalid @enderror" 
                               id="max_students" 
                               name="max_students" 
                               min="1"
                               value="{{ old('max_students', $mapping->max_students) }}">
                        <div class="form-text">Leave empty for no limit</div>
                        @error('max_students')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="2">{{ old('notes', $mapping->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" 
                                class="btn btn-outline-danger"
                                onclick="if(confirm('Are you sure you want to delete this mapping?')) { document.getElementById('delete-form').submit(); }">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum]) }}" 
                           class="btn btn-outline-secondary me-2">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Delete form -->
            <form id="delete-form" 
                  action="{{ route('admin.academic-sessions.curricula.mappings.destroy', [$academicSession, $curriculum, $mapping]) }}" 
                  method="POST" 
                  class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isElectiveCheckbox = document.getElementById('is_elective');
    const maxStudentsContainer = document.getElementById('maxStudentsContainer');
    
    // Toggle max students field based on elective status
    function toggleMaxStudents() {
        if (isElectiveCheckbox.checked) {
            maxStudentsContainer.style.display = 'block';
        } else {
            maxStudentsContainer.style.display = 'none';
            document.getElementById('max_students').value = '';
        }
    }
    
    // Initial toggle
    toggleMaxStudents();
    
    // Add event listener for changes to the elective checkbox
    isElectiveCheckbox.addEventListener('change', toggleMaxStudents);
});
</script>
@endpush
@endsection
