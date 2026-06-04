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
    {{-- <p class="sub-header">{{ $programme->programme_code }} - {{ $programme->name }}</p> --}}
    <p class="sub-header">
        {{-- Programme: {{ $programme->programme_code }} - {{ $programme->name }} <br> --}}
        Year of Study: Level {{ $yearOfStudy->name }} <br>
        Semester: {{ $semester->name }} <br>
        Academic Year: {{ $academicYear->year }}
    </p>

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

    <div class="footer">
        Exported on: {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>

</html>
