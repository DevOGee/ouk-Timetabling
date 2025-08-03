@if ($timetable->isNotEmpty())
    <div class="mt-5">
        <!-- Desktop View -->
        <div class="d-none d-md-block">
            <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 10%;">Time (EAT)</th>
                        @foreach ($days as $day)
                            <th style="width: 18%;">{{ $day->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $hasMorningSlots = $timetable->where('morning_start_time', '!=', null)->isNotEmpty();
                        $startTime = $hasMorningSlots ? 8 * 60 : 15 * 60;
                        $rendered = [];
                    @endphp

                    @foreach (range($startTime, 20.5 * 60, 30) as $minute)
                        @php
                            $hour = intdiv($minute, 60);
                            $min = $minute % 60;
                            $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : '';
                        @endphp
                        <tr style="height: 50px;">
                            <td style="background-color: #f8f9fa;">{{ $timeLabel }}</td>
                            @foreach ($days as $day)
                                @php
                                    $lesson = $timetable
                                        ->where('day_id', $day->id)
                                        ->first(function ($mapping) use ($minute, &$rendered) {
                                            if (isset($rendered[$mapping->day_id][$mapping->id])) {
                                                return false;
                                            }
                                            if ($mapping->morning_start_time && $mapping->morning_duration) {
                                                $morningStart = \Carbon\Carbon::parse($mapping->morning_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->morning_start_time)->minute;
                                                $morningEnd = $morningStart + $mapping->morning_duration;

                                                if ($minute >= $morningStart && $minute < $morningEnd && $minute == $morningStart) {
                                                    $mapping->start_time = $mapping->morning_start_time;
                                                    $mapping->duration = $mapping->morning_duration;
                                                    $mapping->session = 'Morning';
                                                    return true;
                                                }
                                            }
                                            if ($mapping->evening_start_time && $mapping->evening_duration) {
                                                $eveningStart = \Carbon\Carbon::parse($mapping->evening_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->evening_start_time)->minute;
                                                $eveningEnd = $eveningStart + $mapping->evening_duration;

                                                if ($minute >= $eveningStart && $minute < $eveningEnd && $minute == $eveningStart) {
                                                    $mapping->start_time = $mapping->evening_start_time;
                                                    $mapping->duration = $mapping->evening_duration;
                                                    $mapping->session = 'Evening';
                                                    return true;
                                                }
                                            }
                                            return false;
                                        });
                                @endphp

                                @if ($lesson)
                                    @php
                                        $rowspan = ceil($lesson->duration / 30);
                                        $color = $lesson->courseUnit->color ?? '#' . substr(md5($lesson->courseUnit->code), 0, 6);
                                        // Mark all slots for this lesson as rendered
                                        for ($i = 0; $i < $rowspan; $i++) {
                                            $rendered[$day->id][$lesson->id][$minute + ($i * 30)] = true;
                                        }
                                    @endphp
                                    <td rowspan="{{ $rowspan }}" style="background-color: {{ $color }}; color: white; vertical-align: top; padding: 0; height: {{ $rowspan * 50 }}px;">
                                        <div class="lesson-container">
                                            <div class="course-title mb-1">
                                                <div><strong>{{ $lesson->courseUnit->code }}</strong></div>
                                                <div style="font-size: 0.85em;">{{ $lesson->courseUnit->name }}</div>
                                            </div>
                                            <div class="programme-codes">
                                                @php
                                                    $programmes = $timetable
                                                        ->where('day_id', $day->id)
                                                        ->where('morning_start_time', $lesson->morning_start_time)
                                                        ->where('evening_start_time', $lesson->evening_start_time)
                                                        ->map(function($l) {
                                                            return $l->programme->programme_code ?? 'N/A';
                                                        })->unique()->sort()->values();
                                                @endphp
                                                @foreach($programmes as $programmeCode)
                                                    <span class="programme-badge">{{ $programmeCode }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                @elseif (!isset($rendered[$day->id][$minute]))
                                    <td></td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div class="d-md-none">
            <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; -ms-overflow-style: -ms-autohiding-scrollbar; background: #f8f9fa; padding: 5px 0;">
                <div style="min-width: 650px;">
                    <!-- Time slots header -->
                    <div style="display: flex; margin-bottom: 5px;">
                        <div style="width: 60px; flex-shrink: 0;"></div>
                        @foreach($days as $day)
                            <div style="width: 150px; text-align: center; font-size: 0.75rem; font-weight: bold; padding: 3px;">
                                {{ substr($day->name, 0, 3) }}
                            </div>
                        @endforeach
                    </div>

                    @php
                        $hasMorningSlots = $timetable->where('morning_start_time', '!=', null)->isNotEmpty();
                        $startTime = $hasMorningSlots ? 8 * 60 : 15 * 60;
                        $rendered = [];
                    @endphp

                    @foreach (range($startTime, 20.5 * 60, 30) as $minute)
                        @php
                            $hour = intdiv($minute, 60);
                            $min = $minute % 60;
                            $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : '';
                        @endphp
                        @if ($timeLabel != '')
                            <div style="display: flex; min-height: 50px; border-top: 1px solid #dee2e6;">
                                <div style="width: 60px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; background: #f8f9fa; padding: 5px;">
                                    {{ $timeLabel }}
                                </div>
                                @foreach($days as $day)
                                    @php
                                        $lesson = $timetable
                                            ->where('day_id', $day->id)
                                            ->first(function ($mapping) use ($minute, &$rendered) {
                                                if (isset($rendered[$mapping->day_id][$mapping->id])) {
                                                    return false;
                                                }
                                                if ($mapping->morning_start_time && $mapping->morning_duration) {
                                                    $morningStart = \Carbon\Carbon::parse($mapping->morning_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->morning_start_time)->minute;
                                                    $morningEnd = $morningStart + $mapping->morning_duration;

                                                    if ($minute >= $morningStart && $minute < $morningEnd && $minute == $morningStart) {
                                                        $mapping->start_time = $mapping->morning_start_time;
                                                        $mapping->duration = $mapping->morning_duration;
                                                        $mapping->session = 'Morning';
                                                        return true;
                                                    }
                                                }
                                                if ($mapping->evening_start_time && $mapping->evening_duration) {
                                                    $eveningStart = \Carbon\Carbon::parse($mapping->evening_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->evening_start_time)->minute;
                                                    $eveningEnd = $eveningStart + $mapping->evening_duration;

                                                    if ($minute >= $eveningStart && $minute < $eveningEnd && $minute == $eveningStart) {
                                                        $mapping->start_time = $mapping->evening_start_time;
                                                        $mapping->duration = $mapping->evening_duration;
                                                        $mapping->session = 'Evening';
                                                        return true;
                                                    }
                                                }
                                                return false;
                                            });
                                    @endphp

                                    @if ($lesson)
                                        @php
                                            $height = ceil($lesson->duration / 30) * 50;
                                            $color = $lesson->courseUnit->color ?? '#' . substr(md5($lesson->courseUnit->code), 0, 6);
                                            // Mark all slots for this lesson as rendered
                                            for ($i = 0; $i < ceil($lesson->duration / 30); $i++) {
                                                $rendered[$day->id][$lesson->id][$minute + ($i * 30)] = true;
                                            }
                                        @endphp
                                        <div style="width: 150px; height: {{ $height }}px; margin: 0 1px; background-color: {{ $color }}; color: white; padding: 5px; font-size: 0.7rem; border-radius: 3px; display: flex; flex-direction: column; justify-content: center;">
                                            <div style="font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $lesson->courseUnit->code }}
                                            </div>
                                            <div style="font-size: 0.8em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $lesson->courseUnit->name }}
                                            </div>
                                            <div style="font-size: 0.65rem; opacity: 0.9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                @php
                                                    $programmes = $timetable
                                                        ->where('day_id', $day->id)
                                                        ->where('morning_start_time', $lesson->morning_start_time)
                                                        ->where('evening_start_time', $lesson->evening_start_time)
                                                        ->map(function($l) {
                                                            return $l->programme->programme_code ?? 'N/A';
                                                        })->unique()->sort()->values();
                                                    echo $programmes->implode(', ');
                                                @endphp
                                            </div>
                                        </div>
                                    @else
                                        <div style="width: 150px; height: 50px; margin: 0 1px; background-color: #f8f9fa; border: 1px solid #dee2e6;"></div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="mt-2 text-muted small text-center" style="padding: 5px 0;">
                <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to view full schedule
            </div>
        </div>
    </div>

    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .lesson-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2px;
        }
        .programme-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 2px 5px;
            border-radius: 3px;
            margin-right: 3px;
            font-size: 0.65rem;
        }
        @media (min-width: 768px) {
            .course-title {
                font-size: 0.8rem;
            }
            .programme-badge {
                font-size: 0.7rem;
            }
        }
    </style>
@endif