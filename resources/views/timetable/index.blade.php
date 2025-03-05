<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timetable</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        /* .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        } */

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            color: #037b90;
            font-size: 28px;
            font-weight: bold;
        }

        .filter-section {
            padding: 20px;
            background: #e3f2fd;
            border-radius: 10px;
            margin-bottom: 20px;
        }

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
</head>

<body>

    <div class="container mt-5">
        {{-- <h2 class="page-header">Student Timetable</h2> --}}

        <!-- Filtering Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('timetable.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="school_id" class="form-label">School</label>
                        <select class="form-control" id="school_id" name="school_id" required>
                            <option value="">Select School</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}"
                                    {{ request('school_id') == $school->id ? 'selected' : '' }}>
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

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">View Timetable</button>
                    <a href="{{ route('timetable.export.pdf', request()->all()) }}" class="btn btn-danger">Export as
                        PDF</a>
                </div>

            </form>
        </div>

        <!-- Timetable Display -->
        @if ($timetable->isNotEmpty())
            <div class="mt-5">
                <h3>Teaching & Learning Schedule</h3>
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

</body>

</html>
