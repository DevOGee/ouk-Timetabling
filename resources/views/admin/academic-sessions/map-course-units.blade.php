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
                <div id="mappings-container">
                    @forelse($groupedMappings as $groupName => $mappingsGroup)
                        @php
                            list($year, $semester) = explode('.', $groupName);
                            $groupLabel = "Level {$year}.{$semester}";
                        @endphp
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ $groupLabel }}</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Code</th>
                                                <th>Course Unit</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($mappingsGroup as $index => $mapping)
                                                @include('admin.academic-sessions.partials.course-unit-mapping-row', [
                                                    'index' => $index,
                                                    'mapping' => $mapping,
                                                    'showYearSemester' => false
                                                ])
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            No course unit mappings found. Add some using the button below.
                        </div>
                    @endforelse
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
    <tr id="mapping-new-INDEX" data-course-id="">
        <td class="course-unit-code fw-bold"></td>
        <td class="course-unit-name"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-mapping" data-bs-toggle="tooltip" title="Remove">
                <i class="bi bi-trash"></i>
            </button>
            <input type="hidden" name="mappings[INDEX][course_unit_id]" class="course-unit-id">
            <input type="hidden" name="mappings[INDEX][year_of_study_id]" class="year-id">
            <input type="hidden" name="mappings[INDEX][semester_id]" class="semester-id">
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
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Get all necessary elements
    const addSelectedBtn = document.getElementById('addSelectedCourses');
    const yearSelect = document.getElementById('year_select');
    const semesterSelect = document.getElementById('semester_select');
    const courseSearch = document.getElementById('courseSearch');
    const modalElement = document.getElementById('addCourseUnitsModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
    
    // Function to find or create a group container for a year.semester
    function getOrCreateGroupContainer(year, semester) {
        const groupId = `group-${year}-${semester}`;
        let groupContainer = document.getElementById(groupId);
        
        if (!groupContainer) {
            // Create a new group container
            const groupLabel = `Level ${year}.${semester}`;
            groupContainer = document.createElement('div');
            groupContainer.id = groupId;
            groupContainer.className = 'card mb-4';
            groupContainer.innerHTML = `
                <div class="card-header bg-light">
                    <h5 class="mb-0">${groupLabel}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Unit</th>
                                    <th>Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="mapping-group" data-year="${year}" data-semester="${semester}">
                                <!-- Mappings will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            
            // Insert the new group before the "No mappings" message or at the end
            const mappingsContainer = document.getElementById('mappings-container');
            const noMappingsAlert = mappingsContainer.querySelector('.alert');
            
            if (noMappingsAlert) {
                noMappingsAlert.remove();
            }
            
            mappingsContainer.insertBefore(groupContainer, mappingsContainer.firstChild);
        }
        
        return groupContainer.querySelector('.mapping-group');
    }
    
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
            const semesterId = semesterSelect?.selectedOptions[0]?.value;
            const yearName = yearSelect?.selectedOptions[0]?.text;
            const semesterName = semesterSelect?.selectedOptions[0]?.text;
            
            if (!yearId || !semesterId) {
                alert('Please select both year and semester');
                return;
            }
            
            // Get selected course units
            const selectedCourses = Array.from(document.querySelectorAll('.course-unit-item input[type="checkbox"]:checked'));
            
            if (selectedCourses.length === 0) {
                alert('Please select at least one course unit');
                return;
            }
            
            // Get the next available index for new mappings
            const existingMappings = document.querySelectorAll('input[name^="mappings["]');
            let maxIndex = -1;
            existingMappings.forEach(input => {
                const match = input.name.match(/mappings\[(\d+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    if (index > maxIndex) maxIndex = index;
                }
            });
            
            // Get or create the group container for this year and semester
            const groupTbody = getOrCreateGroupContainer(yearName, semesterName);
            
            // Add each selected course unit
            selectedCourses.forEach((checkbox, i) => {
                const courseItem = checkbox.closest('.course-unit-item');
                const courseId = courseItem.dataset.id;
                const courseLabel = courseItem.querySelector('label');
                const courseCode = courseLabel.querySelector('strong').textContent;
                const courseName = courseLabel.textContent.replace(courseCode, '').replace(' - ', '').trim();
                
                // Check if this course is already mapped to this year and semester
                const existingMapping = document.querySelector(`tr[data-course-id="${courseId}"]`);
                if (existingMapping) {
                    const existingYear = existingMapping.closest('.mapping-group')?.dataset.year;
                    const existingSemester = existingMapping.closest('.mapping-group')?.dataset.semester;
                    
                    if (existingYear === yearName && existingSemester === semesterName) {
                        console.log(`Course ${courseCode} is already mapped to ${yearName} ${semesterName}`);
                        return; // Skip this course as it's already mapped
                    }
                }
                
                // Create a new row
                const index = maxIndex + i + 1;
                const rowTemplate = document.getElementById('mapping-row-template').content.cloneNode(true);
                const row = rowTemplate.querySelector('tr');
                
                // Update row data and content
                row.dataset.courseId = courseId;
                row.id = `mapping-${index}`;
                
                // Set code and name in the correct order
                row.querySelector('.course-unit-code').textContent = courseCode;
                row.querySelector('.course-unit-name').textContent = courseName;
                
                // Update form fields
                const fields = row.querySelectorAll('input[type="hidden"]');
                fields.forEach(field => {
                    field.name = field.name.replace('INDEX', index);
                    if (field.classList.contains('course-unit-id')) field.value = courseId;
                    if (field.classList.contains('year-id')) field.value = yearId;
                    if (field.classList.contains('semester-id')) field.value = semesterId;
                });
                
                // Add to the group container
                groupTbody.appendChild(row);
                
                // Initialize tooltip for the new row
                const tooltipTriggerList = [].slice.call(row.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
                
                // Uncheck the checkbox
                checkbox.checked = false;
                courseItem.classList.remove('selected');
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
            if (row && confirm('Are you sure you want to remove this course unit?')) {
                const groupTbody = row.closest('tbody');
                row.remove();
                
                // If no more rows in this group, remove the entire group
                if (groupTbody && groupTbody.querySelectorAll('tr').length === 0) {
                    const groupCard = groupTbody.closest('.card');
                    if (groupCard) {
                        groupCard.remove();
                        
                        // If no more groups, show the "No mappings" message
                        const mappingsContainer = document.getElementById('mappings-container');
                        if (mappingsContainer && mappingsContainer.children.length === 0) {
                            const noMappingsAlert = document.createElement('div');
                            noMappingsAlert.className = 'alert alert-info';
                            noMappingsAlert.textContent = 'No course unit mappings found. Add some using the button below.';
                            mappingsContainer.appendChild(noMappingsAlert);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
