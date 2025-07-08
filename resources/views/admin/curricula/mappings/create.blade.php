@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Add Course Unit to {{ $curriculum->name }}</h2>
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
                    <li class="breadcrumb-item active" aria-current="page">Add</li>
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
            <form action="{{ route('admin.academic-sessions.curricula.mappings.store', [$academicSession, $curriculum]) }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="programme_id" class="form-label">Programme <span class="text-danger">*</span></label>
                        <select class="form-select @error('programme_id') is-invalid @enderror" 
                                id="programme_id" 
                                name="programme_id" 
                                required>
                            <option value="">Select Programme</option>
                            @foreach($programmes as $programme)
                                <option value="{{ $programme->id }}" 
                                    {{ old('programme_id') == $programme->id ? 'selected' : '' }}>
                                    {{ $programme->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('programme_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="course_unit_id" class="form-label">Course Unit <span class="text-danger">*</span></label>
                        <select class="form-select @error('course_unit_id') is-invalid @enderror" 
                                id="course_unit_id" 
                                name="course_unit_id" 
                                required
                                {{ count($courseUnits) === 0 ? 'disabled' : '' }}>
                            <option value="">Select Course Unit</option>
                            @if(count($courseUnits) > 0)
                                @foreach($courseUnits as $id => $name)
                                    <option value="{{ $id }}" 
                                        {{ old('course_unit_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">No course units available</option>
                            @endif
                        </select>
                        @error('course_unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                                    {{ old('year_of_study_id') == $year->id ? 'selected' : '' }}>
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
                                    {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="lecturer_id" class="form-label">Lecturer</label>
                        <select class="form-select @error('lecturer_id') is-invalid @enderror" 
                                id="lecturer_id" 
                                name="lecturer_id">
                            <option value="">Select Lecturer (Optional)</option>
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" 
                                    {{ old('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('lecturer_id')
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
                                   {{ old('is_elective') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_elective">
                                This is an elective course
                            </label>
                            @error('is_elective')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6" id="maxStudentsContainer" style="display: none;">
                        <label for="max_students" class="form-label">Maximum Students (Optional)</label>
                        <input type="number" 
                               class="form-control @error('max_students') is-invalid @enderror" 
                               id="max_students" 
                               name="max_students" 
                               min="1"
                               value="{{ old('max_students') }}">
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
                              rows="2">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Add Course Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const programmeSelect = document.getElementById('programme_id');
    const courseUnitSelect = document.getElementById('course_unit_id');
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
    
    // Load course units when a programme is selected
    programmeSelect.addEventListener('change', function() {
        const programmeId = this.value;
        
        if (!programmeId) {
            courseUnitSelect.innerHTML = '<option value="">Select Course Unit</option>';
            courseUnitSelect.disabled = true;
            return;
        }
        
        // Show loading state
        courseUnitSelect.innerHTML = '<option value="">Loading course units...</option>';
        courseUnitSelect.disabled = true;
        
        // Fetch course units for the selected programme
        fetch(`/admin/academic-sessions/{{ $academicSession->id }}/curricula/{{ $curriculum->id }}/programmes/${programmeId}/course-units`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load course units');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.course_units.length > 0) {
                        let options = '<option value="">Select Course Unit</option>';
                        data.course_units.forEach(course => {
                            options += `<option value="${course.id}">${course.text}</option>`;
                        });
                        courseUnitSelect.innerHTML = options;
                        courseUnitSelect.disabled = false;
                        
                        // Restore previously selected value if any
                        const oldValue = '{{ old('course_unit_id') }}';
                        if (oldValue) {
                            courseUnitSelect.value = oldValue;
                        }
                    } else {
                        courseUnitSelect.innerHTML = '<option value="">No course units available for this programme</option>';
                        courseUnitSelect.disabled = true;
                    }
                } else {
                    throw new Error(data.message || 'Failed to load course units');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                courseUnitSelect.innerHTML = '<option value="">Error loading course units</option>';
                courseUnitSelect.disabled = true;
            });
    });
    
    // Trigger change event if there's a previously selected programme (after validation errors)
    if (programmeSelect.value) {
        programmeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
