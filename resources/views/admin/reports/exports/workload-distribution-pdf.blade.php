<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Workload Distribution Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1, h2, h3, h4, h5, h6 {
            margin: 0 0 10px 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h2 {
            color: #333;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 3px;
        }
        .badge-primary {
            background-color: #007bff;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .text-center {
            text-align: center !important;
        }
        .mb-0 {
            margin-bottom: 0 !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Workload Distribution Report</h2>
        <p><strong>Academic Session:</strong> {{ $academicSession->name }}</p>
        @if($school)
            <p><strong>School:</strong> {{ $school->name }}</p>
        @endif
        <p><strong>Generated:</strong> {{ now()->format('F j, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 30%;">Instructor</th>
                <th style="width: 50%;">Course Units</th>
                <th style="width: 15%; text-align: center;">Total Units</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workloadData as $instructor)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $instructor->name }}</td>
                    <td>{{ $instructor->course_units ?: 'No units assigned' }}</td>
                    <td style="text-align: center;">
                        <span class="badge badge-primary">{{ $instructor->total_units }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        No workload data found for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
