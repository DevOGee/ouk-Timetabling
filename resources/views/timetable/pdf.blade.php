<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Timetable - {{ $programme->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .sub-header {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .timetable {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .timetable th,
        .timetable td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            height: 28px;
            vertical-align: middle;
            word-wrap: break-word;
            white-space: normal;
        }

        .timetable th {
            background-color: #037b90;
            color: white;
            font-size: 12px;
        }

        .lesson {
            background-color: #ff7f50;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        .course-title {
            font-style: italic;
            font-size: 9px;
            color: rgba(255, 255, 255, 0.8);
            word-wrap: break-word;
        }

        .mode {
            font-size: 9px;
            color: white;
        }

        .session {
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .logo {
            max-width: 100px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <!-- Logo in Header -->
    <div class="header">
        <img src="{{ public_path('ouk-logo.png') }}" style="width: 200px;" alt="OUK Logo">
    </div>

    <h2 class="header">Teaching & Learning Schedule for {{ $programme->name }}</h2>
    <p class="sub-header">{{ $programme->programme_code }} - {{ $programme->name }}</p>

    <h3>Teaching & Learning Schedule for {{ $programme->name }}</h3>

    @if ($timetable->isNotEmpty())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Day</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timetable as $lesson)
                    <tr>
                        <td>{{ $lesson->courseUnit->code }}</td>
                        <td>{{ $lesson->courseUnit->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($lesson->morning_start_time ?? $lesson->evening_start_time)->format('h:i A') }}
                        </td>
                        <td>
                            @if ($lesson->morning_start_time)
                                {{ \Carbon\Carbon::parse($lesson->morning_start_time)->addMinutes($lesson->morning_duration)->format('h:i A') }}
                            @elseif ($lesson->evening_start_time)
                                {{ \Carbon\Carbon::parse($lesson->evening_start_time)->addMinutes($lesson->evening_duration)->format('h:i A') }}
                            @endif
                        </td>
                        <td>{{ $lesson->day->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No timetable data available for the selected filters.</p>
    @endif



    @if ($timetable->isNotEmpty())
        <table class="timetable">
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
                        $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : '';
                    @endphp
                    <tr>
                        <td>{{ $timeLabel }}</td>
                        @foreach ($days as $day)
                            @php
                                // Retrieve the lesson for the specific day and time slot
                                $lesson = $timetable->first(function ($lesson) use ($day, $minute) {
                                    $lessonStart =
                                        \Carbon\Carbon::parse($lesson->start_time)->hour * 60 +
                                        \Carbon\Carbon::parse($lesson->start_time)->minute;
                                    $lessonEnd = $lessonStart + $lesson->duration;

                                    // Check if the lesson's time overlaps with the current time slot
                                    return $lesson->day_id === $day->id &&
                                        $minute >= $lessonStart &&
                                        $minute < $lessonEnd;
                                });
                            @endphp

                            @if ($lesson)
                                <td rowspan="{{ ceil($lesson->duration / 30) }}" class="lesson">
                                    <p><strong>{{ $lesson->courseUnit->instructors->first()->name ?? 'N/A' }}</strong>
                                    </p>
                                    <p>{{ $lesson->courseUnit->code }}</p>
                                    <p class="course-title">{{ $lesson->courseUnit->name }}</p>
                                    <p class="mode">Mode: Synchronous online</p>
                                    <p class="session">{{ $lesson->session ?? 'N/A' }}</p>
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif


    <table class="timetable">
        <thead>
            <tr>
                <th>Time (EAT)</th>
                @foreach ($groupedLessons as $dayId => $lessons)
                    <th>{{ $lessons->first()->day->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach (range(8 * 60, 20 * 60 - 30, 30) as $minute)
                @php
                    $hour = intdiv($minute, 60);
                    $min = $minute % 60;
                    $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : ''; // Display full hours only
                @endphp
                <tr>
                    <td>{{ $timeLabel }}</td>
                    @foreach ($groupedLessons as $dayId => $lessons)
                        @php
                            $lesson = $lessons->first(function ($lesson) use ($minute) {
                                $lessonStart =
                                    \Carbon\Carbon::parse($lesson->start_time)->hour * 60 +
                                    \Carbon\Carbon::parse($lesson->start_time)->minute;
                                $lessonEnd = $lessonStart + $lesson->duration;
                                return $minute >= $lessonStart && $minute < $lessonEnd;
                            });
                        @endphp

                        @if ($lesson)
                            <td rowspan="{{ ceil($lesson->duration / 30) }}" class="lesson">
                                <strong>{{ $lesson->courseUnit->code }} - {{ $lesson->courseUnit->name }}</strong>
                                <br>
                                <span class="course-title">{{ optional($lesson->lecturer)->name }}</span>
                                <br>
                                {{ \Carbon\Carbon::parse($lesson->start_time)->format('g:i A') }} -
                                {{ \Carbon\Carbon::parse($lesson->start_time)->addMinutes($lesson->duration)->format('g:i A') }}
                            </td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>


    @if ($timetable->isNotEmpty())
        <table class="timetable">
            <thead>
                <tr>
                    <th style="width: 10%;">Time (EAT)</th>
                    @foreach ($days as $day)
                        <th style="width: auto;">{{ $day->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach (range(8 * 60, 20 * 60 - 30, 30) as $minute)
                    @php
                        $hour = intdiv($minute, 60);
                        $min = $minute % 60;
                        $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : '';
                    @endphp
                    <tr>
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
                                <td rowspan="{{ ceil($lesson->duration / 30) }}" class="lesson">
                                    <p><strong>{{ optional($lesson->courseUnit->instructors->first())->title->name ?? '' }}
                                            {{ optional($lesson->courseUnit->instructors->first())->name ?? '' }}</strong>
                                    </p>
                                    <p>{{ $lesson->courseUnit->code }}</p>
                                    <p class="course-title">{{ $lesson->courseUnit->name }}</p>
                                    <p class="mode">Mode: Synchronous online</p>
                                    <p class="session">{{ $lesson->session ?? 'N/A' }}</p>
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>
