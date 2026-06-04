@if ($timetable->isNotEmpty())
    <div class="mt-5">
        <h3>Teaching & Learning Schedule</h3>
        
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
                                        ->first(function ($mapping) use ($minute) {
                                            if ($mapping->morning_start_time && $mapping->morning_duration) {
                                                $morningStart = \Carbon\Carbon::parse($mapping->morning_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->morning_start_time)->minute;
                                                $morningEnd = $morningStart + $mapping->morning_duration;

                                                if ($minute >= $morningStart && $minute < $morningEnd) {
                                                    $mapping->start_time = $mapping->morning_start_time;
                                                    $mapping->duration = $mapping->morning_duration;
                                                    $mapping->session = 'Morning';
                                                    return true;
                                                }
                                            }


                                            if ($mapping->evening_start_time && $mapping->evening_duration) {
                                                $eveningStart = \Carbon\Carbon::parse($mapping->evening_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->evening_start_time)->minute;
                                                $eveningEnd = $eveningStart + $mapping->evening_duration;

                                                if ($minute >= $eveningStart && $minute < $eveningEnd) {
                                                    $mapping->start_time = $mapping->evening_start_time;
                                                    $mapping->duration = $mapping->evening_duration;
                                                    $mapping->session = 'Evening';
                                                    return true;
                                                }
                                            }
                                            return false;
                                        });
                                @endphp

                                @if ($lesson && \Carbon\Carbon::parse($lesson->start_time)->hour * 60 + \Carbon\Carbon::parse($lesson->start_time)->minute == $minute)
                                    @include('partials.timetable.slot', ['lesson' => $lesson])
                                @else
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
                        <div style="width: 80px; text-align: center; font-size: 0.75rem; font-weight: bold; padding: 3px;">
                            {{ substr($day->name, 0, 3) }}
                        </div>
                        @endforeach
                    </div>

                    @php
                        $hasMorningSlots = $timetable->where('morning_start_time', '!=', null)->isNotEmpty();
                        $startTime = $hasMorningSlots ? 8 * 60 : 15 * 60;
                    @endphp

                    @foreach (range($startTime, 20.5 * 60, 30) as $minute)
                        @php
                            $hour = intdiv($minute, 60);
                            $min = $minute % 60;
                            $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : '';
                            $hasContent = false;
                        @endphp
                        
                        @if($timeLabel != '')
                        <div style="display: flex; min-height: 40px; border-top: 1px solid #dee2e6;">
                            <div style="width: 60px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; background: #f8f9fa; padding: 5px;">
                                {{ $timeLabel }}
                            </div>
                            
                            @foreach($days as $day)
                                @php
                                    $lesson = $timetable
                                        ->where('day_id', $day->id)
                                        ->first(function ($mapping) use ($minute) {
                                            if ($mapping->morning_start_time && $mapping->morning_duration) {
                                                $morningStart = \Carbon\Carbon::parse($mapping->morning_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->morning_start_time)->minute;
                                                $morningEnd = $morningStart + $mapping->morning_duration;

                                                if ($minute >= $morningStart && $minute < $morningEnd) {
                                                    $mapping->start_time = $mapping->morning_start_time;
                                                    $mapping->duration = $mapping->morning_duration;
                                                    $mapping->session = 'Morning';
                                                    return true;
                                                }
                                            }


                                            if ($mapping->evening_start_time && $mapping->evening_duration) {
                                                $eveningStart = \Carbon\Carbon::parse($mapping->evening_start_time)->hour * 60 + \Carbon\Carbon::parse($mapping->evening_start_time)->minute;
                                                $eveningEnd = $eveningStart + $mapping->evening_duration;

                                                if ($minute >= $eveningStart && $minute < $eveningEnd) {
                                                    $mapping->start_time = $mapping->evening_start_time;
                                                    $mapping->duration = $mapping->evening_duration;
                                                    $mapping->session = 'Evening';
                                                    return true;
                                                }
                                            }
                                            return false;
                                        });
                                @endphp

                                @if ($lesson && \Carbon\Carbon::parse($lesson->start_time)->hour * 60 + \Carbon\Carbon::parse($lesson->start_time)->minute == $minute)
                                    <div style="width: 80px; min-height: 40px; padding: 3px; background-color: {{ $lesson->courseUnit?->color ?? '#ff7f50' }}; color: white;">
                                        @include('partials.timetable.slot', ['lesson' => $lesson, 'mobile' => true])
                                    </div>
                                    @php $hasContent = true; @endphp
                                @else
                                    <div style="width: 80px; min-height: 40px; padding: 3px; background: white;"></div>
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
        .instructor-img-container {
            text-align: center;
            margin-bottom: 3px;
        }
        .instructor-image {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 1px solid #fff;
            object-fit: cover;
        }
        .lesson-details {
            text-align: center;
        }
        .instructor-name {
            font-size: 0.7rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .course-code {
            font-weight: bold;
            font-size: 0.7rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .course-title {
            display: none;
        }
        .mode, .session {
            font-size: 0.6rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @media (min-width: 768px) {
            .course-title {
                display: inline;
            }
            .instructor-image {
                width: 35px;
                height: 35px;
            }
        }
    </style>
@endif
