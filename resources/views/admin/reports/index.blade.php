@extends('layouts.app')

@section('title', 'Reports')

@push('styles')
<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 0.5rem;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card .bi {
        opacity: 0.7;
    }
    .btn {
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reports Dashboard</h1>
    </div>

    <div class="row">
        <!-- Instructor Schedules Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Instructor Schedules</div>
                            <p class="mb-0 text-muted">View teaching schedules by instructor</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-video2 fa-3x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.instructor-schedules') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-search me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Distribution Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Workload Distribution</div>
                            <p class="mb-0 text-muted">View staff workload distribution across  schools</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-3x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.workload-distribution') }}" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-bar-chart-line me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Schedules Card -->
        @include('admin.reports.class-schedules-card')

<!-- Lecturer Conflicts Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Lecturer Conflicts</div>
                            <p class="mb-0 text-muted">View lecturers with overlapping schedules</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-x fa-3x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.lecturer-conflicts') }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-search me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Conflicts Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Exam Conflicts</div>
                            <p class="mb-0 text-muted">Detect student and invigilator scheduling conflicts in exams</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-3x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.exam-conflicts') }}" class="btn btn-sm btn-danger">
                            <i class="bi bi-search me-1"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Utilization Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Room Utilization</div>
                            <p class="mb-0 text-muted">Analyze classroom and facility usage</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-building fa-3x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="btn btn-sm btn-secondary" disabled>
                            <i class="bi bi-clock me-1"></i> Coming Soon
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript functionality here if needed
    document.addEventListener('DOMContentLoaded', function() {
        // You can add interactive functionality here
    });
</script>
@endpush
