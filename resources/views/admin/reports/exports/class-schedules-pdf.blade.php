<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Class Schedules - {{ $school->name }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            font-size: 10px;
            line-height: 1.2;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        th, td { 
            border: 0.5px solid #ddd; 
            padding: 4px; 
            text-align: left; 
            font-size: 9px;
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
        }
        .programme-title { 
            font-size: 12px; 
            font-weight: bold; 
            margin: 15px 0 5px 0;
            page-break-after: avoid;
        }
        .course-code { 
            font-weight: bold; 
            font-size: 9px;
        }
        .course-name { 
            font-size: 8px; 
            color: #666; 
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $school->name }} - Class Schedules</h2>
        <p>Academic Session: {{ $academicSession->name ?? 'N/A' }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @foreach($scheduleData as $programmeData)
        <div class="programme-title">
            {{ $programmeData['programme_code'] }} - {{ $programmeData['programme_name'] }}
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Year/Sem</th>
                    @foreach($days as $day)
                        <th style="width: " . (85 / count($days)) . "%;">{{ $day }}</th>
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
                                        <div style="margin-bottom: 4px;">
                                            <div class="course-code">{{ $course['programme_code'] }} {{ $course['code'] }}</div>
                                            @if($showCourseNames)
                                                <div class="course-name">{{ $course['name'] }}</div>
                                            @endif
                                            @if(!empty($course['instructor']))
                                                <div class="instructor" style="font-size: 8px; color: #444;">
                                                    {{ $course['instructor'] }}
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
    @endforeach
</body>
</html>
