<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Teaching Timetable - {{ $programme->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }

        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Quicksand', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            background-color: #f8f9fa;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: left;
            margin: 0 0 10px 15px;
            font-size: 1.1rem;
            font-weight: 600;
            padding-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .university-name {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: #fff;
        }

        .programme-name {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #fff;
        }

        .programme-info {
            font-size: 0.95rem;
            margin-top: 10px;
        }

        .programme-info div {
            margin-bottom: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            page-break-inside: avoid;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #037b90;
            font-weight: bold;
            color: #fff;
        }

        .time-col {
            width: 10%;
            background-color: #037b90;
            font-weight: bold;
            color: #fff;
            font-size: 9px;
        }

        .lesson {
            background-color: #037b90;
            color: #fff;
            border: 1px solid #037b90;
            border-radius: 2px;
            padding: 2px;
            margin: 1px 0;
            font-size: 7px;
            height: 100%;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
        }

        .course-code {
            font-weight: bold;
            font-size: 7px;
            margin-bottom: 1px;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .instructor {
            font-size: 7px;
            margin-bottom: 1px;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .time {
            font-size: 6px;
            color: #fff;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
    </style>
    @php
        function isCellOccupied($groupedLessons, $dayId, $minute) {
            if (!isset($groupedLessons[$dayId])) {
                return false;
            }
            
            foreach ($groupedLessons[$dayId] as $slot => $lesson) {
                if (in_array($minute, $lesson->slots) && $minute !== $slot) {
                    return true;
                }
            }
            
            return false;
        }
    @endphp
</head>

<body>
    <div class="header">
        <div class="university-name">{{ $programme->school->name ?? 'OUK' }}</div>
        <div class="programme-name">Teaching Timetable - {{ $programme->name }}</div>
        <div class="programme-info">
            <div><strong>Level:</strong> {{ $levelName ?? 'N/A' }}</div>
            <div><strong>Semester:</strong> {{ $semesterName ?? 'N/A' }}</div>
            <div><strong>Academic Year:</strong> {{ $academicYear ?? now()->format('Y') }}/{{ now()->addYear()->format('y') }}</div>
            <div><strong>Mode of Study:</strong> {{ $programme->modeOfLearning->name ?? 'Regular' }}</div>
            <div><strong>Generated on:</strong> {{ now()->format('F j, Y') }}</div>
        </div>
    </div>

    @php
        // Filter out weekends
        $weekDays = $days->filter(function($day) {
            return !in_array(strtolower($day->name), ['saturday', 'sunday']);
        });
    @endphp

    @if ($timetable->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Time</th>
                    @foreach($weekDays as $day)
                        <th style="width: 18%;">{{ strtoupper($day->short_name) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    // Define time slots from 7:00 AM to 9:00 PM in 1-hour intervals
                    $startTime = strtotime('07:00');
                    $endTime = strtotime('21:00');
                    $interval = 60 * 60; // 60 minutes in seconds
                    
                    // Group lessons by day and time slot
                    $groupedLessons = [];
                    foreach ($timetable as $lesson) {
                        if (isset($lesson->day_id) && isset($lesson->start_minutes)) {
                            $dayId = $lesson->day_id;
                            $startSlot = floor($lesson->start_minutes / 60) * 60; // Round down to nearest hour
                            
                            // Store all time slots this lesson occupies (in 30-minute intervals)
                            $lesson->slots = range(
                                $lesson->start_minutes,
                                $lesson->start_minutes + $lesson->duration - 1,
                                30
                            );
                            $groupedLessons[$dayId][$startSlot] = $lesson;
                        }
                    }
                    
                    // Sort days by their ID to maintain consistent order
                    $weekDays = $weekDays->sortBy('id');
                @endphp
                
                @php
                    // Generate time slots from 7:00 AM to 9:00 PM in 1-hour intervals
                    $timeSlots = [];
                    for ($hour = 7; $hour < 21; $hour++) {
                        $timeSlots[] = $hour * 60;
                    }
                    
                    // Track which lessons we've already displayed to avoid duplicates
                    $displayedLessons = [];
                @endphp
                
                @foreach ($timeSlots as $index => $slotTime)
                    @php
                        $formattedTime = date('g:i A', mktime(intdiv($slotTime, 60), $slotTime % 60));
                        $nextSlotTime = $slotTime + 60;
                        $nextFormattedTime = date('g:i A', mktime(intdiv($nextSlotTime, 60), $nextSlotTime % 60));
                    @endphp
                    <tr>
                        <td class="time-col">{{ $formattedTime }}<br>{{ $nextFormattedTime }}</td>
                        @foreach($weekDays as $day)
                            @php
                                $hasLesson = isset($groupedLessons[$day->id][$slotTime]);
                                $isOccupied = $hasLesson ? false : isCellOccupied($groupedLessons, $day->id, $slotTime);
                                $lesson = $hasLesson ? $groupedLessons[$day->id][$slotTime] : null;
                                $lessonId = $lesson ? ($lesson->id ?? null) : null;
                                $alreadyDisplayed = $lessonId && in_array($lessonId, $displayedLessons);
                            @endphp
                            <td class="lesson-cell">
                                @if($hasLesson && !$alreadyDisplayed)
                                    @php 
                                        // Calculate the number of rows this lesson should span
                                        $durationInHours = $lesson->duration / 60;
                                        $rowSpan = max(1, ceil($durationInHours));
                                        
                                        // Mark this lesson as displayed
                                        if ($lessonId) {
                                            $displayedLessons[] = $lessonId;
                                        }
                                    @endphp
                                    <div class="lesson" style="height: {{ ($rowSpan * 20) - 2 }}px;">
                                        <div class="course-code" title="{{ $lesson->courseUnit->name ?? 'N/A' }}">
                                            {{ $lesson->courseUnit->code ?? 'N/A' }}
                                        </div>
                                        <div class="instructor" title="{{ $lesson->instructor_name ?? 'TBA' }}">
                                            <i class="bi bi-person"></i> {{ $lesson->instructor_name ?? 'TBA' }}
                                        </div>
                                        <div class="time">
                                            <i class="bi bi-clock"></i> {{ $lesson->formatted_start_time ?? '' }} - {{ $lesson->formatted_end_time ?? '' }}
                                        </div>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <div>Generated on {{ now()->format('F j, Y \a\t g:i A') }} | 
             <span class="page-number"></span> | 
             {{ date('Y') }} {{ $programme->school->name ?? 'OUK' }}. All rights reserved.
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 15;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>

</html>
