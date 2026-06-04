<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Schedule Export - {{ $programme->name }}</title>
    <style>
        @page { margin: 30px 25px; }
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ base_path('public/ouk-logo.png') }}');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 60%;
            opacity: 0.3;
            z-index: -1;
            pointer-events: none;
        }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 50px;
        }
        .header h1 {
            font-size: 18pt;
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        .header h2 {
            font-size: 14pt;
            margin: 5px 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #555;
        }
        .level-section {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }
        .level-title {
            background-color: #f8f9fa;
            padding: 5px 10px;
            font-weight: bold;
            margin: 5px 0 10px 0;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #f1f5fd;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 9pt;
        }
        .time-slot {
            font-size: 8pt;
            margin: 2px 0;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        .morning {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
        }
        .evening {
            background-color: #f3e5f5;
            border-left: 3px solid #9c27b0;
        }
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .footer p {
            margin: 2px 0;
        }
        .school-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="watermark"></div>
    {{-- <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;"> --}}
    
    <div>
        <h1 style="font-size: 16pt; margin: 0 0 10px 0; text-align: left;">{{ $programme->school->name ?? 'School of ' . $programme->name }}</h1>
        <p style="margin: 5px 0; text-align: left;">Teaching & Learning Schedule for {{ $programme->name }}</p>
        {{-- <p style="margin: 5px 0; text-align: left;">Programme Code: {{ $programme->programme_code }}</p> --}}
        <p style="margin: 5px 0 0 0; text-align: left;">Academic Session: {{ $academicSession->name }}</p>
    </div>

    {{-- <div>
        <img src="{{ public_path('ouk-logo.png') }}" alt="OUK Logo" style="height: 80px; width: auto;">
    </div> --}}

{{-- </header> --}}


        


    @foreach($mappings as $yearOfStudyId => $semesters)
        @foreach($semesters as $semesterId => $courses)
            @php
                $yearOfStudy = $courses->first()->yearOfStudy ?? null;
                $semester = $courses->first()->semester ?? null;
            @endphp
            <div class="level-section">
                <div class="level-title">
                    Level {{ $yearOfStudy ? $yearOfStudy->name : 'N/A' }}.{{ $semester ? $semester->name : 'Semester N/A' }}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%;">Code</th>
                            <th style="width: 30%;">Course Name</th>
                            <th style="width: 15%;">Day</th>
                            <th style="width: 20%;">Instructor</th>
                            <th style="width: 25%;">Schedule</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $mapping)
                            <tr>
                                <td>{{ $mapping->courseUnit->code }}</td>
                                <td>{{ $mapping->courseUnit->name }}</td>
                                <td>{{ $mapping->day->name ?? 'Not Set' }}</td>
                                <td>{{ $mapping->instructor ? $mapping->instructor->name : 'Not Assigned' }}</td>
                                <td>
                                    @if($mapping->morning_start_time)
                                        <div class="time-slot morning">
                                            <strong>Morning:</strong> 
                                            {{ \Carbon\Carbon::parse($mapping->morning_start_time)->format('h:i A') }} 
                                            ({{ $mapping->morning_duration }} min)
                                        </div>
                                    @endif
                                    @if($mapping->evening_start_time)
                                        <div class="time-slot evening">
                                            <strong>Evening:</strong> 
                                            {{ \Carbon\Carbon::parse($mapping->evening_start_time)->format('h:i A') }} 
                                            ({{ $mapping->evening_duration }} min)
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endforeach

    <div style="page-break-before: always;"></div>

    <div class="footer" style="text-align: right;">
        {{-- <p>Generated by OUK Planner System</p> --}}
        <p style="margin: 2px 0;">Powered by Directorate of ICT</p>
        <p style="margin: 2px 0 0 0;">Generated on: {{ now()->format('F j, Y h:i A') }}</p>
    </div>
</body>
</html>
