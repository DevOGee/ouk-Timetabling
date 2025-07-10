@extends('layouts.app')

@section('title', "Scheduling: $programme->programme_code")

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">{{ $programme->name }} ({{ $programme->programme_code }}) - Scheduling</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @foreach ($groupedMappings as $group => $mappings)
            <div class="mt-5">
                <h4 class="text-primary">{{ $group }}</h4>
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Instructor</th>
                            <th>Scheduled Slot</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mappings as $mapping)
                            @php
                                $course = $mapping->courseUnit;
                                $instructor = $mapping->instructor;
                            @endphp
                            <tr>
                                <td>{{ $course->code }}</td>
                                <td>{{ $course->name }}</td>
                                <td>
                                    @if ($instructor)
                                        {{ $instructor->name }}
                                    @else
                                        <span class="text-muted">No Instructor Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($mapping->morning_start_time || $mapping->evening_start_time)
                                        <span class="badge bg-info">
                                            {{ $mapping->day->name ?? 'N/A' }}
                                            @if ($mapping->morning_start_time)
                                                - {{ \Carbon\Carbon::parse($mapping->morning_start_time)->format('h:i A') }}
                                                ({{ $mapping->morning_duration }} min)
                                            @endif
                                            @if ($mapping->evening_start_time)
                                                / {{ \Carbon\Carbon::parse($mapping->evening_start_time)->format('h:i A') }}
                                                ({{ $mapping->evening_duration }} min)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">No Slot Assigned</span>
                                    @endif
                                </td>

                                <td>
                                    {{-- Assign/Unassign Instructor --}}
                                    @if ($mapping->user_id)
                                        <form
                                            action="{{ route('admin.academic-sessions.programmes.scheduling.remove-instructor', [
                                                'academicSession' => $academicSession->id,
                                                'programme' => $programme->id
                                            ]) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to unassign this instructor?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="mapping_id" value="{{ $mapping->id }}">
                                            <button type="submit" class="btn btn-warning btn-sm">Unassign</button>
                                        </form>
                                    @else
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#assignInstructorModal{{ $mapping->id }}">
                                            Assign Instructor
                                        </button>
                                    @endif

                                    {{-- Assign/Edit Slot --}}
                                    @if (!$mapping->morning_start_time && !$mapping->evening_start_time)
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#assignSlotModal{{ $mapping->id }}">
                                            Assign Slot
                                        </button>
                                    @else
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editSlotModal{{ $mapping->id }}">
                                            Edit Slot
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            
                            {{-- Include modals for each mapping --}}
                            @include('partials.modals.assign-instructor', [
                                'course' => $course, 
                                'mapping' => $mapping, 
                                'academicSession' => $academicSession,
                                'programme' => $programme,
                                'instructors' => $instructors
                            ])
                            @include('partials.modals.assign-slot', [
                                'course' => $course, 
                                'mapping' => $mapping, 
                                'days' => $days
                            ])
                            @include('partials.modals.edit-slot', [
                                'course' => $course, 
                                'mapping' => $mapping, 
                                'days' => $days,
                                'programme' => $programme,
                                'academicSession' => $academicSession
                            ])
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="mt-4">
            <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Academic Session
            </a>
        </div>

{{-- Modals are included individually for each mapping --}}

