@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">{{ $examSchedule->name }}</h1>
            <p class="text-muted mb-0">
                {{ $examSchedule->start_date->format('M d, Y') }} - {{ $examSchedule->end_date->format('M d, Y') }}
                ({{ $examSchedule->academicSession->name }})
            </p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <form action="{{ route('admin.exams.rollover', $examSchedule) }}" method="POST" onsubmit="return confirm('This will import all active course units from the academic session. Continue?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <span data-feather="repeat"></span> Rollover from Timetable
                </button>
            </form>
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <span data-feather="upload"></span> Import CSV
            </button>
            <a href="#" class="btn btn-sm btn-outline-secondary">
                <span data-feather="download"></span> Export
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Scheduled Exams</h5>
            <div class="d-flex gap-2 align-items-center">
                <form action="{{ route('admin.exams.show', $examSchedule) }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Search Course or Invigilator..." 
                           value="{{ request('search') }}" style="width: 250px;">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                    @if(request('search'))
                        <a href="{{ route('admin.exams.show', $examSchedule) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </form>
                <span class="badge bg-primary ms-2">{{ $exams->total() }} Exams</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%;">Course</th>
                            <th>Invigilator</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr id="exam-row-{{ $exam->id }}">
                                <td>
                                    <div class="fw-bold">{{ $exam->courseUnit->code }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 200px;">
                                        {{ $exam->courseUnit->name }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span id="invigilator-name-{{ $exam->id }}" class="small">
                                            {{ $exam->invigilator->name ?? 'Unassigned' }}
                                        </span>
                                        <button class="btn btn-sm btn-outline-primary btn-edit-invigilator" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#invigilatorModal"
                                            data-exam-id="{{ $exam->id }}" 
                                            data-user-id="{{ $exam->user_id }}"
                                            title="Assign Invigilator">
                                            <i class="bi bi-person-gear"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span id="date-text-{{ $exam->id }}">
                                            {{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : 'Unscheduled' }}
                                        </span>
                                        <button class="btn btn-sm btn-outline-success btn-edit-schedule" 
                                            data-exam-id="{{ $exam->id }}"
                                            data-date="{{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '' }}"
                                            data-time="{{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '' }}"
                                            data-duration="{{ $exam->duration_minutes }}"
                                            title="Edit Schedule">
                                            <i class="bi bi-calendar-event"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span id="time-text-{{ $exam->id }}">
                                        {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '--:--' }}
                                    </span>
                                </td>
                                <td>
                                    <span id="duration-text-{{ $exam->id }}">
                                        {{ $exam->duration_minutes }} min
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <p>No exams found in this schedule.</p>
                                    @if($examSchedule->academicSession)
                                    <p class="small">
                                        You can rollover courses from the 
                                        <strong>{{ $examSchedule->academicSession->name }}</strong> timetable.
                                    </p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $exams->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Simple Toast for feedback -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Update saved successfully.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">Edit Exam Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="schedule-exam-id">
                <div class="mb-3">
                    <label for="schedule-date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="schedule-date" 
                        min="{{ $examSchedule->start_date->format('Y-m-d') }}"
                        max="{{ $examSchedule->end_date->format('Y-m-d') }}">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="schedule-time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="schedule-time">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="schedule-duration" class="form-label">Duration (min)</label>
                        <input type="number" class="form-control" id="schedule-duration" min="30" step="15">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-schedule">Save Schedule</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Invigilator Modal -->
<div class="modal fade" id="invigilatorModal" tabindex="-1" aria-labelledby="invigilatorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invigilatorModalLabel">Assign Invigilator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-exam-id">
                <div class="mb-3">
                    <label for="invigilator-select" class="form-label">Search Instructor</label>
                    <select class="form-select select2-invigilator" id="invigilator-select" style="width: 100%;">
                        <!-- Options populated via AJAX -->
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-invigilator">Save Assignment</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.exams.import', $examSchedule) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Exam Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Step 1: Get Data</label>
                        <div class="d-flex gap-2">
                             <a href="{{ asset('templates/exam_import_template.csv') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-download"></i> Download Blank Template
                            </a>
                            <a href="{{ route('admin.exams.export-unscheduled', $examSchedule) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-cloud-download"></i> Download Unscheduled Exams
                            </a>
                        </div>
                        <div class="form-text">
                            You can download a list of unscheduled exams, fill in the dates/times, and upload it below.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label fw-bold">Step 2: Upload CSV</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".csv" required>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <strong>Expected Columns:</strong><br>
                        course_code, programme_code (optional), date (Y-m-d), start_time (H:i), duration (minutes)
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Fix for Select2 z-index in Bootstrap 5 Modal */
    .select2-container--open {
        z-index: 9999999 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        
        let invigilatorModal = null;
        let scheduleModal = null;
        
        const modalElement = document.getElementById('invigilatorModal');
        if(modalElement) {
             invigilatorModal = new bootstrap.Modal(modalElement);
             
             // Dynamic Select2 Initialization (Scheduling Pattern)
             modalElement.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const examId = button.getAttribute('data-exam-id');
                const userId = button.getAttribute('data-user-id');
                
                // Update hidden input
                document.getElementById('edit-exam-id').value = examId;
                
                // Initialise jQuery Select2
                const select = $('#invigilator-select');
                
                // Destroy if exists
                if (select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
                
                // Init Select2
                select.select2({
                    dropdownParent: $('#invigilatorModal'),
                    width: '100%',
                    placeholder: "Search Instructor...",
                    allowClear: true,
                    theme: 'bootstrap-5',
                    ajax: {
                        url: "{{ route('search.instructors') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return { q: params.term };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(instructor) {
                                    return {
                                        id: instructor.id,
                                        text: instructor.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
                
                // Pre-fill selection
                select.val(null).trigger('change'); // Clear first
                
                const nameSpan = document.getElementById(`invigilator-name-${examId}`);
                const currentName = nameSpan ? nameSpan.innerText.trim() : 'Unassigned';
                
                if (userId && currentName !== 'Unassigned') {
                    const option = new Option(currentName, userId, true, true);
                    select.append(option).trigger('change');
                }
             });
             
             modalElement.addEventListener('hidden.bs.modal', function() {
                const select = $('#invigilator-select');
                if (select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
             });
        }

        const scheduleModalElement = document.getElementById('scheduleModal');
        if(scheduleModalElement) {
             scheduleModal = new bootstrap.Modal(scheduleModalElement);
        }

        function updateExam(examId, data) {
            return fetch(`/admin/exams/update-slot/${examId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json());
        }

        // Handle Schedule Edit Click
        document.querySelectorAll('.btn-edit-schedule').forEach(btn => {
            btn.addEventListener('click', function() {
                const examId = this.dataset.examId;
                const date = this.dataset.date;
                const time = this.dataset.time;
                const duration = this.dataset.duration;

                document.getElementById('schedule-exam-id').value = examId;
                document.getElementById('schedule-date').value = date;
                document.getElementById('schedule-time').value = time;
                document.getElementById('schedule-duration').value = duration;

                scheduleModal.show();
            });
        });

        // Handle Save Schedule
        document.getElementById('save-schedule').addEventListener('click', function() {
            const examId = document.getElementById('schedule-exam-id').value;
            const date = document.getElementById('schedule-date').value;
            const time = document.getElementById('schedule-time').value;
            const duration = document.getElementById('schedule-duration').value;

            updateExam(examId, {
                exam_date: date,
                start_time: time,
                duration_minutes: duration
            })
            .then(data => {
                if (data.success) {
                    toast.show();
                    
                    // Update UI text
                    document.getElementById(`date-text-${examId}`).innerText = date ? date : 'Unscheduled';
                    document.getElementById(`time-text-${examId}`).innerText = time ? time : '--:--';
                    document.getElementById(`duration-text-${examId}`).innerText = duration + ' min';

                    // Update data attributes on the button for next open
                    const btn = document.querySelector(`.btn-edit-schedule[data-exam-id="${examId}"]`);
                    if(btn) {
                        btn.dataset.date = date;
                        btn.dataset.time = time;
                        btn.dataset.duration = duration;
                    }

                    scheduleModal.hide();
                } else {
                    alert('Error updating schedule.');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Handle Save Invigilator
        document.getElementById('save-invigilator').addEventListener('click', function() {
            const examId = document.getElementById('edit-exam-id').value;
            
            // Get value from Select2
            let userId = '';
            let userName = 'Unassigned';
            
            const select = $('#invigilator-select');
            const data = select.select2('data');
            
            if (data && data.length > 0) {
                userId = data[0].id;
                userName = data[0].text;
            }

            updateExam(examId, { user_id: userId })
            .then(data => {
                if (data.success) {
                    toast.show();
                    
                    // Update UI text
                    const nameSpan = document.getElementById(`invigilator-name-${examId}`);
                    if (nameSpan) nameSpan.innerText = userId ? userName : 'Unassigned';
                    
                    // Update data-attribute for next edit
                    const editBtn = document.querySelector(`.btn-edit-invigilator[data-exam-id="${examId}"]`);
                    if(editBtn) editBtn.setAttribute('data-user-id', userId);
                    
                    invigilatorModal.hide();
                } else {
                    alert('Error updating invigilator.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endpush
