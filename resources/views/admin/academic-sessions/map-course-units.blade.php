@extends('layouts.admin')

@section('title', 'Map Course Units - ' . $programme->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Map Course Units</h1>
                    <p class="mb-0 text-muted">{{ $programme->name }} ({{ $programme->code }}) - {{ $academicSession->name }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.academic-sessions.curriculum', $academicSession) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Curriculum
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Mapped Course Units --}}
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Mapped Course Units</h6>
                    <div class="d-flex">
                        <form action="{{ route('admin.academic-sessions.programmes.map-course-units', [$academicSession, $programme]) }}" method="GET" class="d-flex align-items-center me-3">
                            <select name="specialisation_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" {{ $selectedSpecialisationId == 'all' ? 'selected' : '' }}>All Specialisations</option>
                                <option value="core_only" {{ $selectedSpecialisationId == 'core_only' ? 'selected' : '' }}>Core Only</option>
                                @foreach($specialisations as $spec)
                                    <option value="{{ $spec->id }}" {{ $selectedSpecialisationId == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                                @endforeach
                            </select>
                        </form>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCourseUnitsModal">
                            <i class="bi bi-plus-circle"></i> Add Course Units
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="levelTabs" role="tablist">
                        @foreach($tabs as $index => $tab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                        id="tab-{{ $tab['id'] }}" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#content-{{ $tab['id'] }}" 
                                        type="button" 
                                        role="tab">
                                    {{ $tab['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="tab-content" id="levelTabsContent">
                        @forelse($tabs as $index => $tab)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                 id="content-{{ $tab['id'] }}" 
                                 role="tabpanel">
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%">Code</th>
                                                <th style="width: 35%">Title</th>
                                                <th style="width: 10%">Credit</th>
                                                <th style="width: 15%">Type</th>
                                                <th style="width: 15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tab['mappings'] as $mapping)
                                                <tr data-course-id="{{ $mapping->course_unit_id }}">
                                                    <td>{{ $mapping->courseUnit->code }}</td>
                                                    <td>{{ $mapping->courseUnit->name }}</td>
                                                    <td>{{ $mapping->courseUnit->credit_hours ?? '-' }}</td>
                                                    <td>
                                                        @if($mapping->is_core)
                                                            <span class="badge bg-success">Core</span>
                                                        @elseif($mapping->specialisation)
                                                            <span class="badge bg-info text-dark">{{ $mapping->specialisation->name }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">Elective</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm remove-mapping" title="Remove">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">No course units mapped yet.</p>
                                <p class="small text-muted">Click "Add Course Units" to get started.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Course Units Modal --}}
<div class="modal fade" id="addCourseUnitsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.academic-sessions.programmes.course-units.add', [$academicSession, $programme]) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title mb-0">Add Course Units</h5>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Selected
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="year_select" class="form-label">Year of Study</label>
                            <select id="year_select" name="year_of_study_id" class="form-select" required>
                                <option value="">Select Year</option>
                                @foreach($yearsOfStudy as $year)
                                    <option value="{{ $year->id }}" {{ old('year_of_study_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="semester_select" class="form-label">Semester</label>
                            <select id="semester_select" name="semester_id" class="form-select" required>
                                <option value="">Select Semester</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Course Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_core" id="is_core_yes" value="1" {{ old('is_core', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_core_yes">Core (All Specialisations)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_core" id="is_core_no" value="0" {{ old('is_core') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_core_no">Specific Specialisation</label>
                        </div>
                    </div>

                    @if($specialisations->isNotEmpty())
                    <div class="mb-3" id="specialisation_select_container" style="{{ old('is_core', '1') == '0' ? 'display: block;' : 'display: none;' }}">
                        <label for="specialisation_select" class="form-label">Specialisation</label>
                        <select id="specialisation_select" name="specialisation_id" class="form-select">
                            <option value="">Select Specialisation</option>
                            @foreach($specialisations as $specialisation)
                                <option value="{{ $specialisation->id }}" {{ old('specialisation_id') == $specialisation->id ? 'selected' : '' }}>{{ $specialisation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="courseSearch" class="form-control" placeholder="Search course units...">
                        </div>
                    </div>

                    <div class="course-units-container">
                        @foreach($courseUnits as $courseUnit)
                            <div class="course-unit-item" data-id="{{ $courseUnit->id }}">
                                <input type="checkbox" name="course_unit_ids[]" value="{{ $courseUnit->id }}" class="form-check-input" id="course_{{ $courseUnit->id }}" {{ in_array($courseUnit->id, old('course_unit_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="course_{{ $courseUnit->id }}">
                                    <strong>{{ $courseUnit->code }}</strong> - {{ $courseUnit->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const removeCourseUnitUrl = (courseUnitId) => 
        `{{ route('admin.academic-sessions.programmes.course-units.remove', [$academicSession, $programme, '']) }}/${courseUnitId}`;
    
    const courseSearch = document.getElementById('courseSearch');
    
    // Show/hide specialisation dropdown
    document.querySelectorAll('input[name="is_core"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.getElementById('specialisation_select_container');
            if (container) {
                container.style.display = this.value === '0' ? 'block' : 'none';
            }
        });
    });
    
    // Course unit selection
    document.querySelectorAll('.course-unit-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    this.classList.toggle('selected', checkbox.checked);
                }
            } else {
                this.classList.toggle('selected', e.target.checked);
            }
        });
    });
    
    // Search functionality
    if (courseSearch) {
        courseSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.course-unit-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }
    
    // Remove mapping
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.remove-mapping')) {
            e.preventDefault();
            const row = e.target.closest('tr');
            const courseUnitId = row?.dataset?.courseId;
            
            if (!courseUnitId || !confirm('Are you sure you want to remove this course unit?')) {
                return;
            }
            
            try {
                const response = await fetch(removeCourseUnitUrl(courseUnitId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    row.remove();
                    showToast('success', result.message);
                } else {
                    throw new Error(result.message || 'Failed to remove course unit');
                }
            } catch (error) {
                console.error('Error removing course unit:', error);
                showToast('error', error.message || 'An error occurred while removing the course unit');
            }
        }
    });
    
    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        const toastElement = document.createElement('div');
        toastElement.innerHTML = toastHtml.trim();
        toastContainer.appendChild(toastElement.firstElementChild);
        
        const toast = new bootstrap.Toast(toastElement.firstElementChild, {
            autohide: true,
            delay: 3000
        });
        
        toast.show();
        
        toastElement.firstElementChild.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }
});
</script>
@endpush
@endsection