<!-- Bulk Schedule Modal -->
<div class="modal fade" id="bulkScheduleModal" tabindex="-1" aria-labelledby="bulkScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('admin.academic-sessions.programmes.scheduling.bulk-schedule', [$academicSession, $programme]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="bulkScheduleModalLabel">
                        <i class="bi bi-upload me-2"></i> Bulk Schedule Upload
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Download the template file with current courses, update the scheduling information, and upload the completed file.
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="downloadTemplate">
                                <i class="bi bi-download me-1"></i> Download Template
                            </button>
                        </div>
                        <div class="mt-2 small">
                            The template includes all current courses. You can add, modify, or remove rows as needed.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="scheduleFile" class="form-label">Schedule File (CSV)</label>
                        <input class="form-control" type="file" id="scheduleFile" name="schedule_file" accept=".csv" required>
                        <div class="form-text">
                            File must be in CSV format with the following columns: course_code, instructor_email, day_id, morning_start, morning_duration, evening_start, evening_duration
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Instructor Email</th>
                                    <th>Day</th>
                                    <th>Morning Start</th>
                                    <th>Duration (min)</th>
                                    <th>Evening Start</th>
                                    <th>Duration (min)</th>
                                </tr>
                            </thead>
                            <tbody id="previewTable">
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Upload a file to preview data
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBulkSchedule" disabled>
                        <i class="bi bi-upload me-1"></i> Upload & Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .preview-row {
        font-size: 0.85rem;
    }
    .preview-row td {
        vertical-align: middle;
    }
    .invalid-cell {
        background-color: #fff5f5;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips and modals when DOM is fully loaded
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        


        // Handle assign instructor modals
        document.querySelectorAll('[id^="assignInstructorModal"]').forEach(modal => {
            const mappingId = modal.id.replace('assignInstructorModal', '');
            const select = $(`#user_id_${mappingId}`);
            
            // Handle modal show event
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const courseCode = button.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
                
                // Set the course code in the modal title if needed
                const titleElement = this.querySelector('.modal-title');
                if (titleElement && !titleElement.textContent.includes(courseCode)) {
                    titleElement.textContent = `Assign Instructor to ${courseCode}`;
                }
                
                // Destroy existing Select2 if it exists
                if (select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
                
                // Initialize Select2 with proper configuration
                select.select2({
                    dropdownParent: $(`#assignInstructorModal${mappingId}`),
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
            modal.addEventListener('hidden.bs.modal', function () {
                if (select.length && select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
            });
        });
        
        // Old duplicate initialization code has been removed

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

        // Bulk scheduling functionality
        const bulkScheduleModal = document.getElementById('bulkScheduleModal');
        if (bulkScheduleModal) {
            const days = @json(\App\Models\Day::all()->pluck('name', 'id'));
            
            // Download template with current courses
            document.getElementById('downloadTemplate').addEventListener('click', function(e) {
                e.preventDefault();
                const url = '{{ route("admin.academic-sessions.programmes.scheduling.download-courses", [$academicSession, $programme]) }}';
                window.location.href = url;
            });

            // Handle file upload and preview
            document.getElementById('scheduleFile').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                Papa.parse(file, {
                    header: true,
                    skipEmptyLines: true,
                    complete: function(results) {
                        const previewTable = document.getElementById('previewTable');
                        previewTable.innerHTML = '';
                        
                        if (results.errors.length > 0) {
                            previewTable.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center text-danger">
                                        Error parsing file: ${results.errors[0].message}
                                    </td>
                                </tr>`;
                            return;
                        }

                        const data = results.data;
                        if (data.length === 0) {
                            previewTable.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No data found in the file
                                    </td>
                                </tr>`;
                            return;
                        }

                        // Validate required columns
                        const requiredColumns = ['course_code', 'day'];
                        const missingColumns = requiredColumns.filter(col => !results.meta.fields.includes(col));
                        
                        if (missingColumns.length > 0) {
                            previewTable.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center text-danger">
                                        Missing required columns: ${missingColumns.join(', ')}. Note: 'day' should be a day name (e.g., Monday, Tuesday, etc.)
                                    </td>
                                </tr>`;
                            return;
                        }

                        // Display preview
                        data.forEach((row, index) => {
                            const tr = document.createElement('tr');
                            tr.className = 'preview-row';
                            
                            // Helper to validate time format (HH:MM, 24-hour format)
                            const isValidTime = (time) => {
                                if (!time) return true;
                                return /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(time);
                            };
                            
                            // Helper to validate day name
                            const isValidDay = (day) => {
                                if (!day) return false;
                                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                return days.includes(day.toLowerCase().trim());
                            };
                            
                            // Helper to validate duration
                            const isValidDuration = (dur) => {
                                if (!dur) return true;
                                return !isNaN(dur) && parseInt(dur) > 0;
                            };
                            
                            const dayName = row.day ? row.day.trim() : '';
                            const dayId = Object.entries(days).find(([_, name]) => 
                                name.toLowerCase() === dayName.toLowerCase()
                            )?.[0];
                            
                            const cells = [
                                row.course_code || '',
                                row.instructor_email || '',
                                dayName || '-',
                                row.morning_start || '-',
                                row.morning_duration ? `${row.morning_duration} min` : '-',
                                row.evening_start || '-',
                                row.evening_duration ? `${row.evening_duration} min` : '-',
                            ];
                            
                            cells.forEach((cell, i) => {
                                const td = document.createElement('td');
                                td.textContent = cell;
                                
                                // Highlight invalid cells
                                if (i === 2) {
                                    if (!dayName) {
                                        td.classList.add('text-danger');
                                        td.title = 'Day is required';
                                    } else if (!dayId) {
                                        td.classList.add('text-danger');
                                        td.title = 'Invalid day name. Use full day names (e.g., Monday, Tuesday, etc.)';
                                    }
                                } else if ((i === 3 || i === 5) && cell !== '-' && !isValidTime(cell)) {
                                    td.classList.add('invalid-cell');
                                    td.title = 'Invalid time format. Use HH:MM (24-hour format, e.g., 09:00, 14:30)';
                                } else if ((i === 4 || i === 6) && cell !== '-' && !isValidDuration(row[i === 4 ? 'morning_duration' : 'evening_duration'])) {
                                    td.classList.add('invalid-cell');
                                    td.title = 'Invalid duration (must be a positive number)';
                                }
                                
                                tr.appendChild(td);
                            });
                            
                            previewTable.appendChild(tr);
                        });
                        
                        // Enable submit button if we have valid data
                        document.getElementById('submitBulkSchedule').disabled = false;
                    },
                    error: function(error) {
                        console.error('Error parsing CSV:', error);
                        const previewTable = document.getElementById('previewTable');
                        previewTable.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center text-danger">
                                    Error parsing file: ${error.message}
                                </td>
                            </tr>`;
                    }
                });
            });
        }
    });
</script>
@endpush
