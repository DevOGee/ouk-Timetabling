@extends('layouts.app')

@section('title', "Scheduling: {$programme->name}")

@section('content')
    <!-- Success Toast -->
    @if(session('success'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="successToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    <!-- Error Toast -->
    @if($errors->any())
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="errorToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Error</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .schedule-table {
        min-width: 100%;
    }
    .course-card {
        transition: all 0.2s;
        border-left: 4px solid #4e73df;
    }
    .course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .time-slot {
        font-size: 0.8rem;
        white-space: nowrap;
    }
    .schedule-slots .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    .schedule-slots .badge i {
        margin-right: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-calendar-week text-primary"></i>
            {{ $programme->name }} - Scheduling
        </h1>
        <small class="text-muted">{{ $academicSession->name }}</small>
    </div>

    @include('partials.alert')

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Schedule</h6>
            <div>
                <span class="badge bg-primary">Morning</span>
                <span class="badge bg-info">Evening</span>
            </div>
        </div>
        <div class="card-body">
            @if($groupedMappings->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-gray-400" style="font-size: 3rem;"></i>
                    <p class="text-muted">No courses scheduled yet. Add courses to this programme first.</p>
                    <a href="{{ route('admin.academic-sessions.programmes.map-course-units', ['academicSession' => $academicSession, 'programme' => $programme]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Courses
                    </a>
                </div>
            @else
                @foreach($groupedMappings as $groupName => $mappingsGroup)
                    @php
                        $year = 'N/A';
                        $semester = 'N/A';
                        $groupLabel = 'Other';
                        
                        if (str_contains($groupName, '.')) {
                            $parts = explode('.', $groupName, 2);
                            $year = $parts[0] ?? 'N/A';
                            $semester = $parts[1] ?? 'N/A';
                            $groupLabel = "Level {$year}.{$semester}";
                        } else if (!empty($groupName)) {
                            $groupLabel = $groupName;
                        }
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
                                            <th>Instructor</th>
                                            <th>Schedule</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mappingsGroup as $mapping)
                                            @if(!is_object($mapping) || !isset($mapping->courseUnit))
                                                @continue
                                            @endif
                                            <tr data-mapping-id="{{ $mapping->id }}" 
                                                data-day-id="{{ $mapping->day_id }}"
                                                data-morning-start="{{ $mapping->morning_start_time ? \Carbon\Carbon::parse($mapping->morning_start_time)->format('H:i') : '' }}"
                                                data-morning-duration="{{ $mapping->morning_duration ?? '' }}"
                                                data-evening-start="{{ $mapping->evening_start_time ? \Carbon\Carbon::parse($mapping->evening_start_time)->format('H:i') : '' }}"
                                                data-evening-duration="{{ $mapping->evening_duration ?? '' }}">
                                                <td class="fw-bold">{{ $mapping->courseUnit->code ?? 'N/A' }}</td>
                                                <td>{{ $mapping->courseUnit->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($mapping->instructor)
                                                        {{ $mapping->instructor->name }}
                                                    @else
                                                        <span class="text-muted">Not assigned</span>
                                                    @endif
                                                </td>
                                                <td class="time-slot">
                                                    @if($mapping->day_id && ($mapping->morning_start_time || $mapping->evening_start_time))
                                                        <div class="schedule-slots">
                                                            @php
                                                                // Format time to 12-hour format with AM/PM
                                                                $formatTime = function($time) {
                                                                    if (!$time) return '';
                                                                    return \Carbon\Carbon::parse($time)->format('h:i A');
                                                                };
                                                                
                                                                $dayShown = false;
                                                            @endphp
                                                            
                                                            @if($mapping->morning_start_time)
                                                            <div class="mb-2">
                                                                @if(!$dayShown)
                                                                    <strong>{{ $mapping->day->name }}</strong>
                                                                    @php $dayShown = true; @endphp
                                                                @endif
                                                                <span class="badge bg-primary">
                                                                    Morning: {{ $formatTime($mapping->morning_start_time) }} ({{ $mapping->morning_duration }} mins)
                                                                </span>
                                                            </div>
                                                            @endif
                                                            
                                                            @if($mapping->evening_start_time)
                                                            <div class="mb-2">
                                                                @if(!$dayShown)
                                                                    <strong>{{ $mapping->day->name }}</strong>
                                                                    @php $dayShown = true; @endphp
                                                                @endif
                                                                <span class="badge bg-info">
                                                                    Evening: {{ $formatTime($mapping->evening_start_time) }} ({{ $mapping->evening_duration }} mins)
                                                                </span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Not scheduled</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-gear"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @if(!$mapping->instructor)
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                                                       data-bs-target="#assignInstructorModal"
                                                                       data-course-unit-id="{{ $mapping->course_unit_id }}">
                                                                        <i class="bi bi-person-plus me-2"></i>Assign Instructor
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                                                       data-bs-target="#assignInstructorModal"
                                                                       data-course-unit-id="{{ $mapping->course_unit_id }}"
                                                                       data-current-instructor="{{ $mapping->instructor->id }}">
                                                                        <i class="bi bi-person-check me-2"></i>Edit Instructor
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       onclick="if(confirm('Remove this instructor?')) { document.getElementById('remove-instructor-{{ $mapping->id }}').submit(); }">
                                                                        <i class="bi bi-person-dash me-2"></i>Remove Instructor
                                                                    </a>
                                                                    <form id="remove-instructor-{{ $mapping->id }}" 
                                                                          action="{{ route('admin.academic-sessions.programmes.scheduling.remove-instructor', ['academicSession' => $academicSession, 'programme' => $programme]) }}" 
                                                                          method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <input type="hidden" name="course_unit_id" value="{{ $mapping->course_unit_id }}">
                                                                    </form>
                                                                </li>
                                                            @endif

                                                            <li><hr class="dropdown-divider"></li>

                                                            @if(!$mapping->day_id)
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                                                       data-bs-target="#assignSlotModal"
                                                                       data-course-unit-id="{{ $mapping->course_unit_id }}"
                                                                       data-mapping-id="{{ $mapping->id }}">
                                                                        <i class="bi bi-calendar-plus me-2"></i>Schedule Time
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                                                       data-bs-target="#assignSlotModal"
                                                                       data-course-unit-id="{{ $mapping->course_unit_id }}"
                                                                       data-mapping-id="{{ $mapping->id }}"
                                                                       data-edit="true">
                                                                        <i class="bi bi-calendar-check me-2"></i>Edit Schedule
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       onclick="if(confirm('Remove this schedule?')) { document.getElementById('delete-slot-{{ $mapping->id }}').submit(); }">
                                                                        <i class="bi bi-trash me-2"></i>Remove Schedule
                                                                    </a>
                                                                    <form id="delete-slot-{{ $mapping->id }}" 
                                                                          action="{{ route('admin.academic-sessions.programmes.scheduling.delete-slot', ['academicSession' => $academicSession, 'programme' => $programme, 'mapping' => $mapping]) }}" 
                                                                          method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    
    <!-- Action Buttons at Bottom -->
    <div class="d-flex justify-content-between mt-4 mb-5">
        <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Academic Session
        </a>
        <div>
            <a href="{{ route('admin.academic-sessions.programmes.map-course-units', ['academicSession' => $academicSession, 'programme' => $programme]) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Curriculum
            </a>
        </div>
    </div>
</div>

@include('admin.programmes.scheduling.modals.assign-instructor')
@include('admin.programmes.scheduling.modals.assign-slot')
@include('admin.programmes.scheduling.modals.edit-slot')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips and modals when DOM is fully loaded
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        


        // Handle assign instructor modal
        const assignInstructorModal = document.getElementById('assignInstructorModal');
        if (assignInstructorModal) {
            // Initialize Select2 when modal is about to be shown
            assignInstructorModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const mappingId = button.getAttribute('data-mapping-id');
                const courseUnitId = button.getAttribute('data-course-unit-id');
                const courseCode = button.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
                const modalForm = assignInstructorModal.querySelector('form');
                
                console.log('Opening instructor modal for mapping:', mappingId, 'course unit:', courseUnitId);
                
                // Set the mapping ID and course unit ID
                modalForm.querySelector('input[name="mapping_id"]').value = mappingId || '';
                modalForm.querySelector('input[name="course_unit_id"]').value = courseUnitId || '';
                
                // Set the course code in the modal title
                document.getElementById('courseCodeDisplay').textContent = courseCode;
                
                // Initialize or reinitialize Select2
                const select = $('#user_id');
                
                // Destroy existing Select2 if it exists
                if (select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
                
                // Initialize Select2 with proper configuration
                select.select2({
                    dropdownParent: $('#assignInstructorModal'),
                    width: '100%',
                    placeholder: 'Search for an instructor...',
                    allowClear: true,
                    theme: 'bootstrap-5',
                    dropdownAutoWidth: true
                });
                
                // Reset the selection
                select.val(null).trigger('change');
            });
            
            // Clean up Select2 when modal is hidden
            assignInstructorModal.addEventListener('hidden.bs.modal', function () {
                const select = $('#user_id');
                if (select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
            });
        }

        // Handle edit slot modal
        const editSlotModal = document.getElementById('editSlotModal');
        if (editSlotModal) {
            editSlotModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const modal = $(this);
                const form = modal.find('form');
                
                // Set mapping ID and action URL
                const mappingId = button.getAttribute('data-mapping-id');
                form.find('input[name="mapping_id"]').val(mappingId);
                const baseUrl = '{{ route('admin.academic-sessions.programmes.scheduling.update-slot', ['academicSession' => $academicSession->id, 'programme' => $programme->id, 'mapping' => 0]) }}';
                form.attr('action', baseUrl.replace('/0', '/' + mappingId));
                
                // Set course code in title
                const courseCode = button.getAttribute('data-course-code');
                modal.find('#editCourseCodeTitle').text(`Edit Schedule - ${courseCode}`);
                
                // Set day
                const dayId = button.getAttribute('data-day-id');
                form.find('#edit_day_id').val(dayId);
                
                // Handle morning session
                const morningStart = button.getAttribute('data-morning-start');
                const morningDuration = button.getAttribute('data-morning-duration');
                const hasMorning = morningStart && morningDuration;
                
                form.find('#edit_enable_morning').prop('checked', hasMorning);
                if (hasMorning) {
                    form.find('#edit_morning_start').val(morningStart);
                    form.find('#edit_morning_duration').val(morningDuration);
                } else {
                    form.find('#edit_morning_start').val('08:00');
                    form.find('#edit_morning_duration').val('60');
                }
                form.find('#edit_morning_fields input').prop('disabled', !hasMorning);
                
                // Handle evening session
                const eveningStart = button.getAttribute('data-evening-start');
                const eveningDuration = button.getAttribute('data-evening-duration');
                const hasEvening = eveningStart && eveningDuration;
                
                form.find('#edit_enable_evening').prop('checked', hasEvening);
                if (hasEvening) {
                    form.find('#edit_evening_start').val(eveningStart);
                    form.find('#edit_evening_duration').val(eveningDuration);
                } else {
                    form.find('#edit_evening_start').val('17:00');
                    form.find('#edit_evening_duration').val('60');
                }
                form.find('#edit_evening_fields').toggle(hasEvening);
                form.find('#edit_evening_fields input').prop('disabled', !hasEvening);
                
                // Set up delete button
                form.find('#deleteScheduleBtn').off('click').on('click', function() {
                    if (confirm('Are you sure you want to delete this schedule?')) {
                        const deleteForm = document.createElement('form');
                        deleteForm.method = 'POST';
                        const deleteBaseUrl = '{{ route('admin.academic-sessions.programmes.scheduling.delete-slot', ['academicSession' => $academicSession->id, 'programme' => $programme->id, 'mapping' => 0]) }}';
                        deleteForm.action = deleteBaseUrl.replace('/0', '/' + mappingId);
                        deleteForm.innerHTML = `
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="mapping_id" value="${mappingId}">
                        `;
                        document.body.appendChild(deleteForm);
                        deleteForm.submit();
                    }
                });
            });
        }

        // Handle assign slot modal
        const assignSlotModal = document.getElementById('assignSlotModal');
        if (assignSlotModal) {
            assignSlotModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const modal = $(this);
                
                // Set mapping ID
                modal.find('input[name="mapping_id"]').val(button.getAttribute('data-mapping-id'));
                
                // Set course unit info
                const courseUnitName = button.getAttribute('data-course-unit-name');
                modal.find('.course-unit-name').text(courseUnitName);
                
                // Reset form
                modal.find('form')[0].reset();
            });
        }

        // Show success/error toasts if needed
        @if(session('success'))
            const toast = new bootstrap.Toast(document.getElementById('successToast'));
            toast.show();
        @endif

        @if($errors->any())
            const errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
            errorToast.show();
        @endif
    });
</script>
@endpush
