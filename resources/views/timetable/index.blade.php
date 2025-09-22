<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timetable</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Quicksand', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
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
            /* background: linear-gradient(90deg, #4361ee, #f72585); */
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
    {{-- @if($currentAcademicSession)
        <div class="container mt-3">
            <div class="alert alert-success mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-calendar-alt me-2"></i>
                        <strong>Active Academic Session:</strong> {{ $currentAcademicSession->name }}
                        <span class="badge bg-success ms-2">Active</span>
                    </div>
                    <div class="small text-muted">
                        {{ $currentAcademicSession->start_date->format('M d, Y') }} - {{ $currentAcademicSession->end_date->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

    <div class="filter-section">
        @php
            $groupedProgrammes = $programmes->sortBy('programme_code')->groupBy('school_id');
        @endphp

        <div class="container">
            {{-- <h2 class="filter-title">Timetable Viewer</h2> --}}

            <form method="GET" action="{{ route('timetable.index') }}" class="filter-form">
                <div class="d-flex align-items-end flex-wrap" style="gap: 8px;">
                    {{-- School Dropdown --}}
                    <div class="flex-grow-1" style="min-width: 200px;">
                        <div class="form-group mb-0">
                            <select class="form-control form-control-sm" id="school_id" name="school_id" required style="border-radius: 5px; height: 38px;">
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
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <div class="form-group mb-0">
                            <select class="form-control form-control-sm" id="programme_id" name="programme_id" required style="border-radius: 5px; height: 38px;">
                                <option value="">Select Programme</option>
                                @php
                                    $selectedSchoolId = request('school_id');
                                @endphp
                                @foreach ($groupedProgrammes as $schoolId => $schoolProgrammes)
                                    @foreach ($schoolProgrammes as $programme)
                                        <option value="{{ $programme->id }}" data-school="{{ $schoolId }}"
                                            {{ request('programme_id') == $programme->id ? 'selected' : '' }}
                                            data-school-id="{{ $schoolId }}"
                                            style="display: {{ (!$selectedSchoolId || $selectedSchoolId == $schoolId) ? '' : 'none' }};">
                                            {{ $programme->programme_code }} - {{ $programme->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Level of Study --}}
                    <div style="width: 150px;">
                        <div class="form-group mb-0">
                            <select class="form-control form-control-sm" id="level" name="level" required style="border-radius: 5px; height: 38px;">
                                <option value="">Select Level</option>
                                @php
                                    $selectedProgrammeId = request('programme_id');
                                    $availableLevels = [];
                                    
                                    if ($selectedProgrammeId) {
                                        // Get all year/semester combinations that have course units for the selected programme
                                        $availableLevels = \DB::table('course_unit_programme_mappings')
                                            ->where('programme_id', $selectedProgrammeId)
                                            ->select('year_of_study_id', 'semester_id')
                                            ->distinct()
                                            ->get()
                                            ->map(function($item) {
                                                return $item->year_of_study_id . '.' . $item->semester_id;
                                            })
                                            ->toArray();
                                    }
                                @endphp
                                @foreach ($levels as $level)
                                    @php
                                        $levelId = $level->year_id . '.' . $level->semester_id;
                                        $shouldShow = in_array($levelId, $availableLevels) || (!$selectedProgrammeId && empty($availableLevels));
                                    @endphp
                                    @if($shouldShow)
                                        <option value="{{ $levelId }}" 
                                                data-year-id="{{ $level->year_id }}"
                                                data-semester-id="{{ $level->semester_id }}"
                                                data-programme-id="{{ $selectedProgrammeId }}"
                                                {{ request('level') == $levelId ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Export Button --}}
                    {{-- <div>
                        <button type="button" id="exportPdfBtn" class="btn btn-success btn-sm" style="border-radius: 5px; height: 38px; white-space: nowrap;">
                            <i class="fas fa-file-export me-1"></i> Export PDF
                        </button>
                    </div> --}}
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
        const levelSelect = document.getElementById('level');
        
        if (!schoolId) {
            // If no school is selected, show all programmes and reset level
            const options = programmeSelect.getElementsByTagName('option');
            for (let i = 0; i < options.length; i++) {
                options[i].style.display = '';
            }
            levelSelect.innerHTML = '<option value="">Select Level</option>';
            return;
        }
        
        // Show loading state
        programmeSelect.disabled = true;
        programmeSelect.innerHTML = '<option value="">Loading programmes...</option>';
        
        // Clear level select
        levelSelect.innerHTML = '<option value="">Select Level</option>';
        levelSelect.disabled = true;
        
        // Fetch programmes for the selected school
        fetch(`/api/programmes-by-school?school_id=${schoolId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Repopulate programme select
                programmeSelect.innerHTML = '<option value="">Select Programme</option>';
                
                if (data.programmes && data.programmes.length > 0) {
                    data.programmes.forEach(programme => {
                        const option = document.createElement('option');
                        option.value = programme.id;
                        option.textContent = `${programme.programme_code} - ${programme.name}`;
                        programmeSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No programmes found';
                    programmeSelect.appendChild(option);
                }
                
                // Enable the select
                programmeSelect.disabled = false;
                
                // If there's a programme ID in the URL, select it
                const urlParams = new URLSearchParams(window.location.search);
                const programmeId = urlParams.get('programme_id');
                if (programmeId) {
                    programmeSelect.value = programmeId;
                    // Trigger change to update levels
                    const event = new Event('change');
                    programmeSelect.dispatchEvent(event);
                }
            })
            .catch(error => {
                console.error('Error fetching programmes:', error);
                programmeSelect.innerHTML = '<option value="">Error loading programmes</option>';
                programmeSelect.disabled = false;
            });
    }

    function checkTimetableLoaded() {
        const schoolId = document.getElementById('school_id').value;
        const programmeId = document.getElementById('programme_id').value;
        const level = document.getElementById('level').value;
        const exportBtn = document.getElementById('exportPdfBtn');
        
        // Only proceed if export button exists
        if (!exportBtn) {
            console.log('Export button not found in the DOM');
            return;
        }

        if (schoolId && programmeId && level) {
            // Enable export button and update its click handler
            exportBtn.disabled = false;
            exportBtn.onclick = function() {
                try {
                    // Get the current URL parameters
                    const params = new URLSearchParams(window.location.search);
                    
                    // Construct the export URL
                    let exportUrl = '{{ route("timetable.export.pdf") }}';
                    exportUrl += `?school_id=${schoolId}&programme_id=${programmeId}&level=${level}`;
                    
                    // Add any additional filters that might be present
                    if (params.get('campus')) {
                        exportUrl += `&campus=${params.get('campus')}`;
                    }
                    
                    // Open the export URL in a new tab
                    window.open(exportUrl, '_blank');
                } catch (error) {
                    console.error('Error in export button click handler:', error);
                }
            };

            document.body.classList.add('timetable-loaded');
        } else {
            // Disable export button if not all required fields are selected
            exportBtn.disabled = true;
            document.body.classList.remove('timetable-loaded');
        }
    }
    
    // Function to update level dropdown based on selected programme
    function updateLevels() {
        const schoolId = document.getElementById('school_id').value;
        const programmeId = document.getElementById('programme_id').value;
        const levelSelect = document.getElementById('level');
        
        // Save the current value before making changes
        const currentValue = levelSelect.value;
        
        // If no school or programme is selected, show all levels
        if (!schoolId || !programmeId) {
            // Restore original options if they exist
            const originalOptions = levelSelect.dataset.originalOptions;
            if (originalOptions) {
                levelSelect.innerHTML = originalOptions;
                
                // Try to restore the previously selected value
                if (currentValue) {
                    const optionToSelect = levelSelect.querySelector(`option[value="${currentValue}"]`);
                    if (optionToSelect) {
                        optionToSelect.selected = true;
                    }
                }
            }
            levelSelect.disabled = true;
            checkTimetableLoaded();
            return;
        }
        
        levelSelect.disabled = true;
        levelSelect.innerHTML = '<option value="">Loading levels...</option>';
        
        // Include both school_id and programme_id in the API call
        fetch(`/api/levels-with-timetables?school_id=${schoolId}&programme_id=${programmeId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Clear existing options
                levelSelect.innerHTML = '';
                
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select Level';
                levelSelect.appendChild(defaultOption);
                
                if (data.data && data.data.length > 0) {
                    // Add the levels from the API response
                    data.data.forEach(level => {
                        const option = document.createElement('option');
                        option.value = level.id; // Format: "year_id.semester_id" (e.g., "1.1")
                        option.textContent = level.name; // e.g., "Year 1 - Semester 1"
                        levelSelect.appendChild(option);
                    });
                    
                    // Try to restore the previously selected value if it exists in the new options
                    if (currentValue) {
                        const optionToSelect = levelSelect.querySelector(`option[value="${currentValue}"]`);
                        if (optionToSelect) {
                            optionToSelect.selected = true;
                        }
                    }
                } else {
                    // If no levels found, show a message
                    const noLevelsOption = document.createElement('option');
                    noLevelsOption.value = '';
                    noLevelsOption.textContent = 'No levels found';
                    levelSelect.appendChild(noLevelsOption);
                }
                
                // Enable the select
                levelSelect.disabled = false;
                
                // Update the export button state
                checkTimetableLoaded();
                
                // Trigger change event to update the timetable
                levelSelect.dispatchEvent(new Event('change'));
            })
            .catch(error => {
                console.error('Error fetching levels:', error);
                
                // Clear existing options
                levelSelect.innerHTML = '';
                
                // Add error message
                const errorOption = document.createElement('option');
                errorOption.value = '';
                errorOption.textContent = 'Error loading levels. Please try again.';
                levelSelect.appendChild(errorOption);
                
                // If we have the original levels, add them as fallback
                const originalLevels = document.querySelectorAll('#level option[data-original]');
                if (originalLevels.length > 0) {
                    originalLevels.forEach(opt => {
                        levelSelect.appendChild(opt.cloneNode(true));
                    });
                }
            });
    }
    
    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Store original level options for later use
        const levelSelect = document.getElementById('level');
        const originalLevels = levelSelect.innerHTML;
        levelSelect.setAttribute('data-original', originalLevels);
        
        // Mark all level options with data-original="true" for reference
        Array.from(levelSelect.options).forEach(option => {
            if (option.value) {
                option.setAttribute('data-original', 'true');
            }
        });
        
        // Add event listeners for form changes
        const schoolSelect = document.getElementById('school_id');
        const programmeSelect = document.getElementById('programme_id');
        
        schoolSelect.addEventListener('change', function() {
            filterProgrammes();
            checkTimetableLoaded();
        });
        
        programmeSelect.addEventListener('change', function() {
            updateLevels();
            checkTimetableLoaded();
        });
        
        levelSelect.addEventListener('change', checkTimetableLoaded);
        
        // If a school is already selected, trigger the programme load
        if (schoolSelect.value) {
            filterProgrammes();
        } else if (programmeSelect.value) {
            // If no school but a programme is selected, update levels
            updateLevels();
        }
    });
</script>

</html>