@extends('layouts.app')

@section('title', 'My Timetable')

@push('styles')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .timetable-slot {
        height: 100%;
        padding: 8px;
        border-radius: 4px;
        color: white;
        font-size: 0.8rem;
        overflow: hidden;
    }
    .lesson-container {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .course-title {
        font-weight: 600;
        margin-bottom: 4px;
    }
    .instructor-info {
        font-size: 0.75rem;
        margin-bottom: 2px;
    }
    .time-info {
        font-size: 0.7rem;
        opacity: 0.9;
    }
    .programme-badge {
        font-size: 0.7rem;
        opacity: 0.9;
        margin-top: 4px;
        display: inline-block;
        padding: 2px 6px;
        border-radius: 3px;
        background-color: rgba(255, 255, 255, 0.2);
    }
    .timetable th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
    }
    .timetable td {
        vertical-align: middle;
        padding: 0;
        height: 60px;
    }
    .time-col {
        background-color: #f8f9fa;
        font-weight: 500;
        width: 80px;
    }
    @media (max-width: 768px) {
        .timetable-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
            background: #f8f9fa;
            padding: 5px 0;
        }
        .timetable {
            min-width: 650px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-calendar-week me-2 text-primary"></i>My Timetable
        </h1>
        
        @if($activeSession)
            <div class="d-flex align-items-center">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 me-3">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $activeSession->name }}
                </span>
            </div>
        @endif
    </div>

    @if(!$activeSession)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            No active academic session found. Please contact the administrator.
        </div>
    @elseif($timetable->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i>
            No timetable data found for the current academic session.
        </div>
    @else
        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
                @include('instructor.partials._timetable', ['timetable' => $timetable, 'days' => $days])
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-info-circle me-2"></i>Legend
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Course Information</h6>
                        <ul class="list-unstyled">
                            <li><span class="font-weight-bold">Top Line:</span> Course Code & Name</li>
                            <li><span class="font-weight-bold">Middle Line:</span> Programme & Venue</li>
                            <li><span class="font-weight-bold">Bottom Line:</span> Time Slot</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Color Coding</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($timetable->groupBy('course_unit_id') as $courseId => $slots)
                                @php
                                    $course = $slots->first()->courseUnit;
                                    $color = $course->color ?? '#' . substr(md5($course->code), 0, 6);
                                @endphp
                                <div class="d-flex align-items-center mb-2">
                                    <span class="color-swatch me-2" style="background-color: {{ $color }}; width: 16px; height: 16px; border-radius: 3px; display: inline-block;"></span>
                                    <small>{{ $course->code }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
