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
                                        {{ $instructor->title ? $instructor->title->abbreviation . ' ' : '' }}{{ $instructor->name }}
                                    @else
                                        <span class="text-muted">No Instructor Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($mapping->morning_start_time || $mapping->evening_start_time)
                                        <span class="badge bg-info">
                                            {{ $mapping->day->name ?? 'N/A' }}
                                            @if ($mapping->morning_start_time)
                                                -
                                                {{ \Carbon\Carbon::parse($mapping->morning_start_time)->format('h:i A') }}
                                                ({{ $mapping->morning_duration }} min)
                                            @endif
                                            @if ($mapping->evening_start_time)
                                                /
                                                {{ \Carbon\Carbon::parse($mapping->evening_start_time)->format('h:i A') }}
                                                ({{ $mapping->evening_duration }} min)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">No Slot Assigned</span>
                                    @endif
                                </td>

                                <td>
                                    {{-- Assign/Unassign Instructor --}}
                                    @if (!$instructor)
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#assignInstructorModal{{ $course->id }}">Assign
                                            Instructor</button>
                                    @else
                                        @php
                                            // Debug information
                                            // dd($mapping);
                                        @endphp
                                        {{-- <form
                                            action="{{ route('admin.programmes.remove_instructor', [
                                                'programme' => $programme->id, 
                                                'courseUnit' => $course->id, 
                                                'user' => $mapping->user_id
                                            ]) }}"
                                            method="POST" 
                                            class="d-inline"
                                        >
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-warning btn-sm">Unassign</button>
                                        </form> --}}
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
                        @endforeach
                    </tbody>
                </table>
                {{-- All modals after table --}}
                @foreach ($groupedMappings as $group => $mappings)
                    @foreach ($mappings as $mapping)
                        @php $course = $mapping->courseUnit; @endphp
                        @include('partials.modals.assign-instructor')
                        @include('partials.modals.assign-slot')
                        @include('partials.modals.edit-slot')
                    @endforeach
                @endforeach
            </div>
        @endforeach

        <a href="{{ route('admin.programmes.index') }}" class="mb-3 btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Programmes
        </a>

        @php
            // Get the current academic session ID or use a default value
            $academicSessionId = session('current_academic_session_id') ?? 1;
            $academicSession = \App\Models\AcademicSession::find($academicSessionId);
        @endphp
        <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', [$academicSession, $programme]) }}" class="mb-3 btn btn-outline-primary float-end">
            <i class="bi bi-calendar-plus"></i> Manage Schedule
        </a>
    </div>
@endsection
