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

        {{-- Group courses by Year and Semester - Using groupedMappings from controller --}}
        @foreach ($groupedMappings as $groupName => $mappings)
            <div class="mt-5">
                <h4 class="text-primary">{{ $groupName }}</h4>
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
                                $courseUnit = $mapping->courseUnit;
                                $instructor = $mapping->instructor;
                                // Check if any slot is assigned
                                $hasSlot = $mapping->morning_start_time || $mapping->evening_start_time;
                            @endphp
                            <tr>
                                <td>{{ $courseUnit->code }}</td>
                                <td>{{ $courseUnit->name }}</td>
                                <td>
                                    @if ($instructor)
                                        {{ $instructor->title->abbreviation ?? '' }} {{ $instructor->name }}
                                    @else
                                        <span class="text-muted">No Instructor Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($hasSlot)
                                        <span class="badge bg-info">
                                            {{ $mapping->day->name ?? 'N/A' }}
                                            @if ($mapping->morning_start_time)
                                                - {{ \Carbon\Carbon::parse($mapping->morning_start_time)->format('h:i A') }} ({{ $mapping->morning_duration }} min)
                                            @endif
                                            @if ($mapping->evening_start_time)
                                                / {{ \Carbon\Carbon::parse($mapping->evening_start_time)->format('h:i A') }} ({{ $mapping->evening_duration }} min)
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
                                            data-bs-target="#assignInstructorModal{{ $mapping->id }}">Assign
                                            Instructor</button>
                                    @else
                                        <form
                                            action="{{ route('programmes.remove_instructor', [$programme, $courseUnit, $instructor]) }}"
                                            method="POST" 
                                            class="d-inline"
                                        >
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-warning btn-sm">Unassign</button>
                                        </form>
                                    @endif

                                    {{-- Assign/Edit Slot --}}
                                    @if (!$hasSlot)
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

                            {{-- Modals for each mapping --}}
                            
                            <!-- Assign Instructor Modal -->
                            <div class="modal fade" id="assignInstructorModal{{ $mapping->id }}" tabindex="-1"
                                aria-labelledby="assignInstructorLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="assignInstructorLabel">Assign Instructor to
                                                {{ $courseUnit->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form
                                            action="{{ route('programmes.add_instructor', [$programme, $courseUnit]) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <label for="user_id_{{ $mapping->id }}"
                                                    class="form-label">Select Instructor</label>
                                                <select class="form-control" name="user_id" required>
                                                    <option value="">Search Instructor...</option>
                                                    @foreach($instructors as $inst)
                                                        <option value="{{ $inst->id }}">{{ $inst->title->abbreviation ?? '' }} {{ $inst->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Assign</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Assign Slot Modal --}}
                            <div class="modal fade" id="assignSlotModal{{ $mapping->id }}" tabindex="-1"
                                aria-labelledby="assignSlotLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="assignSlotLabel">Assign Lesson Slot</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form
                                            action="{{ route('admin.lesson_slots.store', ['programme' => $programme->id, 'courseUnit' => $courseUnit->id]) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <label for="day_id" class="form-label">Select Day</label>
                                                <select class="form-control" name="day_id" required>
                                                    @foreach ($days as $day)
                                                        <option value="{{ $day->id }}">{{ $day->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                
                                                <hr class="my-3">
                                                <h6 class="text-primary">Morning Session</h6>
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control" name="morning_start_time">
                                                <label class="mt-2 form-label">Duration (Minutes)</label>
                                                <input type="number" class="form-control" name="morning_duration" min="1">
                                                
                                                <hr class="my-3">
                                                <h6 class="text-primary">Evening Session</h6>
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control" name="evening_start_time">
                                                <label class="mt-2 form-label">Duration (Minutes)</label>
                                                <input type="number" class="form-control" name="evening_duration" min="1">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Assign Slot</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit Slot Modal --}}
                            <div class="modal fade" id="editSlotModal{{ $mapping->id }}" tabindex="-1"
                                aria-labelledby="editSlotLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editSlotLabel">Edit Lesson Slot</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form
                                            action="{{ route('admin.lesson_slots.update', ['programme' => $programme->id, 'courseUnit' => $courseUnit->id, 'lessonSlot' => $mapping->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <label for="day_id" class="form-label">Select Day</label>
                                                <select class="form-control" name="day_id" required>
                                                    @foreach ($days as $day)
                                                        <option value="{{ $day->id }}"
                                                            {{ $mapping->day_id == $day->id ? 'selected' : '' }}>
                                                            {{ $day->name }}</option>
                                                    @endforeach
                                                </select>
                                                
                                                <hr class="my-3">
                                                <h6 class="text-primary">Morning Session</h6>
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control" name="morning_start_time"
                                                    value="{{ $mapping->morning_start_time ? \Carbon\Carbon::parse($mapping->morning_start_time)->format('H:i') : '' }}">
                                                <label class="mt-2 form-label">Duration (Minutes)</label>
                                                <input type="number" class="form-control" name="morning_duration"
                                                    value="{{ $mapping->morning_duration }}" min="1">
                                                    
                                                <hr class="my-3">
                                                <h6 class="text-primary">Evening Session</h6>
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control" name="evening_start_time"
                                                    value="{{ $mapping->evening_start_time ? \Carbon\Carbon::parse($mapping->evening_start_time)->format('H:i') : '' }}">
                                                <label class="mt-2 form-label">Duration (Minutes)</label>
                                                <input type="number" class="form-control" name="evening_duration"
                                                    value="{{ $mapping->evening_duration }}" min="1">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Update Slot</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="mt-4">
            <a href="{{ route('admin.programmes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Programmes
            </a>

            @php
                $academicSessionId = session('current_academic_session_id') ?? 1;
                $academicSession = \App\Models\AcademicSession::find($academicSessionId);
            @endphp
            @if($academicSession)
                <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', [$academicSession, $programme]) }}" class="btn btn-outline-primary float-end">
                    <i class="bi bi-calendar-plus"></i> Manage Detailed Schedule
                </a>
            @endif
        </div>
    </div>
@endsection
