@extends('layouts.app')

@section('title', 'Student Timetable')

@section('content')
    <style>
        .lesson-container {
            display: flex;
            flex-direction: column;
            align-items: left;
            /* Centers content */
            text-align: left;
            gap: 8px;
        }

        .instructor-img-container {
            margin-bottom: 5px;
            /* Moves image slightly up */
        }

        .instructor-image {
            width: 80px;
            /* Adjust size */
            height: 80px;
            object-fit: cover;
            /* Ensures the image fully fills the space */
            border-radius: 50%;
            /* Makes the image circular */
        }

        .lesson-details {
            width: 100%;
        }

        .instructor-name {
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .course-code {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: bold;
        }

        .course-title {
            font-style: italic;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
        }

        .mode {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
        }

        .session {
            font-size: 14px;
            font-weight: bold;
            color: white;
            margin-top: 5px;
        }
    </style>


    <div class="container mt-5">
        <h2 class="mb-4">Student Timetable</h2>
        <form method="GET" action="{{ route('timetable.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="school_id" class="form-label">School</label>
                    <select class="form-control" id="school_id" name="school_id" required>
                        <option value="">Select School</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="programme_id" class="form-label">Programme</label>
                    <select class="form-control" id="programme_id" name="programme_id" required>
                        <option value="">Select Programme</option>
                        @foreach ($programmes as $programme)
                            <option value="{{ $programme->id }}"
                                {{ request('programme_id') == $programme->id ? 'selected' : '' }}>
                                {{ $programme->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="year_of_study_id" class="form-label">Year of Study</label>
                    <select class="form-control" id="year_of_study_id" name="year_of_study_id" required>
                        <option value="">Select Year</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}"
                                {{ request('year_of_study_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="semester_id" class="form-label">Semester</label>
                    <select class="form-control" id="semester_id" name="semester_id" required>
                        <option value="">Select Semester</option>
                        @foreach ($semesters as $semester)
                            <option value="{{ $semester->id }}"
                                {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="mt-3 btn btn-primary">View Timetable</button>
        </form>

        @if ($timetable->isNotEmpty())
            <div class="mt-5">
                <h3>Class Schedule</h3>
                <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Time (EAT)</th>
                            @foreach ($days as $day)
                                <th style="width: 18%;">{{ $day->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (range(8 * 60, 20 * 60 - 30, 30) as $minute)
                            @php
                                $hour = intdiv($minute, 60);
                                $min = $minute % 60;
                                $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : ''; // Show full hours only
                            @endphp
                            <tr style="height: 40px;">
                                <td>{{ $timeLabel }}</td>
                                @foreach ($days as $day)
                                    @php
                                        $lesson = $timetable
                                            ->where('day_id', $day->id)
                                            ->first(function ($lesson) use ($minute) {
                                                $lessonStart =
                                                    (int) date('H', strtotime($lesson->start_time)) * 60 +
                                                    (int) date('i', strtotime($lesson->start_time));
                                                $lessonEnd = $lessonStart + $lesson->duration;
                                                return $minute >= $lessonStart && $minute < $lessonEnd;
                                            });
                                    @endphp

                                    @if (
                                        $lesson &&
                                            (int) date('H', strtotime($lesson->start_time)) * 60 + (int) date('i', strtotime($lesson->start_time)) ==
                                                $minute)
                                        <td rowspan="{{ ceil($lesson->duration / 30) }}"
                                            style="background-color: {{ $lesson->courseUnit ? $lesson->courseUnit->color : '#ff7f50' }}; color: white; vertical-align: middle; padding: 10px;">
                                            <div class="lesson-container">
                                                <div class="instructor-img-container">
                                                    <img class="instructor-image"
                                                        src="{{ optional($lesson->courseUnit->instructors->first())->image_path
                                                            ? asset('storage/' . optional($lesson->courseUnit->instructors->first())->image_path)
                                                            : 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png' }}"
                                                        alt="Instructor Image">
                                                </div>
                                                <div class="lesson-details">
                                                    <p class="instructor-name">
                                                        {{ $lesson->courseUnit->instructors->first()->title->name ?? '' }}
                                                        {{ $lesson->courseUnit->instructors->first()->name ?? '' }}
                                                    </p>
                                                    <div class="course-code">
                                                        {{ $lesson->courseUnit->code }}:
                                                        <span class="course-title">
                                                            {{ $lesson->courseUnit->name }}
                                                        </span>
                                                    </div>
                                                    <p class="mode">Mode: Synchronous online</p>
                                                    <div class="session">
                                                        {{ $lesson->session }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @elseif (!$lesson)
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
