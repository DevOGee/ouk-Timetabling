<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class Schedules - {{ $school->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .programme-title { font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; }
        .course-code { font-weight: bold; }
        .course-name { font-size: 11px; color: #666; }
        .instructor { font-size: 10px; color: #4a6fdc; margin-top: 2px; }
    </style>
</head>
<body>
    <h2>{{ $school->name }} - Class Schedules</h2>
    <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>

    @foreach($scheduleData as $programmeData)
        <div class="programme-title">
            {{ $programmeData['programme_code'] }} - {{ $programmeData['programme_name'] }}
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 100px;">Year/Sem</th>
                    @foreach($days as $day)
                        <th>{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($programmeData['schedules'] as $row)
                    <tr>
                        <td style="font-weight: bold;">{{ $row['row_header'] }}</td>
                        @foreach($days as $day)
                            <td>
                                @if(!empty($row['days'][$day]))
                                    @foreach($row['days'][$day] as $course)
                                        <div>
                                            <div class="course-code">{{ $course['programme_code'] }} {{ $course['code'] }}</div>
                                            @if($showCourseNames)
                                                <div class="course-name">{{ $course['name'] }}</div>
                                            @endif
                                            @if($showInstructors && !empty($course['instructors']))
                                                <div class="instructor">
                                                    {{ implode(', ', array_column($course['instructors'], 'name')) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if(!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>
