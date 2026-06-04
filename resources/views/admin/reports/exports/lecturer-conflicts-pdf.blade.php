<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Lecturer Conflicts Report</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0.5cm;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .title {
            font-size: 14pt;
            margin: 0;
            padding: 0;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12pt;
            margin-bottom: 15px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .instructor-section {
            margin-bottom: 20px;
            page-break-inside: auto;
            break-inside: auto;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .instructor-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #ddd;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .conflict-block {
            margin-bottom: 8px;
            page-break-inside: avoid;
            break-inside: avoid-page;
        }
        .conflict-header {
            background-color: #f9f9f9;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
        }
        .conflict-body {
            padding: 4px 0;
            font-size: 8pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 8pt;
        }
        th {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 3px 5px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 3px 5px;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 8pt;
            color: #6c757d;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .page-break {
            page-break-after: always;
            break-after: page;
        }
        .no-conflicts {
            text-align: center;
            font-style: italic;
            color: #6c757d;
            padding: 10px;
            font-size: 9pt;
        }
        .instructor-number {
            font-weight: bold;
            margin-right: 2px;
            color: #333;
        }
        .instructor-number:after {
            content: '.';
            margin-right: 2px;
        }
        .school-badge {
            background-color: #6c757d;
            color: white;
            border-radius: 3px;
            padding: 1px 6px;
            font-size: 8pt;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Lecturer Scheduling Conflicts Report</div>
        <div class="subtitle">
            @if(isset($academicSession))
                Academic Session: {{ $academicSession->name }}
            @endif
            @if(isset($school))
                <br>School: {{ $school->name }}
            @endif
        </div>
        {{-- <div class="filters">
            Generated on: {{ now()->format('F j, Y \a\t g:i A') }}
        </div> --}}
    </div>

    @if($conflicts->isEmpty())
        <div class="no-conflicts">
            No lecturer scheduling conflicts found for the selected filters.
        </div>
    @else
        @php $instructorNumber = 1; @endphp
        @foreach($conflicts as $instructorId => $instructorConflicts)
            @php 
                $firstConflict = $instructorConflicts->first();
                $instructor = is_array($firstConflict) ? $firstConflict['instructor'] : $firstConflict->instructor;
            @endphp
            
            <div class="instructor-section">
                <div class="instructor-name">
                    <span class="instructor-number">{{ $instructorNumber++ }}</span>
                    <span>{{ $instructor->name }}</span>
                    @if($instructor->school)
                        <span class="school-badge">
                            <i class="fas fa-school"></i> {{ $instructor->school->name }}
                        </span>
                    @endif
                </div>
                
                @foreach($instructorConflicts as $conflictItem)
                    @php
                        $conflict = is_array($conflictItem) ? (object)$conflictItem : $conflictItem;
                        $conflictSlots = is_array($conflict->conflicting_slots) 
                            ? $conflict->conflicting_slots 
                            : $conflict->conflicting_slots->toArray();
                    @endphp
                    <div class="conflict-block">
                        <div class="conflict-header">
                            Conflict on {{ $conflict->day ?? 'Unknown Day' }} ({{ $conflict->time_period ?? 'Unknown Time' }})
                        </div>
                        <div class="conflict-body">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Programme</th>
                                        <th>Time</th>
                                        <th>Session</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $formatTime = function($timeStr) {
                                            if (empty($timeStr)) return 'N/A';
                                            try {
                                                return \Carbon\Carbon::createFromFormat('H:i:s', $timeStr)->format('g.ia');
                                            } catch (\Exception $e) {
                                                try {
                                                    return \Carbon\Carbon::parse($timeStr)->format('g.ia');
                                                } catch (\Exception $e) {
                                                    return $timeStr;
                                                }
                                            }
                                        };
                                    @endphp
                                    @foreach($conflictSlots as $slotItem)
                                        @php 
                                            $slot = is_array($slotItem) ? (object)$slotItem : $slotItem;
                                            $timeParts = isset($slot->time) ? explode(' - ', $slot->time) : [];
                                            $formattedTime = count($timeParts) === 2 
                                                ? $formatTime(trim($timeParts[0])) . ' - ' . $formatTime(trim($timeParts[1]))
                                                : 'N/A';
                                        @endphp
                                        <tr>
                                            <td>{{ $slot->course ?? 'N/A' }}</td>
                                            <td>{{ $slot->programme ?? 'N/A' }}</td>
                                            <td>{{ $formattedTime }}</td>
                                            <td>{{ $slot->type ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            
        @endforeach
    @endif
    
    <div class="footer">
        Generated by OUK Timetable System on {{ now()->format('F j, Y \a\t g:i A') }}
    </div>
</body>
</html>
