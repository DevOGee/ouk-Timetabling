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
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-8">
            <h2>Map Course Units</h2>
            <h4>{{ $programme->name }} ({{ $programme->programme_code }}) - {{ $academicSession->name }}</h4>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Academic Session
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="mapping-form" action="{{ route('admin.academic-sessions.programmes.course-units.store', ['academicSession' => $academicSession, 'programme' => $programme]) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="mappings-table">
                        <thead class="table-light">
                            <tr>
                                <th>Course Unit</th>
                                <th>Code</th>
                                <th>Year</th>
                                <th>Semester</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mappings-container">
                            @foreach($mappings as $index => $mapping)
                                @include('admin.academic-sessions.partials.course-unit-mapping-row', [
                                    'index' => $index,
                                    'mapping' => $mapping
                                ])
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseUnitsModal">
                        <i class="bi bi-plus-circle"></i> Add Course Units
                    </button>
                    <div>
                        <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Session
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Save All Mappings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Course Units Modal -->
<div class="modal fade" id="addCourseUnitsModal" tabindex="-1" aria-labelledby="addCourseUnitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseUnitsModalLabel">Add Course Units</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="courseSearch" class="form-control" placeholder="Search course units...">
                    </div>
                </div>

                <div class="course-units-container mb-3">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="addSelectedCourses">
                    <i class="bi bi-plus-circle"></i> Add Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template for new row -->
<template id="mapping-row-template">
    <tr data-course-id="">
        <td class="course-unit-name"></td>
        <td class="course-unit-code"></td>
        <td class="course-year"></td>
        <td class="course-semester"></td>
        <td>
            <input type="hidden" name="mappings[INDEX][course_unit_id]" class="course-unit-id">
            <input type="hidden" name="mappings[INDEX][year_of_study_id]" class="year-id">
            <input type="hidden" name="mappings[INDEX][semester_id]" class="semester-id">
            <button type="button" class="btn btn-sm btn-outline-danger remove-mapping">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
// Debug: Check if script is loaded
console.log('=== COURSE MAPPING SCRIPT LOADED ===');

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Debug: Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded!');
    } else {
        console.log('Bootstrap is available');
    }
    
    // Get all necessary elements
    const addSelectedBtn = document.getElementById('addSelectedCourses');
    const yearSelect = document.getElementById('year_select');
    const semesterSelect = document.getElementById('semester_select');
    const mappingsContainer = document.getElementById('mappings-container');
    const courseSearch = document.getElementById('courseSearch');
    const modalElement = document.getElementById('addCourseUnitsModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
    
    // Debug: Log elements
    console.log('Elements:', {
        addSelectedBtn: addSelectedBtn ? 'Found' : 'Not found',
        yearSelect: yearSelect ? 'Found' : 'Not found',
        semesterSelect: semesterSelect ? 'Found' : 'Not found',
        mappingsContainer: mappingsContainer ? 'Found' : 'Not found',
        courseSearch: courseSearch ? 'Found' : 'Not found',
        modal: modal ? 'Initialized' : 'Failed to initialize'
    });
    
    // Handle course unit selection
    document.querySelectorAll('.course-unit-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Only toggle if not clicking directly on the checkbox
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    this.classList.toggle('selected', checkbox.checked);
                }
            } else {
                // If clicking the checkbox directly
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
    
    // Handle Add Selected button click
    if (addSelectedBtn) {
        addSelectedBtn.addEventListener('click', function(e) {
            console.log('Add Selected button clicked');
            
            const yearId = yearSelect?.value;
            const semesterId = semesterSelect?.value;
            
            console.log('Selected values:', { yearId, semesterId });
            
            if (!yearId || !semesterId) {
                alert('Please select both year and semester');
                return;
            }
            
            const selectedCourses = document.querySelectorAll('.course-unit-item input[type="checkbox"]:checked');
            console.log('Selected courses count:', selectedCourses.length);
            
            if (selectedCourses.length === 0) {
                alert('Please select at least one course unit');
                return;
            }
            
            // Get current row count for unique indices
            const currentRowCount = document.querySelectorAll('#mappings-container tr').length;
            
            // Process each selected course
            selectedCourses.forEach((checkbox, index) => {
                const item = checkbox.closest('.course-unit-item');
                const courseId = item?.dataset?.id;
                const label = item?.querySelector('label')?.textContent?.trim() || '';
                const [courseCode, ...nameParts] = label.split(' - ');
                const courseName = nameParts.join(' - ').trim();
                const yearName = yearSelect.options[yearSelect.selectedIndex]?.text || '';
                const semesterName = semesterSelect.options[semesterSelect.selectedIndex]?.text || '';
                
                console.log(`Processing course ${index + 1}:`, {
                    courseId,
                    courseCode,
                    courseName,
                    yearId,
                    yearName,
                    semesterId,
                    semesterName
                });
                
                // Check if this course already exists with the same year and semester
                const exists = Array.from(document.querySelectorAll('tr[data-course-id]')).some(row => {
                    return row.dataset.courseId === courseId &&
                           row.querySelector('.year-id')?.value === yearId &&
                           row.querySelector('.semester-id')?.value === semesterId;
                });
                
                if (!exists) {
                    // Create a new row
                    const newRow = document.createElement('tr');
                    newRow.dataset.courseId = courseId;
                    
                    // Get the index for this new row
                    const rowIndex = currentRowCount + index;
                    
                    // Create the row HTML
                    newRow.innerHTML = `
                        <td class="course-unit-name">${courseName}</td>
                        <td class="course-unit-code">${courseCode || ''}</td>
                        <td class="course-year">${yearName}</td>
                        <td class="course-semester">${semesterName}</td>
                        <td>
                            <input type="hidden" name="mappings[${rowIndex}][course_unit_id]" class="course-unit-id" value="${courseId}">
                            <input type="hidden" name="mappings[${rowIndex}][year_of_study_id]" class="year-id" value="${yearId}">
                            <input type="hidden" name="mappings[${rowIndex}][semester_id]" class="semester-id" value="${semesterId}">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-mapping">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    
                    // Add the new row to the container
                    if (mappingsContainer) {
                        mappingsContainer.appendChild(newRow);
                        console.log('Added new row for course:', courseId);
                    }
                } else {
                    console.log('Course already exists, skipping:', courseId);
                }
                
                // Reset the checkbox
                checkbox.checked = false;
                item.classList.remove('selected');
            });
            
            // Reset the form
            if (yearSelect) yearSelect.value = '';
            if (semesterSelect) semesterSelect.value = '';
            if (courseSearch) courseSearch.value = '';
            
            // Hide the modal
            if (modal) {
                modal.hide();
                console.log('Modal hidden');
            }
        });
    }
    
    // Handle remove mapping button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-mapping')) {
            e.preventDefault();
            const row = e.target.closest('tr');
            if (row) {
                console.log('Removing row:', row);
                row.remove();
            }
        }
    });
});
</script>
@endpush

@endsection
