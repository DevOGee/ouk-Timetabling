@extends('layouts.app')

@section('title', "Scheduling: $programme->programme_code")

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Premium Page Header */
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

    /* Group Cards */
    .schedule-group-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        margin-bottom: 1.5rem;
        overflow: hidden;
        animation: fadeIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    .schedule-group-header {
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

    /* Modals & Bulk Upload */
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
    .preview-row { font-size: 0.85rem; }
    .preview-row td { vertical-align: middle; }
    .invalid-cell { background-color: #fff5f5; }
</style>
@endpush

@section('content')
<div class="container-fluid" style="padding: 0 2rem;">
    <!-- Premium Header -->
    <div class="page-header-premium">
        <div>
            <h2 class="page-header-title">Scheduling Console</h2>
            <div class="page-header-subtitle">
                <span class="badge bg-light text-dark border"><i class="bi bi-upc-scan me-1 text-muted"></i> {{ $programme->programme_code }}</span>
                <span style="color: var(--slate-300);">•</span>
                <span class="fw-bold" style="color: var(--slate-700);">{{ $programme->name }}</span>
                <span style="color: var(--slate-300);">•</span>
                <span>{{ $academicSession->name }}</span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn-outline-soft">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <a href="{{ url("/admin/academic-sessions/{$academicSession->id}/programmes/{$programme->id}/map-course-units") }}" class="btn-outline-soft">
                <i class="bi bi-pencil-square text-teal"></i> Edit Curriculum
            </a>
            
            <div class="dropdown">
                <button class="btn-outline-soft dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Export
                </button>
                <ul class="dropdown-menu shadow-sm" aria-labelledby="exportDropdown" style="border-radius: 8px; border: 1px solid var(--slate-200);">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.academic-sessions.programmes.scheduling.export', ['academicSession' => $academicSession->id, 'programme' => $programme->id, 'format' => 'pdf']) }}" target="_blank">
                            <i class="bi bi-file-pdf" style="color: #ef4444;"></i> PDF Document
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.academic-sessions.programmes.scheduling.export', ['academicSession' => $academicSession->id, 'programme' => $programme->id, 'format' => 'excel']) }}">
                            <i class="bi bi-file-excel" style="color: #10b981;"></i> Excel Spreadsheet
                        </a>
                    </li>
                </ul>
            </div>
            
            <button type="button" class="btn-premium" data-bs-toggle="modal" data-bs-target="#bulkScheduleModal">
                <i class="bi bi-cloud-upload"></i> Bulk Upload
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center p-3 mb-4" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center p-3 mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @forelse ($groupedMappings as $group => $mappings)
        <div class="schedule-group-card" style="animation-delay: {{ $loop->index * 0.05 }}s;">
            <div class="schedule-group-header">
                <i class="bi bi-layers text-teal" style="color: var(--teal);"></i>
                Level {{ $group }}
            </div>
            <div class="table-responsive" style="margin: 0; padding: 0;">
                <table class="table premium-table mb-0 w-100" style="border: none;">
                    <thead style="background: #fff; border-bottom: 1px solid var(--slate-100);">
                        <tr>
                            <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; width: 35%;">Course</th>
                            <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; width: 25%;">Instructor</th>
                            <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; width: 30%;">Schedule</th>
                            <th style="padding: 0.8rem 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 1px; border: none; text-align: right; width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mappings as $mapping)
                            @php
                                $course = $mapping->courseUnit;
                                $instructor = $mapping->instructor;
                            @endphp
                            <tr style="transition: background-color 0.2s; border-bottom: 1px solid var(--slate-100);">
                                <td style="padding: 1rem 1.5rem; vertical-align: middle;">
                                    <div class="fw-bold text-slate-900" style="font-size: 0.95rem;">{{ $course->code }}</div>
                                    <div class="text-slate-500" style="font-size: 0.85rem;">{{ $course->name }}</div>
                                </td>
                                <td style="padding: 1rem 1.5rem; vertical-align: middle;">
                                    @if ($instructor)
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--teal-bg); color: var(--teal); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">
                                                {{ substr($instructor->name, 0, 1) }}
                                            </div>
                                            <span class="text-slate-800 fw-medium" style="font-size: 0.9rem;">{{ $instructor->name }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1"><i class="bi bi-person-x me-1"></i> Unassigned</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.5rem; vertical-align: middle;">
                                    @if ($mapping->morning_start_time || $mapping->evening_start_time)
                                        <div class="d-flex flex-column gap-1">
                                            @if ($mapping->day)
                                                <div class="fw-bold text-slate-800" style="font-size: 0.85rem;">
                                                    <i class="bi bi-calendar-event text-teal me-1"></i> {{ $mapping->day->name }}
                                                </div>
                                            @endif
                                            
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                @if ($mapping->morning_start_time)
                                                    <span class="badge" style="background: rgba(3, 123, 144, 0.1); color: var(--teal); border: 1px solid rgba(3, 123, 144, 0.2); font-weight: 600; padding: 0.4rem 0.6rem;">
                                                        <i class="bi bi-sun me-1 text-warning"></i>
                                                        {{ \Carbon\Carbon::parse($mapping->morning_start_time)->format('h:i A') }}
                                                        <span class="opacity-75 ms-1 fw-normal">({{ $mapping->morning_duration }}m)</span>
                                                    </span>
                                                @endif
                                                
                                                @if ($mapping->evening_start_time)
                                                    <span class="badge" style="background: rgba(15, 23, 42, 0.05); color: var(--slate-800); border: 1px solid rgba(15, 23, 42, 0.1); font-weight: 600; padding: 0.4rem 0.6rem;">
                                                        <i class="bi bi-moon-stars me-1 text-indigo-500" style="color: #6366f1;"></i>
                                                        {{ \Carbon\Carbon::parse($mapping->evening_start_time)->format('h:i A') }}
                                                        <span class="opacity-75 ms-1 fw-normal">({{ $mapping->evening_duration }}m)</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400" style="font-size: 0.85rem; font-style: italic;">No slots scheduled</span>
                                    @endif
                                </td>

                                <td style="padding: 1rem 1.5rem; vertical-align: middle; text-align: right;">
                                    <div class="d-flex gap-2 justify-content-end">
                                        {{-- Assign/Unassign Instructor --}}
                                        @if ($mapping->user_id)
                                            <form action="{{ route('admin.academic-sessions.programmes.scheduling.remove-instructor', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this instructor?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="mapping_id" value="{{ $mapping->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" title="Unassign Instructor" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;">
                                                    <i class="bi bi-person-dash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#assignInstructorModal{{ $mapping->id }}" data-toggle="tooltip" title="Assign Instructor" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border-color: var(--slate-300);">
                                                <i class="bi bi-person-plus text-slate-600"></i>
                                            </button>
                                        @endif

                                        {{-- Assign/Edit Slot --}}
                                        @if (!$mapping->morning_start_time && !$mapping->evening_start_time)
                                            <button class="btn btn-sm" style="background: rgba(3, 123, 144, 0.1); color: var(--teal); width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid rgba(3, 123, 144, 0.2);" data-bs-toggle="modal" data-bs-target="#assignSlotModal{{ $mapping->id }}" data-toggle="tooltip" title="Schedule Slots">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm" style="background: #fff; color: var(--teal); width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1.5px solid rgba(3, 123, 144, 0.3);" data-bs-toggle="modal" data-bs-target="#editSlotModal{{ $mapping->id }}" data-toggle="tooltip" title="Edit Slots">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @endif
                                    </div>
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
        </div>
    @empty
        <div class="alert alert-info d-flex align-items-center p-4" style="background: rgba(59,130,246,.05); border: 1px dashed rgba(59,130,246,.3); border-radius: 12px; color: #1e40af;">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
            <div>
                <strong>No course units mapped yet.</strong><br>
                <span class="text-primary opacity-75">Click 'Edit Curriculum' to map course units to this programme before scheduling.</span>
            </div>
        </div>
    @endforelse
</div>

<!-- Bulk Schedule Modal -->
<div class="modal fade" id="bulkScheduleModal" tabindex="-1" aria-labelledby="bulkScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-premium">
            <form action="{{ route('admin.academic-sessions.programmes.scheduling.bulk-schedule', [$academicSession, $programme]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header modal-header-premium">
                    <h5 class="modal-title modal-title-premium" id="bulkScheduleModalLabel">
                        <i class="bi bi-cloud-arrow-up me-2" style="color: var(--teal);"></i> Bulk Schedule Upload
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert" style="background: rgba(3, 123, 144, 0.05); border: 1px dashed rgba(3, 123, 144, 0.3); border-radius: 12px; color: var(--slate-700);">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="d-flex gap-3">
                                <i class="bi bi-info-circle-fill fs-4 text-teal"></i>
                                <div>
                                    <strong class="text-slate-800">How it works</strong>
                                    <p class="mb-0 small">Download the template file with current courses, update the scheduling information in Excel, and upload the completed CSV file below.</p>
                                </div>
                            </div>
                            <button type="button" class="btn-outline-soft" id="downloadTemplate">
                                <i class="bi bi-download me-1"></i> Download Template
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4 mt-4">
                        <label for="scheduleFile" class="form-label fw-bold text-slate-700">Schedule File (CSV)</label>
                        <input class="form-control" style="border-radius: 8px; border: 1.5px dashed var(--slate-300); padding: 1rem;" type="file" id="scheduleFile" name="schedule_file" accept=".csv" required>
                        <div class="form-text text-slate-500 mt-2">
                            <i class="bi bi-asterisk"></i> Columns needed: <code class="bg-light px-1 py-0 border rounded">course_code, instructor_email, day, morning_start, morning_duration, evening_start, evening_duration</code>
                        </div>
                    </div>
                    
                    <div class="table-responsive rounded border border-slate-200">
                        <table class="table table-borderless table-sm mb-0">
                            <thead style="background: var(--slate-50); border-bottom: 1px solid var(--slate-200);">
                                <tr>
                                    <th class="py-2 px-3 text-slate-600 text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Course Code</th>
                                    <th class="py-2 px-3 text-slate-600 text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Instructor Email</th>
                                    <th class="py-2 px-3 text-slate-600 text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Day</th>
                                    <th class="py-2 px-3 text-slate-600 text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Morning Start</th>
                                    <th class="py-2 px-3 text-slate-600 text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Duration</th>
                                    <th class="py-2 px-3 text-slate-600 text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Evening Start</th>
                                    <th class="py-2 px-3 text-slate-600 text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Duration</th>
                                </tr>
                            </thead>
                            <tbody id="previewTable">
                                <tr>
                                    <td colspan="7" class="text-center text-slate-400 py-4">
                                        <i class="bi bi-file-earmark-spreadsheet fs-3 d-block mb-2"></i>
                                        Upload a file to preview data here
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--slate-100); padding: 1.25rem 1.5rem; background: var(--slate-50);">
                    <button type="button" class="btn-outline-soft" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-premium" id="submitBulkSchedule" disabled>
                        <i class="bi bi-check-circle"></i> Complete Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Handle assign instructor modals
        document.querySelectorAll('[id^="assignInstructorModal"]').forEach(modal => {
            const mappingId = modal.id.replace('assignInstructorModal', '');
            const select = $(`#user_id_${mappingId}`);
            
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
                
                select.select2({
                    dropdownParent: $(`#assignInstructorModal${mappingId}`),
                    width: '100%',
                    placeholder: 'Search for an instructor...',
                    allowClear: true,
                    theme: 'bootstrap-5'
                });
                select.val(null).trigger('change');
            });
            
            modal.addEventListener('hidden.bs.modal', function () {
                if (select.length && select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
            });
        });
        
        // Handle edit slot modal logic...
        const editSlotModal = document.getElementById('editSlotModal');
        if (editSlotModal) {
            editSlotModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const modal = $(this);
                const form = modal.find('form');
                
                const mappingId = button.getAttribute('data-mapping-id');
                form.find('input[name="mapping_id"]').val(mappingId);
                const baseUrl = '{{ route('admin.academic-sessions.programmes.scheduling.update-slot', ['academicSession' => $academicSession->id, 'programme' => $programme->id, 'mapping' => 0]) }}';
                form.attr('action', baseUrl.replace('/0', '/' + mappingId));
                
                const dayId = button.getAttribute('data-day-id');
                form.find('#edit_day_id').val(dayId);
                
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
                modal.find('input[name="mapping_id"]').val(button.getAttribute('data-mapping-id'));
                modal.find('form')[0].reset();
            });
        }

        // Bulk scheduling functionality
        const bulkScheduleModal = document.getElementById('bulkScheduleModal');
        if (bulkScheduleModal) {
            const days = @json(\App\Models\Day::all()->pluck('name', 'id'));
            
            document.getElementById('downloadTemplate').addEventListener('click', function(e) {
                e.preventDefault();
                const url = '{{ route("admin.academic-sessions.programmes.scheduling.download-courses", [$academicSession, $programme]) }}';
                window.location.href = url;
            });

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
                            previewTable.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-3"><i class="bi bi-x-circle me-1"></i> Error parsing file: ${results.errors[0].message}</td></tr>`;
                            return;
                        }

                        const data = results.data;
                        if (data.length === 0) {
                            previewTable.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-3">No data found in the file</td></tr>`;
                            return;
                        }

                        const requiredColumns = ['course_code', 'day'];
                        const missingColumns = requiredColumns.filter(col => !results.meta.fields.includes(col));
                        
                        if (missingColumns.length > 0) {
                            previewTable.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-1"></i> Missing columns: ${missingColumns.join(', ')}</td></tr>`;
                            return;
                        }

                        data.forEach((row) => {
                            const tr = document.createElement('tr');
                            tr.className = 'preview-row';
                            tr.style.borderBottom = '1px solid var(--slate-100)';
                            
                            const isValidTime = (time) => !time || /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(time);
                            const dayName = row.day ? row.day.trim() : '';
                            const dayId = Object.entries(days).find(([_, name]) => name.toLowerCase() === dayName.toLowerCase())?.[0];
                            const isValidDuration = (dur) => !dur || (!isNaN(dur) && parseInt(dur) > 0);
                            
                            const cells = [
                                row.course_code || '',
                                row.instructor_email || '',
                                dayName || '-',
                                row.morning_start || '-',
                                row.morning_duration ? `${row.morning_duration}m` : '-',
                                row.evening_start || '-',
                                row.evening_duration ? `${row.evening_duration}m` : '-',
                            ];
                            
                            cells.forEach((cell, i) => {
                                const td = document.createElement('td');
                                td.className = 'py-2 px-3';
                                td.textContent = cell;
                                
                                if (i === 2) {
                                    if (!dayName || !dayId) {
                                        td.classList.add('text-danger', 'invalid-cell');
                                        td.title = 'Invalid or missing day';
                                    }
                                } else if ((i === 3 || i === 5) && cell !== '-' && !isValidTime(cell)) {
                                    td.classList.add('invalid-cell');
                                } else if ((i === 4 || i === 6) && cell !== '-' && !isValidDuration(row[i === 4 ? 'morning_duration' : 'evening_duration'])) {
                                    td.classList.add('invalid-cell');
                                }
                                
                                tr.appendChild(td);
                            });
                            previewTable.appendChild(tr);
                        });
                        
                        document.getElementById('submitBulkSchedule').disabled = false;
                    }
                });
            });
        }
    });
</script>
@endpush
