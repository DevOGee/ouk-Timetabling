<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Time Conflicts Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 5px 0 0 0;
            padding: 0;
        }
        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .filters p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #4e73df;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #6c757d;
        }
        .conflict-group {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .conflict-header {
            background-color: #f8d7da !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Time Conflicts Report</h1>
        <p>Generated on: {{ now()->format('F j, Y h:i A') }}</p>
    </div>

    <div class="filters">
        <p><strong>Academic Session:</strong> {{ $academicSession->name }}</p>
        @if($school)
            <p><strong>School:</strong> {{ $school->name }}</p>
        @else
            <p><strong>School:</strong> All Schools</p>
        @endif
    </div>

    @if(count($reportData) > 0)
        @foreach($reportData as $conflict)
            <div class="conflict-group">
                <table>
                    <thead>
                        <tr class="conflict-header">
                            <th colspan="8">
                                {{ $conflict['day'] }} - {{ $conflict['time'] }}
                            </th>
                        </tr>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Programme</th>
                            <th>Year/Semester</th>
                            <th>Instructor</th>
                            <th>Session Type</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conflict['conflicts'] as $mapping)
                            <tr>
                                <td>{{ $mapping->course_unit->code ?? 'N/A' }}</td>
                                <td>{{ $mapping->course_unit->name ?? 'N/A' }}</td>
                                <td>{{ $mapping->programme->programme_code ?? 'N/A' }}</td>
                                <td>
                                    {{ $mapping->year_of_study ? $mapping->year_of_study->name : 'N/A' }} / 
                                    {{ $mapping->semester ? $mapping->semester->name : 'N/A' }}
                                </td>
                                <td>{{ $mapping->instructor ? $mapping->instructor->name : 'N/A' }}</td>
                                <td>{{ $mapping->session_type }}</td>
                                <td>{{ $mapping->start_time }}</td>
                                <td>{{ $mapping->end_time }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <p>No time conflicts found for the selected filters.</p>
    @endif

    <div class="footer">
        <p>Page {{ $pageNo }} of {{ $pageCount }}</p>
    </div>
</body>
</html>
