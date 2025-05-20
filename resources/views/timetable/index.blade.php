<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timetable</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        /* .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        } */

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            color: #037b90;
            font-size: 28px;
            font-weight: bold;
        }

        .filter-section {
            padding: 3rem 2rem;
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), 
                        url('https://events.snap.co.ke/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            border-radius: 0;
            margin: 0 0 2rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
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
            display: none; /* Hide the labels as we'll use placeholders */
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

        .form-control::placeholder {
            color: #adb5bd;
            font-weight: 400;
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
            display: none; /* Hidden by default */
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
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='rgba(255,255,255,0.7)' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 14px 10px;
            padding-right: 3rem;
            cursor: pointer;
        }

        /* Add a subtle animation to the dropdown arrow */
        select.form-control:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        }

        /* Add a subtle focus ring for better accessibility */
        *:focus {
            outline: none;
        }

        /* Add a subtle scale effect on form focus */
        .form-group {
            transition: transform 0.3s ease;
        }

        .form-group:focus-within {
            transform: scale(1.005);
        }

        /* Style the select dropdown options */
        select option {
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            transition: all 0.2s ease;
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
        
        /* Add a title to the filter section */
        .filter-title {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Add a subtle animation to the form */
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
            align-items: left;
            /* Centers content */
            text-align: left;
            gap: 8px;
        }

        .instructor-img-container {
            margin-bottom: 5px;
            /* Moves image slightly up */
        }

        .instructor-image {
            width: 80px;
            /* Adjust size */
            height: 80px;
            object-fit: cover;
            /* Ensures the image fully fills the space */
            border-radius: 50%;
            /* Makes the image circular */
        }

        .lesson-details {
            width: 100%;
        }

        .instructor-name {
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .course-code {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: bold;
        }

        .course-title {
            font-style: italic;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
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
    </style>
</head>

<body>
    {{-- <div class="container mt-5"> --}}
    <div class="filter-section">
        @php
            // Group programmes by school and sort by programme_code
            $groupedProgrammes = $programmes->sortBy('programme_code')->groupBy('school_id');
        @endphp

        <div class="container">
            <h1 class="filter-title">Timetable Viewer</h1>
            
            <form method="GET" action="{{ route('timetable.index') }}" class="filter-form">
                <div class="row g-4">
                    {{-- School Dropdown --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control form-control-lg" id="school_id" name="school_id" required onchange="filterProgrammes()">
                                <option value="">Select School</option>
                                @foreach ($schools as $school)
                                    <option value="{{ $school->id }}"
                                        {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Programme Dropdown (Filtered by school) --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control form-control-lg" id="programme_id" name="programme_id" required>
                                <option value="">Select Programme</option>
                                @foreach ($groupedProgrammes as $schoolId => $schoolProgrammes)
                                    @foreach ($schoolProgrammes as $programme)
                                        <option value="{{ $programme->id }}" data-school="{{ $schoolId }}"
                                            {{ request('programme_id') == $programme->id ? 'selected' : '' }}>
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
                                    <option value="{{ $level->id }}"
                                        {{ request('level') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                {{-- Export Button (initially hidden) --}}
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="{{ route('timetable.export.pdf', request()->all()) }}" 
                           class="btn btn-danger" 
                           id="exportPdfBtn">
                            <i class="fas fa-file-pdf me-2"></i> Export Timetable as PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($timetable->isNotEmpty())
        <div class="mt-5">
            @php
                $selectedProgramme = request('programme_id')
                    ? $programmes->where('id', request('programme_id'))->first()
                    : null;
            @endphp
            <h4 class="d-none d-md-block">Teaching & Learning Schedule @if ($selectedProgramme)
                    - {{ $selectedProgramme->name }}
                @endif
            </h4>
            <h6 class="d-md-none">Teaching & Learning Schedule @if ($selectedProgramme)
                    - {{ $selectedProgramme->name }}
                @endif
            </h6>

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

            <!-- Mobile View -->
            <div class="d-md-none">
                <div
                    style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; -ms-overflow-style: -ms-autohiding-scrollbar;">
                    <div style="min-width: 800px;">
                        <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Time (EAT)</th>
                                    @foreach ($days as $day)
                                        <th style="width: 140px;">{{ $day->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $hasMorningSlots = $timetable
                                        ->where('morning_start_time', '!=', null)
                                        ->isNotEmpty();
                                    $startTime = $hasMorningSlots ? 8 * 60 : 15 * 60;
                                @endphp

                                @foreach (range($startTime, 20.5 * 60, 30) as $minute)
                                    @php
                                        $hour = intdiv($minute, 60);
                                        $min = $minute % 60;
                                        $timeLabel = $min == 0 ? sprintf('%02d:00', $hour) : '';
                                    @endphp
                                    <tr style="height: 50px;">
                                        <td style="background-color: #f8f9fa; font-size: 0.8rem;">{{ $timeLabel }}
                                        </td>
                                        @foreach ($days as $day)
                                            @php
                                                $lesson = $timetable
                                                    ->where('day_id', $day->id)
                                                    ->first(function ($mapping) use ($minute) {
                                                        if (
                                                            $mapping->morning_start_time &&
                                                            $mapping->morning_duration
                                                        ) {
                                                            $morningStart =
                                                                \Carbon\Carbon::parse($mapping->morning_start_time)
                                                                    ->hour *
                                                                    60 +
                                                                \Carbon\Carbon::parse($mapping->morning_start_time)
                                                                    ->minute;
                                                            $morningEnd = $morningStart + $mapping->morning_duration;

                                                            if ($minute >= $morningStart && $minute < $morningEnd) {
                                                                $mapping->start_time = $mapping->morning_start_time;
                                                                $mapping->duration = $mapping->morning_duration;
                                                                $mapping->session = 'Morning';
                                                                return true;
                                                            }
                                                        }

                                                        if (
                                                            $mapping->evening_start_time &&
                                                            $mapping->evening_duration
                                                        ) {
                                                            $eveningStart =
                                                                \Carbon\Carbon::parse($mapping->evening_start_time)
                                                                    ->hour *
                                                                    60 +
                                                                \Carbon\Carbon::parse($mapping->evening_start_time)
                                                                    ->minute;
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
                                                @include('partials.timetable.slot', [
                                                    'lesson' => $lesson,
                                                    'mobile' => true,
                                                ])
                                            @else
                                                <td></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-2 text-center text-muted small">
                    <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to view full schedule
                </div>
            </div>
        </div>
    @endif
    {{-- </div> --}}
</body>

<script>
    // Auto-submit form when any selection changes
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const selects = form.querySelectorAll('select');
        
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // Only submit if all required fields are filled
                const allFilled = Array.from(selects).every(s => !s.required || s.value);
                if (allFilled) {
                    form.submit();
                }
            });
        });
    });
    
    function checkTimetableLoaded() {
        const schoolId = document.getElementById('school_id').value;
        const programmeId = document.getElementById('programme_id').value;
        const level = document.getElementById('level').value;
        const exportBtn = document.getElementById('exportPdfBtn');
        
        if (schoolId && programmeId && level) {
            // Update export button href with current parameters
            const url = new URL(exportBtn.href);
            url.searchParams.set('school_id', schoolId);
            url.searchParams.set('programme_id', programmeId);
            url.searchParams.set('level', level);
            exportBtn.href = url.toString();
            
            // Show export button with animation
            document.body.classList.add('timetable-loaded');
        } else {
            // Hide export button
            document.body.classList.remove('timetable-loaded');
        }
    }
    
    function filterProgrammes() {
        const schoolId = document.getElementById('school_id').value;
        const programmeSelect = document.getElementById('programme_id');
        const options = programmeSelect.getElementsByTagName('option');

        // Show all options first
        for (let i = 0; i < options.length; i++) {
            options[i].style.display = '';
        }

        // Hide options that don't belong to the selected school
        if (schoolId) {
            for (let i = 1; i < options.length; i++) {
                const optionSchoolId = options[i].getAttribute('data-school');
                if (optionSchoolId && optionSchoolId !== schoolId) {
                    options[i].style.display = 'none';
                }
            }
            // Reset the selected value if it's now hidden
            if (programmeSelect.value) {
                const selectedOption = programmeSelect.options[programmeSelect.selectedIndex];
                if (selectedOption.style.display === 'none') {
                    programmeSelect.value = '';
                }
            }
        }
    }
</script>

</html>
