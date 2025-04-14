@if ($timetable->isNotEmpty())
    <div class="mt-5">
        <h3>Teaching & Learning Schedule</h3>

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
                    // Determine if we should start at 3 PM or 8 AM based on the presence of morning or evening slots
                    $hasMorningSlots = $timetable->where('morning_start_time', '!=', null)->isNotEmpty();
                    $startTime = $hasMorningSlots ? 8 * 60 : 15 * 60; // Start at 8 AM (0800 hrs) if morning slots exist, else start at 3 PM (1500 hrs)
                @endphp

                @foreach (range($startTime, 20.5 * 60, 30) as $minute)
                    <!-- Loop from startTime to 9 PM (2100 hrs) -->
                    @php
                        $hour = intdiv($minute, 60);
                        $min = $minute % 60;
                        $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : ''; // Show full hours only
                    @endphp
                    <tr style="height: 50px;">
                        <td>{{ $timeLabel }}</td>
                        @foreach ($days as $day)
                            @php
                                $lesson = $timetable
                                    ->where('day_id', $day->id)
                                    ->first(function ($mapping) use ($minute) {
                                        // Check for morning sessions
                                        if ($mapping->morning_start_time && $mapping->morning_duration) {
                                            $morningStart =
                                                \Carbon\Carbon::parse($mapping->morning_start_time)->hour * 60 +
                                                \Carbon\Carbon::parse($mapping->morning_start_time)->minute;
                                            $morningEnd = $morningStart + $mapping->morning_duration;

                                            if ($minute >= $morningStart && $minute < $morningEnd) {
                                                $mapping->start_time = $mapping->morning_start_time;
                                                $mapping->duration = $mapping->morning_duration;
                                                $mapping->session = 'Morning';
                                                return true;
                                            }
                                        }

                                        // Check for evening sessions
                                        if ($mapping->evening_start_time && $mapping->evening_duration) {
                                            $eveningStart =
                                                \Carbon\Carbon::parse($mapping->evening_start_time)->hour * 60 +
                                                \Carbon\Carbon::parse($mapping->evening_start_time)->minute;
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

                            @if (
                                $lesson &&
                                    \Carbon\Carbon::parse($lesson->start_time)->hour * 60 + \Carbon\Carbon::parse($lesson->start_time)->minute ==
                                        $minute)
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
@endif
