@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Exam Conflict Report</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Reports
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.reports.exam-conflicts') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="exam_schedule_id" class="form-label">Exam Schedule</label>
                    <select name="exam_schedule_id" id="exam_schedule_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Select Schedule --</option>
                        @foreach($examSchedules as $schedule)
                            <option value="{{ $schedule->id }}" {{ (isset($activeSchedule) && $activeSchedule->id == $schedule->id) ? 'selected' : '' }}>
                                {{ $schedule->name }} ({{ $schedule->start_date ? $schedule->start_date->format('M Y') : 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($activeSchedule)
        @if($conflicts->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i> Found {{ $conflicts->count() }} Conflict(s)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Conflict Detail</th>
                                    <th>Exam A</th>
                                    <th>Exam B</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conflicts as $conflict)
                                    <tr>
                                        <td class="fw-bold">{{ $conflict->date->format('D, d M Y') }}</td>
                                        <td>
                                            @if($conflict->type == 'Invigilator Conflict')
                                                <span class="badge bg-warning text-dark">{{ $conflict->type }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $conflict->type }}</span>
                                            @endif
                                        </td>
                                        <td class="text-danger fw-bold">{{ $conflict->detail }}</td>
                                        <td>
                                            <div class="small fw-bold">{{ $conflict->time_a }}</div>
                                            <div>{{ $conflict->exam_a->courseUnit->code }}</div>
                                            <small class="text-muted">{{ $conflict->exam_a->courseUnit->name }}</small>
                                        </td>
                                        <td>
                                            <div class="small fw-bold">{{ $conflict->time_b }}</div>
                                            <div>{{ $conflict->exam_b->courseUnit->code }}</div>
                                            <small class="text-muted">{{ $conflict->exam_b->courseUnit->name }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                <div>
                    No conflicts detected in <strong>{{ $activeSchedule->name }}</strong>. Great job!
                </div>
            </div>
        @endif
    @else
        <div class="alert alert-info">
            Please select an exam schedule to view report.
        </div>
    @endif
</div>
@endsection
