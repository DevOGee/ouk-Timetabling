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
                            <th>Course Unit</th>
                            <th>Programme</th>
                            <th>Date</th>
                            <th>Time & Duration</th>
                            <th>Invigilator</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr id="exam-row-{{ $exam->id }}">
                                <td>
                                    <div class="fw-bold">{{ $exam->courseUnit->code }}</div>
                                    <div class="small text-muted">{{ $exam->courseUnit->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $exam->mapping->programme->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td style="min-width: 160px;">
                                    <input type="date" 
                                        class="form-control form-control-sm exam-date-input" 
                                        data-exam-id="{{ $exam->id }}"
                                        value="{{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '' }}"
                                        min="{{ $examSchedule->start_date->format('Y-m-d') }}"
                                        max="{{ $examSchedule->end_date->format('Y-m-d') }}">
                                </td>
                                <td style="min-width: 200px;">
                                    <div class="input-group input-group-sm">
                                        <input type="time" 
                                            class="form-control exam-time-input" 
                                            data-exam-id="{{ $exam->id }}"
                                            value="{{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '' }}">
                                        <input type="number" 
                                            class="form-control exam-duration-input" 
                                            data-exam-id="{{ $exam->id }}"
                                            value="{{ $exam->duration_minutes }}"
                                            min="30" step="15"
                                            title="Duration in minutes">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">{{ $exam->invigilator->name ?? 'Unassigned' }}</div>
                                </td>
                                <td>
                                    <span class="status-indicator" id="status-{{ $exam->id }}">
                                        @if($exam->exam_date && $exam->start_time)
                                            <span class="badge bg-success">Scheduled</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <p>No exams found.</p>
                                    <p class="small">Use the "Rollover" button to import courses from the timetable.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="d-flex justify-content-center">
            {!! $exams->links() !!}
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

        function updateExam(examId, data) {
            const row = document.getElementById(`exam-row-${examId}`);
            
            fetch(`/admin/exams/update-slot/${examId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toast.show();
                    // Update status badge if scheduled
                    const statusCell = document.getElementById(`status-${examId}`);
                    // Simple logic: if we have date and time, it's scheduled
                    const dateVal = row.querySelector('.exam-date-input').value;
                    const timeVal = row.querySelector('.exam-time-input').value;
                    
                    if (dateVal && timeVal) {
                        statusCell.innerHTML = '<span class="badge bg-success">Scheduled</span>';
                    }
                } else {
                    alert('Error updating exam.');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Attach listeners to inputs
        document.querySelectorAll('.exam-date-input, .exam-time-input, .exam-duration-input').forEach(input => {
            input.addEventListener('change', function() {
                const examId = this.dataset.examId;
                const row = document.getElementById(`exam-row-${examId}`);
                
                const date = row.querySelector('.exam-date-input').value;
                const time = row.querySelector('.exam-time-input').value;
                const duration = row.querySelector('.exam-duration-input').value;

                updateExam(examId, {
                    exam_date: date,
                    start_time: time,
                    duration_minutes: duration
                });
            });
        });
    });
</script>
@endsection
