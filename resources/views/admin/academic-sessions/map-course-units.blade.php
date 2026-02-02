@extends('layouts.app')

@push('styles')
<style>
    .course-unit-item {
        cursor: pointer;
        padding: 8px 12px;
        margin: 2px 0;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    .course-unit-item:hover {
        background-color: #f8f9fa;
    }
    .course-unit-item.selected {
        background-color: #e9ecef;
        font-weight: 500;
    }
    .course-unit-item input[type="checkbox"] {
        margin-right: 8px;
    }
    .modal-lg {
        max-width: 900px;
    }
    .course-units-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
    }
</style>
@endpush

@section('title', 'Map Course Units - ' . $programme->name . ' - ' . $academicSession->name)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-8">
            <h2>Map Course Units</h2>
            <h4>{{ $programme->name }} ({{ $programme->programme_code }}) - {{ $academicSession->name }}</h4>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.academic-sessions.curriculum', $academicSession) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Academic Session
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Specialisation Filter --}}
            @if($specialisations->isNotEmpty())
            <div class="mb-4">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Filter by Specialisation</label>
                        <select name="specialisation_id" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ !$selectedSpecialisationId || $selectedSpecialisationId === 'all' ? 'selected' : '' }}>
                                All Courses (Core + Specific)
                            </option>
                            <option value="core_only" {{ $selectedSpecialisationId === 'core_only' ? 'selected' : '' }}>
                                Core Courses Only
                            </option>
                            @foreach($specialisations as $specialisation)
                                <option value="{{ $specialisation->id }}" 
                                        {{ $selectedSpecialisationId == $specialisation->id ? 'selected' : '' }}>
                                    {{ $specialisation->name }} (Core + Specific)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            @endif

            {{-- Level Tabs --}}
            @if($tabs->isNotEmpty())
                <ul class="nav nav-tabs mb-3" role="tablist">
                    @foreach($tabs as $index => $tab)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                    id="tab-{{ $tab['id'] }}" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#content-{{ $tab['id'] }}" 
                                    type="button" 
                                    role="tab">
                                {{ $tab['label'] }}
                                <span class="badge bg-secondary ms-1">{{ $tab['mappings']->count() }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach($tabs as $index => $tab)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                             id="content-{{ $tab['id'] }}" 
                             role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 15%">Code</th>
                                            <th style="width: 50%">Course Unit</th>
                                            <th style="width: 20%">Type</th>
                                            <th class="text-center" style="width: 15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mapping-group-{{ $tab['year'] }}-{{ $tab['semester'] }}">
                                        @forelse($tab['mappings'] as $mapping)
                                            <tr data-course-id="{{ $mapping->course_unit_id }}" id="mapping-{{ $mapping->course_unit_id }}">
                                                <td class="fw-bold">{{ $mapping->courseUnit->code }}</td>
                                                <td>{{ $mapping->courseUnit->name }}</td>
                                                <td>
                                                    @if($mapping->is_core)
                                                        <span class="badge bg-primary">Core</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $mapping->specialisation->name ?? 'N/A' }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-mapping" 
                                                            data-bs-toggle="tooltip" title="Remove">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No course units mapped for this level yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No course unit mappings found. Add some using the button below.
                </div>
            @endif

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseUnitsModal">
                    <i class="bi bi-plus-circle"></i> Add Course Units
                </button>
                <a href="{{ route('admin.academic-sessions.curriculum', $academicSession) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Session
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Add Course Units Modal --}}
<div class="modal fade" id="addCourseUnitsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0">Add Course Units</h5>
                <div>
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addSelectedCourses">
                        <i class="bi bi-plus-circle"></i> Add Selected
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="year_select" class="form-label">Year of Study</label>
                        <select id="year_select" class="form-select" required>
                            <option value="">Select Year</option>
                            @foreach($yearsOfStudy as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="semester_select" class="form-label">Semester</label>
                        <select id="semester_select" class="form-select" required>
                            <option value="">Select Semester</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Course Type</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_core" id="is_core_yes" value="1" checked>
                        <label class="form-check-label" for="is_core_yes">Core (All Specialisations)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_core" id="is_core_no" value="0">
                        <label class="form-check-label" for="is_core_no">Specific Specialisation</label>
                    </div>
                </div>

                @if($specialisations->isNotEmpty())
                <div class="mb-3" id="specialisation_select_container" style="display: none;">
                    <label for="specialisation_select" class="form-label">Specialisation</label>
                    <select id="specialisation_select" class="form-select">
                        <option value="">Select Specialisation</option>
                        @foreach($specialisations as $specialisation)
                            <option value="{{ $specialisation->id }}">{{ $specialisation->name }}</option>
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
                            <input type="checkbox" class="form-check-input" id="course_{{ $courseUnit->id }}">
                            <label class="form-check-label" for="course_{{ $courseUnit->id }}">
                                <strong>{{ $courseUnit->code }}</strong> - {{ $courseUnit->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const baseUrl = '{{ route("admin.academic-sessions.programmes.map-course-units", [$academicSession, $programme]) }}';
    const addCourseUnitUrl = '{{ route("admin.academic-sessions.programmes.course-units.add", [$academicSession, $programme]) }}';
    const removeCourseUnitUrl = (courseUnitId) => 
        `{{ route('admin.academic-sessions.programmes.course-units.remove', [$academicSession, $programme, '']) }}/${courseUnitId}`;
    
    const addSelectedBtn = document.getElementById('addSelectedCourses');
    const yearSelect = document.getElementById('year_select');
    const semesterSelect = document.getElementById('semester_select');
    const courseSearch = document.getElementById('courseSearch');
    const modalElement = document.getElementById('addCourseUnitsModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
    
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
    
    // Add Selected button
    if (addSelectedBtn) {
        addSelectedBtn.addEventListener('click', async function() {
            const yearId = yearSelect?.value;
            const semesterId = semesterSelect?.value;
            const yearName = yearSelect?.selectedOptions[0]?.text;
            const semesterName = semesterSelect?.selectedOptions[0]?.text;
            
            if (!yearId || !semesterId) {
                showToast('error', 'Please select both year and semester');
                return;
            }
            
            const selectedCourses = Array.from(document.querySelectorAll('.course-unit-item input[type="checkbox"]:checked'));
            
            if (selectedCourses.length === 0) {
                showToast('error', 'Please select at least one course unit');
                return;
            }
            
            const originalButtonText = addSelectedBtn.innerHTML;
            addSelectedBtn.disabled = true;
            addSelectedBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
            
            try {
                for (const checkbox of selectedCourses) {
                    const courseItem = checkbox.closest('.course-unit-item');
                    const courseId = courseItem.dataset.id;
                    const courseLabel = courseItem.querySelector('label');
                    const courseCode = courseLabel.querySelector('strong').textContent;
                    const courseName = courseLabel.textContent.replace(courseCode, '').replace(' - ', '').trim();
                    
                    try {
                        const response = await fetch(addCourseUnitUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                course_unit_id: courseId,
                                year_of_study_id: yearId,
                                semester_id: semesterId,
                                is_core: document.querySelector("input[name=\"is_core\"]:checked").value === "1",
                                specialisation_id: document.getElementById("specialisation_select")?.value || null
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showToast('success', `${courseCode} added successfully`);
                            // Reload page to show new mapping
                            setTimeout(() => location.reload(), 500);
                        } else {
                            throw new Error(result.message || 'Failed to add course unit');
                        }
                    } catch (error) {
                        console.error(`Error adding course ${courseCode}:`, error);
                        showToast('error', `Failed to add ${courseCode}: ${error.message}`);
                    }
                    
                    checkbox.checked = false;
                    courseItem.classList.remove('selected');
                }
                
                if (yearSelect) yearSelect.value = '';
                if (semesterSelect) semesterSelect.value = '';
                if (courseSearch) courseSearch.value = '';
                
                if (modal) modal.hide();
                
            } catch (error) {
                console.error('Error in add selected:', error);
                showToast('error', 'An error occurred while adding course units');
            } finally {
                addSelectedBtn.disabled = false;
                addSelectedBtn.innerHTML = originalButtonText;
            }
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

<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11"></div>

@endsection
