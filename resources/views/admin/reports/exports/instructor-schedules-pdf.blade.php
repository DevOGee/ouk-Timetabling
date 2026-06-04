<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Instructor Schedule - {{ $instructor->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .time-slot {
            background-color: #f1f8ff;
            padding: 3px 6px;
            border-radius: 3px;
            display: inline-block;
            margin: 2px;
            font-family: monospace;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin: 1px;
            display: inline-block;
        }
        .badge-morning {
            background-color: #cfe2ff;
            color: #084298;
        }
        .badge-evening {
            background-color: #e2e3e5;
            color: #2b2f32;
        }
        .course-code {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .course-name {
            color: #6c757d;
            font-size: 11px;
        }
        .header {
            margin-bottom: 20px;
        }
        .instructor-info {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Instructor Schedule</h2>
        <div class="instructor-info">
            <p><strong>Instructor:</strong> {{ $instructor->name }}</p>
            <p><strong>Email:</strong> {{ $instructor->email }}</p>
            <p><strong>Academic Session:</strong> {{ $schedules->first()->academic_session ?? 'N/A' }}</p>
            <p><strong>Generated:</strong> {{ now()->format('F j, Y H:i') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%">Day & Time</th>
                <th style="width: 35%">Course Unit</th>
                <th style="width: 40%">Programmes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
                <tr>
                    <td>
                        <strong>{{ $schedule->day }}</strong>
                        <div style="margin-top: 5px;">
                            @foreach($schedule->times as $index => $time)
                                <div class="time-slot">
                                    {{ $time }}
                                    <span class="badge {{ $schedule->session_types[$index] === 'Morning' ? 'badge-morning' : 'badge-evening' }}">
                                        {{ $schedule->session_types[$index] ?? '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <div class="course-code">{{ $schedule->course_unit->code ?? 'N/A' }}</div>
                        <div class="course-name">{{ $schedule->course_unit->name ?? 'N/A' }}</div>
                        <div style="margin-top: 3px;">
                            <span style="background-color: #d1e7dd; color: #0f5132; padding: 1px 5px; border-radius: 3px; font-size: 10px;">
                                {{ $schedule->year_semester ?? 'N/A' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        @if(count($schedule->programmes) > 0)
                            <ul style="margin: 0; padding-left: 15px;">
                                @foreach($schedule->programmes as $programme)
                                    <li style="margin-bottom: 3px;">
                                        <span style="background-color: #f8f9fa; padding: 1px 5px; border-radius: 3px; font-size: 11px;">
                                            {{ $programme->name }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #6c757d;">N/A</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            
            @if(count($schedules) === 0)
            <tr>
                <td colspan="3" style="text-align: center;">No schedules found for this instructor.</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
