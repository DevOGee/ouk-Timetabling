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
            <span class="badge bg-primary">{{ $exams->count() }} Exams</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th style="width: 25%;">Course</th>
                            <th>Invigilator</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr id="exam-row-{{ $exam->id }}">
                                <td>
                                    <input type="date" class="form-control form-control-sm exam-input" name="exam_date"
                                        data-exam-id="{{ $exam->id }}"
                                        value="{{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '' }}"
                                        min="{{ $examSchedule->start_date->format('Y-m-d') }}"
                                        max="{{ $examSchedule->end_date->format('Y-m-d') }}">
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm exam-input" name="start_time"
                                        data-exam-id="{{ $exam->id }}"
                                        value="{{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '' }}">
                                </td>
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
                                        <button class="btn btn-sm btn-light btn-edit-invigilator" 
                                            data-exam-id="{{ $exam->id }}" 
                                            data-user-id="{{ $exam->user_id }}"
                                            title="Assign Invigilator">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control exam-input" name="duration_minutes"
                                            data-exam-id="{{ $exam->id }}"
                                            value="{{ $exam->duration_minutes }}" 
                                            min="30" step="15" style="max-width: 70px;">
                                        <span class="input-group-text">min</span>
                                    </div>
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
                        <label for="file" class="form-label">CSV File</label>
                        <input class="form-control" type="file" id="file" name="file" required accept=".csv,.xlsx">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        
        let invigilatorModal = null;
        const modalElement = document.getElementById('invigilatorModal');
        if(modalElement) {
             invigilatorModal = new bootstrap.Modal(modalElement);
        }

        // Initialize Select2 in Modal with AJAX
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2-invigilator').select2({
                placeholder: "Search Instructor...",
                allowClear: true,
                width: '100%',
                dropdownParent: jQuery('#invigilatorModal'),
                ajax: {
                    url: "{{ route('search.instructors') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return {
                            results: jQuery.map(data, function(instructor) {
                                return {
                                    id: instructor.id,
                                    text: instructor.name // Standardizing on 'name'
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
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

        // Handle Edit Invigilator Click
        document.querySelectorAll('.btn-edit-invigilator').forEach(btn => {
            btn.addEventListener('click', function() {
                const examId = this.dataset.examId;
                const userId = this.dataset.userId;
                
                // Get current name to pre-fill
                const nameSpan = document.getElementById(`invigilator-name-${examId}`);
                const currentName = nameSpan ? nameSpan.innerText.trim() : 'Unassigned';
                
                document.getElementById('edit-exam-id').value = examId;
                
                if (typeof jQuery !== 'undefined') {
                    const select = jQuery('#invigilator-select');
                    
                    // Clear previous selection
                    select.val(null).empty();
                    
                    // Pre-fill if assigned
                    if (userId && currentName !== 'Unassigned') {
                        const option = new Option(currentName, userId, true, true);
                        select.append(option).trigger('change');
                    }
                }
                
                invigilatorModal.show();
            });
        });

        // Handle Save Invigilator
        document.getElementById('save-invigilator').addEventListener('click', function() {
            const examId = document.getElementById('edit-exam-id').value;
            
            // Get value from Select2
            let userId = '';
            let userName = 'Unassigned';
            
            if (typeof jQuery !== 'undefined') {
                const data = jQuery('#invigilator-select').select2('data');
                if (data && data.length > 0) {
                    userId = data[0].id;
                    userName = data[0].text;
                }
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
                    if(editBtn) editBtn.dataset.userId = userId;
                    
                    invigilatorModal.hide();
                } else {
                    alert('Error updating invigilator.');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        function handleInputUpdate(inputElement) {
            const examId = inputElement.dataset.examId;
            const row = document.getElementById(`exam-row-${examId}`);
            
            const date = row.querySelector('input[name="exam_date"]').value;
            const time = row.querySelector('input[name="start_time"]').value;
            const duration = row.querySelector('input[name="duration_minutes"]').value;

            updateExam(examId, {
                exam_date: date,
                start_time: time,
                duration_minutes: duration
            })
            .then(data => {
                if (data.success) {
                    toast.show();
                } else {
                    alert('Error updating exam.');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Attach listeners to native inputs (Date, Time, Duration)
        document.querySelectorAll('.exam-input').forEach(input => {
            input.addEventListener('change', function() {
                handleInputUpdate(this);
            });
        });
    });
</script>
@endsection
