@extends('layouts.app')

@section('title', "Programme: $programme->name")

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">{{ $programme->name }} - Course Units & Instructors</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif



        {{-- Group courses by Year and Semester --}}
        @php
            $groupedCourses = $programme->courseUnits->groupBy(['yearOfStudy.name', 'semester.name']);
        @endphp

        @foreach ($groupedCourses as $year => $semesters)
            @foreach ($semesters as $semester => $courses)
                <div class="mt-5">
                    <h3 class="text-primary">Year {{ $year }} - Semester {{ $semester }}</h3>
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Unit Name</th>
                                <th>Instructor</th>
                                <th>Scheduled Time Slot</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $courseUnit)
                                @php
                                    $lessonSlot = $courseUnit
                                        ->lessonSlots()
                                        ->where('programme_id', $programme->id)
                                        ->first();
                                @endphp
                                <tr>
                                    <td>{{ $courseUnit->code }}</td>
                                    <td>{{ $courseUnit->name }}</td>
                                    <td>
                                        @php
                                            $assignedInstructor = $courseUnit
                                                ->instructors()
                                                ->wherePivot('programme_id', $programme->id)
                                                ->first();
                                        @endphp
                                        @if ($assignedInstructor)
                                            {{ $assignedInstructor->title->name ?? '' }} {{ $assignedInstructor->name }}
                                        @else
                                            <span class="text-muted">No Instructor Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lessonSlot)
                                            <span class="badge bg-info">
                                                {{ $lessonSlot->day->name }} -
                                                {{ date('h:i A', strtotime($lessonSlot->start_time)) }}
                                                ({{ $lessonSlot->duration }} min)
                                            </span>
                                        @else
                                            <span class="text-muted">No Scheduled Slot</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Remove Course Button --}}
                                        <form
                                            action="{{ route('programmes.remove_course_unit', [$programme, $courseUnit]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">Drop Course</button>
                                        </form>

                                        {{-- Assign/Unassign Instructor --}}
                                        @if (!$assignedInstructor)
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#assignInstructorModal{{ $courseUnit->id }}">Assign
                                                Instructor</button>
                                        @else
                                            <form
                                                action="{{ route('programmes.remove_instructor', [$programme, $courseUnit, $assignedInstructor]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-warning btn-sm">Unassign
                                                    Instructor</button>
                                            </form>
                                        @endif

                                        {{-- Assign or Edit Slot Button --}}
                                        @if (!$lessonSlot)
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#assignSlotModal{{ $courseUnit->id }}">
                                                Assign Slot
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editSlotModal{{ $courseUnit->id }}">
                                                Edit Slot
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Assign Instructor Modal -->
                                <div class="modal fade" id="assignInstructorModal{{ $courseUnit->id }}" tabindex="-1"
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
                                                    <label for="lecturer_id_{{ $courseUnit->id }}"
                                                        class="form-label">Select Instructor</label>
                                                    <select class="form-control instructor-select"
                                                        id="lecturer_id_{{ $courseUnit->id }}" name="lecturer_id" required>
                                                        <option value="">Search Instructor...</option>
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
                                <div class="modal fade" id="assignSlotModal{{ $courseUnit->id }}" tabindex="-1"
                                    aria-labelledby="assignSlotLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="assignSlotLabel">Assign Lesson Slot</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form
                                                action="{{ route('lesson_slots.store', ['programme' => $programme->id, 'courseUnit' => $courseUnit->id]) }}"
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
                                                    <label for="start_time" class="mt-3 form-label">Start Time</label>
                                                    <input type="time" class="form-control" name="start_time" required>
                                                    <label for="duration" class="mt-3 form-label">Duration
                                                        (Minutes)</label>
                                                    <input type="number" class="form-control" name="duration"
                                                        min="1" required>
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
                                @if ($lessonSlot)
                                    <div class="modal fade" id="editSlotModal{{ $courseUnit->id }}" tabindex="-1"
                                        aria-labelledby="editSlotLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editSlotLabel">Edit Lesson Slot</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form
                                                    action="{{ route('lesson_slots.update', ['programme' => $programme->id, 'courseUnit' => $courseUnit->id, 'lessonSlot' => $lessonSlot->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <label for="day_id" class="form-label">Select Day</label>
                                                        <select class="form-control" name="day_id" required>
                                                            @foreach ($days as $day)
                                                                <option value="{{ $day->id }}"
                                                                    {{ $lessonSlot->day_id == $day->id ? 'selected' : '' }}>
                                                                    {{ $day->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <label for="start_time" class="mt-3 form-label">Start Time</label>
                                                        <input type="time" class="form-control" name="start_time"
                                                            value="{{ $lessonSlot->start_time }}" required>
                                                        <label for="duration" class="mt-3 form-label">Duration
                                                            (Minutes)</label>
                                                        <input type="number" class="form-control" name="duration"
                                                            value="{{ $lessonSlot->duration }}" min="1" required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Update
                                                            Slot</button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endforeach
    </div>
@endsection
