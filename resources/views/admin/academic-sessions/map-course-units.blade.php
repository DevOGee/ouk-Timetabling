@extends('layouts.app')

@push('styles')
<style>
    /* Premium Page Header */
    .page-fade-in { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) both; }
    .stagger-1    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .05s both; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    
    .page-header-premium {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .04), 0 1px 3px rgba(15, 23, 42, .02);
        border: 1px solid rgba(226, 232, 240, .6);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    .page-header-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--slate-900);
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }
    .page-header-subtitle {
        font-size: 0.95rem;
        color: var(--slate-500);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Course Unit Items */
    .course-unit-item {
        cursor: pointer;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1.5px solid transparent;
        background: var(--slate-50);
        display: flex;
        align-items: center;
    }
    .course-unit-item:hover {
        background-color: var(--slate-100);
        border-color: var(--slate-200);
    }
    .course-unit-item.selected {
        background-color: rgba(3, 123, 144, 0.05);
        border-color: rgba(3, 123, 144, 0.3);
        color: var(--teal);
    }
    .course-unit-item input[type="checkbox"] {
        margin-right: 12px;
        width: 18px;
        height: 18px;
        accent-color: var(--teal);
        cursor: pointer;
    }
    .course-unit-item label {
        cursor: pointer;
        margin: 0;
        width: 100%;
        font-size: 0.9rem;
    }

    /* Modal Styling */
    .modal-content-premium {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .modal-header-premium {
        background: var(--slate-50);
        border-bottom: 1px solid var(--slate-100);
        padding: 1.5rem;
    }
    .modal-title-premium {
        font-weight: 800;
        color: var(--slate-800);
        font-size: 1.15rem;
    }
    .course-units-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1.5px solid var(--slate-200);
        border-radius: 12px;
        padding: 0.75rem;
        background: #fff;
    }

    /* Group Cards */
    .mapping-group-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        margin-bottom: 1.5rem;
        overflow: hidden;
        animation: fadeIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    .mapping-group-header {
        background: var(--slate-50);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--slate-100);
        font-weight: 700;
        color: var(--slate-800);
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Modern Form Inputs */
    .form-control-premium, .form-select-premium {
        border-radius: 8px;
        border: 1.5px solid var(--slate-200);
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: #fff;
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: var(--teal);
        box-shadow: 0 0 0 3px rgba(3, 123, 144, 0.1);
        outline: none;
    }
    .content-wrapper { background:transparent!important; box-shadow:none!important; padding:1.8rem 2rem!important; }
</style>
@endpush

@section('title', 'Map Course Units - ' . $programme->name . ' - ' . $academicSession->name)

@section('content')
<div class="content-wrapper page-fade-in">
    <nav class="breadcrumb-nav stagger-1" style="display:flex;align-items:center;gap:.4rem;font-size:.78rem;color:var(--slate-500);margin-bottom:1.25rem;">
        <a href="{{ route('admin.academic-sessions.index') }}" style="color:var(--slate-500);text-decoration:none;"><i class="bi bi-calendar2-week"></i> Academic Sessions</a>
        <span class="sep" style="color:var(--slate-300);">/</span>
        <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" style="color:var(--slate-500);text-decoration:none;">{{ Str::limit($academicSession->name, 30) }}</a>
        <span class="sep" style="color:var(--slate-300);">/</span>
        <span class="current" style="color:var(--slate-700);font-weight:600;">Map Course Units: {{ $programme->programme_code }}</span>
    </nav>

    <!-- Premium Header -->
    <div class="page-header-premium stagger-1">
        <div>
            <h2 class="page-header-title">Map Course Units</h2>
            <div class="page-header-subtitle">
                <span class="badge bg-light text-dark border"><i class="bi bi-upc-scan me-1 text-muted"></i> {{ $programme->programme_code }}</span>
                <span style="color: var(--slate-300);">•</span>
                <span class="fw-bold" style="color: var(--slate-700);">{{ $programme->name }}</span>
                <span style="color: var(--slate-300);">•</span>
                <span>{{ $academicSession->name }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-premium" data-bs-toggle="modal" data-bs-target="#addCourseUnitsModal">
                <i class="bi bi-plus-lg"></i> Add Course Units
            </button>
            <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn-outline-soft">
                <i class="bi bi-arrow-left"></i> Back to Session
            </a>
        </div>
    </div>

    <!-- Mappings Container -->
    <div id="mappings-container">
        @forelse($groupedMappings as $groupName => $mappingsGroup)
            @php
                list($year, $semester) = explode('.', $groupName);
                $groupLabel = "Level {$year}.{$semester}";
            @endphp
            <div class="mapping-group-card" id="group-{{ $year }}-{{ $semester }}" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                <div class="mapping-group-header">
                    <i class="bi bi-layers text-teal" style="color: var(--teal);"></i>
                    {{ $groupLabel }}
                </div>
                <div class="table-responsive" style="margin: 0; padding: 0;">
                    <table class="table premium-table mb-0 w-100" style="border: none;">
                        <thead style="background: #fff; border-bottom: 1px solid var(--slate-100);">
                            <tr>
                                <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; width: 120px;">Code</th>
                                <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none;">Course Unit Name</th>
                                <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; text-align: center; width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mapping-group-{{ $year }}-{{ $semester }}">
                            @foreach($mappingsGroup as $mapping)
                                @include('admin.academic-sessions.partials.course-unit-mapping-row', [
                                    'mapping' => $mapping,
                                    'showYearSemester' => false
                                ])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="alert alert-info d-flex align-items-center p-4" style="background: rgba(59,130,246,.05); border: 1px dashed rgba(59,130,246,.3); border-radius: 12px; color: #1e40af;">
                <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                <div>
                    <strong>No course unit mappings found.</strong><br>
                    <span class="text-primary opacity-75">Click 'Add Course Units' to begin building the curriculum for this programme.</span>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Course Units Modal -->
<div class="modal fade" id="addCourseUnitsModal" tabindex="-1" aria-labelledby="addCourseUnitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title modal-title-premium" id="addCourseUnitsModalLabel">
                    <i class="bi bi-journal-plus me-2" style="color: var(--teal);"></i> Add Course Units
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4 g-3">
                    <div class="col-md-6">
                        <label for="year_select" class="form-label fw-bold text-slate-700" style="font-size: 0.85rem;">Year of Study</label>
                        <select id="year_select" class="form-select form-select-premium" required>
                            <option value="">Select Year</option>
                            @foreach($yearsOfStudy as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="semester_select" class="form-label fw-bold text-slate-700" style="font-size: 0.85rem;">Semester</label>
                        <select id="semester_select" class="form-select form-select-premium" required>
                            <option value="">Select Semester</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3 position-relative">
                    <i class="bi bi-search position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: var(--slate-400);"></i>
                    <input type="text" id="courseSearch" class="form-control form-control-premium" placeholder="Search course units by name or code..." style="padding-left: 2.5rem;">
                </div>

                <div class="course-units-container">
                    @foreach($courseUnits as $courseUnit)
                        <div class="course-unit-item" data-id="{{ $courseUnit->id }}">
                            <input type="checkbox" id="course_{{ $courseUnit->id }}">
                            <label for="course_{{ $courseUnit->id }}">
                                <span class="badge bg-light text-dark border me-2">{{ $courseUnit->code }}</span>
                                <span class="fw-semibold text-slate-800">{{ $courseUnit->name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--slate-100); padding: 1.25rem 1.5rem; background: var(--slate-50);">
                <button type="button" class="btn-outline-soft" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-premium" id="addSelectedCourses">
                    <i class="bi bi-plus-lg"></i> Add Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template for new row -->
<template id="mapping-row-template">
    <tr id="mapping-new-INDEX" data-course-id="" style="transition: all 0.3s;">
        <td class="course-unit-code fw-bold text-slate-800" style="padding: 1rem 1.5rem;"></td>
        <td class="course-unit-name text-slate-600" style="padding: 1rem 1.5rem;"></td>
        <td class="text-center" style="padding: 1rem 1.5rem;">
            <button type="button" class="btn btn-sm btn-outline-danger remove-mapping" data-bs-toggle="tooltip" title="Remove Mapping" style="border-radius: 6px;">
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
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Base URLs for API endpoints
    const addCourseUnitUrl = '{{ route("admin.academic-sessions.programmes.course-units.add", [$academicSession, $programme]) }}';
    const removeCourseUnitUrl = (courseUnitId) => 
        `{{ route('admin.academic-sessions.programmes.course-units.remove', [$academicSession, $programme, '']) }}/${courseUnitId}`;
    
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
            groupContainer.className = 'mapping-group-card';
            groupContainer.innerHTML = `
                <div class="mapping-group-header">
                    <i class="bi bi-layers text-teal" style="color: var(--teal);"></i>
                    ${groupLabel}
                </div>
                <div class="table-responsive" style="margin: 0; padding: 0;">
                    <table class="table premium-table mb-0 w-100" style="border: none;">
                        <thead style="background: #fff; border-bottom: 1px solid var(--slate-100);">
                            <tr>
                                <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; width: 120px;">Code</th>
                                <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none;">Course Unit Name</th>
                                <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; text-align: center; width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="mapping-group" data-year="${year}" data-semester="${semester}">
                            <!-- Mappings will be added here -->
                        </tbody>
                    </table>
                </div>
            `;
            
            // Insert the new group before the "No mappings" message or at the end
            const mappingsContainer = document.getElementById('mappings-container');
            const noMappingsAlert = mappingsContainer.querySelector('.alert');
            
            if (noMappingsAlert) {
                noMappingsAlert.remove();
            }
            
            mappingsContainer.appendChild(groupContainer); // Or insert before if you want specific sorting via JS
        }
        
        return groupContainer.querySelector('tbody');
    }
    
    // Handle course unit selection visually
    document.querySelectorAll('.course-unit-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'LABEL') {
                const checkbox = this.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    this.classList.toggle('selected', checkbox.checked);
                }
            } else if (e.target.tagName === 'INPUT') {
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
                item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    }
    
    // Handle Add Selected button click
    if (addSelectedBtn) {
        addSelectedBtn.addEventListener('click', async function(e) {
            const yearId = yearSelect?.value;
            const semesterId = semesterSelect?.selectedOptions[0]?.value;
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
            addSelectedBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Adding...';
            
            try {
                for (const checkbox of selectedCourses) {
                    const courseItem = checkbox.closest('.course-unit-item');
                    const courseId = courseItem.dataset.id;
                    const courseCode = courseItem.querySelector('.badge').textContent;
                    const courseName = courseItem.querySelector('.fw-semibold').textContent;
                    
                    const existingMapping = document.querySelector(`tr[data-course-id="${courseId}"]`);
                    if (existingMapping) {
                        const existingYear = existingMapping.closest('tbody')?.dataset.year;
                        const existingSemester = existingMapping.closest('tbody')?.dataset.semester;
                        
                        if (existingYear === yearName && existingSemester === semesterName) {
                            continue;
                        }
                    }
                    
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
                                semester_id: semesterId
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            const groupTbody = getOrCreateGroupContainer(yearName, semesterName);
                            const rowTemplate = document.getElementById('mapping-row-template').content.cloneNode(true);
                            const row = rowTemplate.querySelector('tr');
                            
                            row.dataset.courseId = courseId;
                            row.id = `mapping-${courseId}`;
                            row.querySelector('.course-unit-code').textContent = courseCode;
                            row.querySelector('.course-unit-name').textContent = courseName;
                            
                            // Highlight new row briefly
                            row.style.backgroundColor = 'rgba(16, 185, 129, 0.1)';
                            setTimeout(() => {
                                row.style.backgroundColor = 'transparent';
                            }, 1500);

                            groupTbody.appendChild(row);
                            
                            const tooltipTriggerList = [].slice.call(row.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                            
                            showToast('success', `${courseCode} added successfully`);
                        } else {
                            throw new Error(result.message || 'Failed to add course unit');
                        }
                    } catch (error) {
                        showToast('error', `Failed to add ${courseCode}: ${error.message}`);
                    }
                    
                    checkbox.checked = false;
                    courseItem.classList.remove('selected');
                }
                
                if (yearSelect) yearSelect.value = '';
                if (semesterSelect) semesterSelect.value = '';
                if (courseSearch) {
                    courseSearch.value = '';
                    courseSearch.dispatchEvent(new Event('input'));
                }
                
                if (modal) modal.hide();
                
            } catch (error) {
                showToast('error', 'An error occurred while adding course units');
            } finally {
                addSelectedBtn.disabled = false;
                addSelectedBtn.innerHTML = originalButtonText;
            }
        });
    }
    
    // Handle remove mapping button clicks
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.remove-mapping')) {
            e.preventDefault();
            const btn = e.target.closest('.remove-mapping');
            const row = btn.closest('tr');
            const courseUnitId = row?.dataset?.courseId;
            
            if (!courseUnitId || !confirm('Are you sure you want to remove this course unit?')) {
                return;
            }
            
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            btn.disabled = true;

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
                    const groupTbody = row.closest('tbody');
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        if (groupTbody && groupTbody.querySelectorAll('tr').length === 0) {
                            const groupCard = groupTbody.closest('.mapping-group-card');
                            if (groupCard) {
                                groupCard.style.opacity = '0';
                                setTimeout(() => groupCard.remove(), 300);
                            }
                        }
                        
                        if (document.querySelectorAll('.mapping-group-card').length === 0) {
                            const noMappingsHtml = `
                                <div class="alert alert-info d-flex align-items-center p-4" style="background: rgba(59,130,246,.05); border: 1px dashed rgba(59,130,246,.3); border-radius: 12px; color: #1e40af;">
                                    <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                                    <div>
                                        <strong>No course unit mappings found.</strong><br>
                                        <span class="text-primary opacity-75">Click 'Add Course Units' to begin building the curriculum for this programme.</span>
                                    </div>
                                </div>
                            `;
                            document.getElementById('mappings-container').innerHTML = noMappingsHtml;
                        }
                    }, 300);
                    
                    showToast('success', result.message);
                } else {
                    throw new Error(result.message || 'Failed to remove course unit');
                }
            } catch (error) {
                btn.innerHTML = originalIcon;
                btn.disabled = false;
                showToast('error', error.message || 'An error occurred while removing the course unit');
            }
        }
    });

    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-medium">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        const toastElement = document.createElement('div');
        toastElement.innerHTML = toastHtml.trim();
        toastContainer.appendChild(toastElement.firstElementChild);
        
        const toast = new bootstrap.Toast(toastElement.firstElementChild, {
            autohide: true,
            delay: 4000
        });
        
        toast.show();
        
        toastElement.firstElementChild.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }
});
</script>
@endpush

<!-- Toast container -->
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 1060"></div>

@endsection
