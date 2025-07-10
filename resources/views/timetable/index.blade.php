<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timetable</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .page-header {
            text-align: left;
            margin: 0 0 10px 15px;
            color: #037b90;
            font-size: 1.1rem;
            font-weight: 600;
            padding-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-section {
            padding: 1.5rem 1rem;
            background-color: #E3F2FD;
            margin: 0 0 2rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .filter-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.5rem;
        }
        
        .form-control, .form-select {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border: 1px solid #ced4da !important;
            height: 48px;
            border-radius: 6px;
            font-size: 15px;
            padding: 0.5rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #80bdff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            background-color: #fff !important;
            color: #000 !important;
        }
        
        .form-label {
            color: #fff;
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .filter-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4361ee, #f72585);
        }

        .form-group {
            margin-bottom: 0;
            position: relative;
        }

        .form-label {
            display: none;
            /* Hide the labels as we'll use placeholders */
        }

        .form-control {
            height: 3.5rem;
            border-radius: 50px;
            padding: 0 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.1);
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 400;
        }

        .form-control:focus {
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
            background-color: rgba(255, 255, 255, 0.15);
        }

        .form-control:hover {
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            background-color: rgba(255, 255, 255, 0.15);
        }

        .btn {
            border-radius: 50px;
            padding: 0.75rem 1.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(45deg, #4361ee, #3a0ca3);
        }

        .btn-danger {
            background: linear-gradient(45deg, #f72585, #b5179e);
            display: none;
            /* Hidden by default */
            margin-top: 1.5rem;
            padding: 0.85rem 2rem;
            font-size: 0.95rem;
            letter-spacing: 1px;
        }

        .timetable-loaded .btn-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            animation: fadeInUp 0.5s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        select.form-control {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23000000' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-color: #f8f9fa !important;
            color: #000 !important;
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 14px 10px;
            padding-right: 3rem;
            cursor: pointer;
        }

        select.form-control:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        }

        *:focus {
            outline: none;
        }

        .form-group {
            transition: transform 0.3s ease;
        }

        .form-group:focus-within {
            transform: scale(1.005);
        }

        select option {
            background-color: #2a2a2a;
            color: #fff;
        }

        select option:hover {
            background-color: #4361ee;
        }

        select option:checked {
            background-color: #4361ee;
            color: white;
        }

        .filter-title {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .filter-form {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lesson-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
            gap: 4px;
            height: 100%;
            justify-content: center;
            padding: 6px;
        }

        .instructor-img-container {
            margin-bottom: 5px;
        }

        .instructor-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
        }

        .lesson-details {
            width: 100%;
        }

        .instructor-name {
            font-weight: bold;
            color: white;
            margin: 0;
            font-size: 14px;
            line-height: 1.2;
        }

        .course-code {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: bold;
            line-height: 1.2;
            margin: 2px 0;
        }

        .course-title {
            font-style: italic;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.2;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mode {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
        }

        .session {
            font-size: 14px;
            font-weight: bold;
            color: white;
            margin-top: 5px;
        }

        /* Timetable specific styles */
        .table-bordered {
            border: 1px solid #dee2e6;
            table-layout: fixed;
            width: 100%;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            color: #333;
            padding: 0;
            height: 50px;
            overflow: hidden;
        }
        .table thead th {
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
            background-color: #e9ecef;
            color: #495057;
            text-align: center;
            font-weight: 600;
            padding: 10px 5px;
        }
        .table tbody tr {
            height: 50px;
        }
        .table tbody td {
            padding: 0;
            vertical-align: top;
            position: relative;
        }
        .table tbody td.time-cell {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: bold;
            white-space: nowrap;
            padding: 0 5px;
            width: 10%;
        }
        .lesson-container {
            padding: 4px 8px;
            position: absolute;
            top: 1px;
            left: 1px;
            right: 1px;
            bottom: 1px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }
        .instructor-img-container {
            display: none; /* Hide images in desktop view for now */
        }
        .instructor-name {
            font-weight: bold;
            color: white;
            margin: 0;
            font-size: 14px;
            line-height: 1.2;
        }
        .course-code {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: bold;
            line-height: 1.2;
        }
        .course-title {
            font-style: italic;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mode, .session {
            display: none; /* Hide mode and session in desktop view */
        }
    </style>
</head>

<body>
    <div class="filter-section">
        @php
            $groupedProgrammes = $programmes->sortBy('programme_code')->groupBy('school_id');
        @endphp

        <div class="container">
            {{-- <h2 class="filter-title">Timetable Viewer</h2> --}}

            <form method="GET" action="{{ route('timetable.index') }}" class="filter-form">
                <div class="row g-4">
                    {{-- School Dropdown --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control form-control-lg" id="school_id" name="school_id" required>
                                <option value="">Select School</option>
                                @foreach ($schools as $school)
                                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Programme Dropdown --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control form-control-lg" id="programme_id" name="programme_id" required>
                                <option value="">Select Programme</option>
                                @foreach ($groupedProgrammes as $schoolId => $schoolProgrammes)
                                    @foreach ($schoolProgrammes as $programme)
                                        <option value="{{ $programme->id }}" data-school="{{ $schoolId }}"
                                            {{ request('programme_id') == $programme->id ? 'selected' : '' }}
                                            style="display: {{ request('school_id') == $schoolId || !request('school_id') ? '' : 'none' }};">
                                            {{ $programme->programme_code }} - {{ $programme->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Level of Study --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control form-control-lg" id="level" name="level" required>
                                <option value="">Select Level</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </div>

    <div class="container-fluid p-0">
        @if ($timetable->isNotEmpty())
            @php
                $selectedProgramme = request('programme_id') ? $programmes->where('id', request('programme_id'))->first() : null;
                
                // --- FIX STARTS HERE ---
                // Determine the earliest start time from the timetable data
                $earliestTime = null;
                foreach ($timetable as $lesson) {
                    $morning = $lesson->morning_start_time ? \Carbon\Carbon::parse($lesson->morning_start_time) : null;
                    $evening = $lesson->evening_start_time ? \Carbon\Carbon::parse($lesson->evening_start_time) : null;

                    if ($morning && (!$earliestTime || $morning->lt($earliestTime))) {
                        $earliestTime = $morning;
                    }
                    if ($evening && (!$earliestTime || $evening->lt($earliestTime))) {
                        $earliestTime = $evening;
                    }
                }
                
                // Default to 8 AM if no lessons exist, otherwise use the floor hour of the earliest lesson
                $startHour = $earliestTime ? $earliestTime->hour : 8;
                $startTimeInMinutes = $startHour * 60;
                // --- FIX ENDS HERE ---
            @endphp
            <h6 class="page-header">
                Teaching &amp; Learning Schedule for {{ $selectedProgramme->name ?? 'Selected Programme' }}
            </h6>

            <div class="d-none d-md-block px-3 py-2">
                <table class="table table-bordered m-0" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Time (EAT)</th>
                            @foreach ($days as $day)
                                <th style="width: 18%;">{{ $day->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (range($startTimeInMinutes, 20.5 * 60, 30) as $minute)
                            @php
                                $hour = intdiv($minute, 60);
                                $min = $minute % 60;
                                $timeLabel = ($min == 0) ? sprintf('%02d:00', $hour) : '';
                            @endphp
                            <tr>
                                <td class="time-cell">{{ $timeLabel }}</td>
                                @foreach ($days as $day)
                                    @php
                                        $lesson = $timetable->first(function ($mapping) use ($day, $minute) {
                                            if ($mapping->day_id != $day->id) return false;

                                            if ($mapping->morning_start_time && $mapping->morning_duration) {
                                                $start = \Carbon\Carbon::parse($mapping->morning_start_time);
                                                $startMinute = $start->hour * 60 + $start->minute;
                                                $endMinute = $startMinute + $mapping->morning_duration;
                                                if ($minute >= $startMinute && $minute < $endMinute) {
                                                    $mapping->session_start_time = $mapping->morning_start_time;
                                                    $mapping->session_duration = $mapping->morning_duration;
                                                    $mapping->session_name = 'Morning';
                                                    return true;
                                                }
                                            }
                                            if ($mapping->evening_start_time && $mapping->evening_duration) {
                                                $start = \Carbon\Carbon::parse($mapping->evening_start_time);
                                                $startMinute = $start->hour * 60 + $start->minute;
                                                $endMinute = $startMinute + $mapping->evening_duration;
                                                if ($minute >= $startMinute && $minute < $endMinute) {
                                                    $mapping->session_start_time = $mapping->evening_start_time;
                                                    $mapping->session_duration = $mapping->evening_duration;
                                                    $mapping->session_name = 'Evening';
                                                    return true;
                                                }
                                            }
                                            return false;
                                        });

                                        $isFirstSlot = false;
                                        if($lesson) {
                                            $lessonStartMinute = \Carbon\Carbon::parse($lesson->session_start_time)->hour * 60 + \Carbon\Carbon::parse($lesson->session_start_time)->minute;
                                            if($lessonStartMinute == $minute) {
                                                $isFirstSlot = true;
                                            }
                                        }
                                    @endphp

                                    @if ($isFirstSlot)
                                        @include('partials.timetable.slot', ['lesson' => $lesson, 'mobile' => false])
                                    @elseif (!$lesson)
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-md-none px-3 pb-3">
                 <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="table table-bordered" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Time</th>
                                @foreach ($days as $day)
                                    <th style="width: 140px;">{{ $day->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                             @foreach (range($startTimeInMinutes, 20.5 * 60, 30) as $minute)
                                @php
                                    $hour = intdiv($minute, 60);
                                    $min = $minute % 60;
                                    $timeLabel = ($min == 0) ? sprintf('%02d:00', $hour) : '';
                                @endphp
                                <tr>
                                    <td class="time-cell" style="font-size: 0.8rem;">{{ $timeLabel }}</td>
                                    @foreach ($days as $day)
                                        @php
                                            $lesson = $timetable->first(function ($mapping) use ($day, $minute) {
                                                if ($mapping->day_id != $day->id) return false;

                                                if ($mapping->morning_start_time && $mapping->morning_duration) {
                                                    $start = \Carbon\Carbon::parse($mapping->morning_start_time);
                                                    $startMinute = $start->hour * 60 + $start->minute;
                                                    $endMinute = $startMinute + $mapping->morning_duration;
                                                    if ($minute >= $startMinute && $minute < $endMinute) {
                                                        $mapping->session_start_time = $mapping->morning_start_time;
                                                        $mapping->session_duration = $mapping->morning_duration;
                                                        $mapping->session_name = 'Morning';
                                                        return true;
                                                    }
                                                }
                                                if ($mapping->evening_start_time && $mapping->evening_duration) {
                                                    $start = \Carbon\Carbon::parse($mapping->evening_start_time);
                                                    $startMinute = $start->hour * 60 + $start->minute;
                                                    $endMinute = $startMinute + $mapping->evening_duration;
                                                    if ($minute >= $startMinute && $minute < $endMinute) {
                                                        $mapping->session_start_time = $mapping->evening_start_time;
                                                        $mapping->session_duration = $mapping->evening_duration;
                                                        $mapping->session_name = 'Evening';
                                                        return true;
                                                    }
                                                }
                                                return false;
                                            });
                                            
                                            $isFirstSlot = false;
                                            if($lesson) {
                                                $lessonStartMinute = \Carbon\Carbon::parse($lesson->session_start_time)->hour * 60 + \Carbon\Carbon::parse($lesson->session_start_time)->minute;
                                                if($lessonStartMinute == $minute) {
                                                    $isFirstSlot = true;
                                                }
                                            }
                                        @endphp

                                        @if ($isFirstSlot)
                                             @include('partials.timetable.slot', ['lesson' => $lesson, 'mobile' => true])
                                        @elseif (!$lesson)
                                            <td></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-center text-muted small">
                    <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to view full schedule
                </div>
            </div>
        @elseif(request('school_id') && request('programme_id') && request('level'))
             <div class="text-center mt-5 text-muted">
                <h4>No timetable available for the selected criteria.</h4>
            </div>
        @endif
    </div>

</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form.filter-form');
        const selects = form.querySelectorAll('select[required]');

        selects.forEach(select => {
            select.addEventListener('change', function() {
                const allFilled = Array.from(selects).every(s => s.value);
                if (allFilled) {
                    form.submit();
                }
            });
        });

        // Initial setup calls
        filterProgrammes();
        checkTimetableLoaded();

        // Add event listener for school dropdown
        document.getElementById('school_id').addEventListener('change', filterProgrammes);
    });

    function filterProgrammes() {
        const schoolId = document.getElementById('school_id').value;
        const programmeSelect = document.getElementById('programme_id');
        const options = programmeSelect.getElementsByTagName('option');

        // Always show the placeholder
        options[0].style.display = ''; 
        
        // Loop through programme options and show/hide based on selected school
        for (let i = 1; i < options.length; i++) {
            const optionSchoolId = options[i].getAttribute('data-school');
            if (schoolId === '' || optionSchoolId === schoolId) {
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
        
        // If the currently selected programme is now hidden, reset the dropdown
        if (programmeSelect.value && programmeSelect.options[programmeSelect.selectedIndex].style.display === 'none') {
            programmeSelect.value = '';
        }
    }

    function checkTimetableLoaded() {
        const schoolId = document.getElementById('school_id').value;
        const programmeId = document.getElementById('programme_id').value;
        const level = document.getElementById('level').value;
        const exportBtn = document.getElementById('exportPdfBtn');

        if (schoolId && programmeId && level) {
            // Update export button href with current parameters
            const url = new URL(exportBtn.href.split('?')[0]); // Base URL
            url.searchParams.set('school_id', schoolId);
            url.searchParams.set('programme_id', programmeId);
            url.searchParams.set('level', level);
            exportBtn.href = url.toString();

            document.body.classList.add('timetable-loaded');
        } else {
            document.body.classList.remove('timetable-loaded');
        }
    }
</script>

</html>