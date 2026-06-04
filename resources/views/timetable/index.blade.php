<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timetable</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-bg: #f4f6f8;
            --card-bg: #ffffff;
            --text-main: #212529;
            --text-muted: #6c757d;
            --accent-primary: #037b90;
            --accent-secondary: #ff7f50;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Inter', sans-serif !important;
            background-color: var(--primary-bg);
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        /* Flat UI Filter Section */
        .filter-section {
            background-color: var(--accent-primary);
            padding: 2rem 1rem;
            margin-bottom: 2rem;
            border-bottom: 4px solid var(--accent-secondary);
        }

        .form-label {
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tom Select Customization - Flat UI & Visible Fonts */
        .ts-wrapper.form-select {
            padding: 0;
            border: none;
            box-shadow: none;
            background: none;
        }
        .ts-control {
            background-color: #ffffff !important;
            border: 2px solid #dee2e6 !important;
            border-radius: 4px !important; /* Flatter corners */
            min-height: 48px;
            font-size: 1rem;
            color: #212529 !important;
            display: flex;
            align-items: center;
        }
        .ts-control input {
            color: #212529 !important;
            font-size: 1rem;
        }
        .ts-control input::placeholder {
            color: #6c757d !important;
        }
        .ts-control .item {
            color: #212529 !important;
        }
        .ts-control.focus {
            border-color: var(--accent-secondary);
            background-color: #ffffff;
            box-shadow: none; /* Removed glow for flat UI */
        }
        .ts-dropdown {
            border-radius: 4px;
            border: 1px solid var(--border-color);
            margin-top: 4px;
            box-shadow: none; /* Flat UI */
            background-color: #ffffff;
            color: #212529;
        }
        .ts-dropdown .option {
            color: #212529;
        }
        .ts-dropdown .active {
            background-color: #e9ecef;
            color: #212529;
        }

        /* Buttons - Flat UI */
        .btn-modern {
            border-radius: 4px; /* Flat UI */
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: none !important; /* Flat UI */
        }

        .btn-primary-modern {
            background-color: var(--accent-primary);
            color: white;
            border: 2px solid var(--accent-primary);
        }

        .btn-primary-modern:hover {
            background-color: #026070; /* Darker shade */
            border-color: #026070;
            color: white;
        }

        .btn-outline-modern {
            background-color: transparent;
            color: var(--accent-primary);
            border: 2px solid var(--accent-primary);
        }

        .btn-outline-modern:hover {
            background-color: var(--accent-primary);
            color: white;
        }
        
        .btn-secondary-modern {
            background-color: var(--accent-secondary);
            color: white;
            border: 2px solid var(--accent-secondary);
        }
        .btn-secondary-modern:hover {
            background-color: #e66c40; /* Darker shade */
            border-color: #e66c40;
            color: white;
        }

        /* Timetable Grid */
        .timetable-container {
            background: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 3rem;
            border: 1px solid var(--border-color);
            box-shadow: none; /* Flat UI */
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            width: 100%;
        }

        .table th, .table td {
            border-color: var(--border-color);
        }

        .table thead th {
            background-color: var(--primary-bg);
            color: var(--accent-primary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
            border-bottom: 2px solid var(--border-color);
            text-align: center;
        }

        .table tbody td {
            padding: 0;
            position: relative;
            vertical-align: top;
            height: 60px;
        }

        .table tbody td.time-cell {
            background-color: var(--primary-bg);
            color: var(--text-main);
            font-weight: 600;
            font-size: 0.85rem;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid var(--border-color);
        }

        /* Empty State */
        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            max-width: 800px;
            margin: 0 auto 4rem auto;
            box-shadow: none;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--accent-primary);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .page-header {
            color: var(--accent-primary);
            font-weight: 700;
            font-size: 1.4rem;
        }
    </style>
</head>

<body>
    <div class="filter-section">
        @php
            $groupedProgrammes = $programmes->sortBy('programme_code')->groupBy('school_id');
        @endphp

        <div class="container">
            <form method="GET" action="{{ route('timetable.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    {{-- School Dropdown --}}
                    <div class="col-md-4">
                        <label for="school_id" class="form-label">School</label>
                        <select class="form-select" id="school_id" name="school_id" required>
                            <option value="">Select School</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Programme Dropdown --}}
                    <div class="col-md-5">
                        <label for="programme_id" class="form-label">Programme</label>
                        <select class="form-select" id="programme_id" name="programme_id" required>
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

                    {{-- Level of Study --}}
                    <div class="col-md-3">
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="">Select Level</option>
                            @php
                                $selectedProgrammeId = request('programme_id');
                                $availableLevels = [];
                                
                                if ($selectedProgrammeId) {
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
            </form>
        </div>
    </div>

    <div class="container-fluid px-md-4 px-2 pb-5">
        @if ($timetable->isNotEmpty())
            @php
                $selectedProgramme = request('programme_id') ? $programmes->where('id', request('programme_id'))->first() : null;
                
                $levelDisplay = '';
                if (request('level')) {
                    list($yearId, $semesterId) = explode('.', request('level'));
                    $levelDisplay = "({$yearId}.{$semesterId})";
                }
                
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
                
                $startHour = $earliestTime ? $earliestTime->hour : 8;
                $startTimeInMinutes = $startHour * 60;
            @endphp
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <h6 class="page-header mb-0">
                    Teaching &amp; Learning Schedule <br>
                    <small class="text-muted fs-6 fw-normal">{{ $selectedProgramme->name ?? 'Selected Programme' }} {{ $levelDisplay }}</small>
                </h6>
                <div class="d-flex gap-3 mt-3 mt-md-0">
                    <a href="{{ route('timetable.export.pdf', request()->all()) }}" class="btn btn-outline-modern btn-modern">
                        <i class="fas fa-file-pdf text-danger"></i> Export PDF
                    </a>
                    <button type="button" class="btn btn-primary-modern btn-modern" data-bs-toggle="modal" data-bs-target="#googleCalendarModal">
                        <i class="fab fa-google"></i> Sync to Google Calendar
                    </button>
                </div>
            </div>

            <div class="timetable-container d-none d-md-block">
                <table class="table table-bordered m-0">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Time (EAT)</th>
                            @foreach ($days as $day)
                                <th style="width: 18.4%;">{{ $day->name }}</th>
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

            <div class="timetable-container d-md-none">
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
                <div class="mt-2 text-center text-muted small pb-3">
                    <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to view full schedule
                </div>
            </div>
        @elseif(request('school_id') && request('programme_id') && request('level'))
             <div class="empty-state">
                <i class="fas fa-calendar-times empty-state-icon text-muted"></i>
                <h3>No Timetable Found</h3>
                <p>We couldn't find a published timetable for the selected criteria. Please verify your selection or contact your administrator.</p>
            </div>
        @else
            {{-- ── Default Landing State ── --}}
            <div class="tt-landing">

                {{-- Hero --}}
                <div class="tt-hero">
                    <div class="tt-hero-badge">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ now()->format('l, d M Y') }}
                    </div>
                    <h1 class="tt-hero-title">OUK <span>Class Timetable</span></h1>
                    <p class="tt-hero-sub">
                        Find your personal teaching &amp; learning schedule — select your school, programme, and level above to get started.
                    </p>

                    {{-- Stats row --}}
                    <div class="tt-stats">
                        <div class="tt-stat">
                            <span class="tt-stat-num">{{ $schools->count() }}</span>
                            <span class="tt-stat-label">Schools</span>
                        </div>
                        <div class="tt-stat-divider"></div>
                        <div class="tt-stat">
                            <span class="tt-stat-num">{{ $programmes->count() }}</span>
                            <span class="tt-stat-label">Programmes</span>
                        </div>
                        <div class="tt-stat-divider"></div>
                        <div class="tt-stat">
                            <span class="tt-stat-num">{{ \App\Models\AcademicSession::where('status','active')->value('name') ?? '—' }}</span>
                            <span class="tt-stat-label">Active Session</span>
                        </div>
                    </div>
                </div>

                {{-- How-to steps --}}
                <div class="tt-steps-title">How to view your schedule</div>
                <div class="tt-steps">
                    <div class="tt-step">
                        <div class="tt-step-icon" style="background:rgba(3,123,144,0.1);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#037b90" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="tt-step-num">1</div>
                        <h4>Select School</h4>
                        <p>Choose the school or faculty you belong to from the School dropdown above.</p>
                    </div>
                    <div class="tt-step-arrow">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                    <div class="tt-step">
                        <div class="tt-step-icon" style="background:rgba(255,127,80,0.1);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#ff7f50" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        </div>
                        <div class="tt-step-num" style="background:#ff7f50;">2</div>
                        <h4>Pick Programme</h4>
                        <p>Select your programme from the filtered list — start typing to search.</p>
                    </div>
                    <div class="tt-step-arrow">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                    <div class="tt-step">
                        <div class="tt-step-icon" style="background:rgba(3,123,144,0.1);">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#037b90" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="tt-step-num">3</div>
                        <h4>Choose Level</h4>
                        <p>Pick your year and semester — the timetable loads automatically.</p>
                    </div>
                </div>

                {{-- Info cards --}}
                <div class="tt-cards">
                    <div class="tt-card tt-card--teal">
                        <div class="tt-card-icon">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </div>
                        <h5>Live Timetable</h5>
                        <p>Schedules reflect the latest updates from the Academic Office in real time.</p>
                    </div>
                    <div class="tt-card tt-card--coral">
                        <div class="tt-card-icon">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <h5>Morning &amp; Evening</h5>
                        <p>Both morning (8am–1pm) and evening (5pm–9pm) sessions are displayed side by side.</p>
                    </div>
                    <div class="tt-card tt-card--teal">
                        <div class="tt-card-icon">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <h5>Export &amp; Sync</h5>
                        <p>Download a PDF of your schedule or sync it directly into Google Calendar.</p>
                    </div>
                </div>

            </div>

            <style>
                .tt-landing { padding: 0 1rem 4rem; }

                /* Hero */
                .tt-hero {
                    background: linear-gradient(135deg, #037b90 0%, #024d5c 100%);
                    border-radius: 20px;
                    padding: 52px 48px;
                    text-align: center;
                    color: #fff;
                    margin-bottom: 48px;
                    position: relative;
                    overflow: hidden;
                }

                .tt-hero::before {
                    content: '';
                    position: absolute;
                    top: -60px; right: -60px;
                    width: 260px; height: 260px;
                    border-radius: 50%;
                    background: rgba(255,255,255,0.05);
                }

                .tt-hero::after {
                    content: '';
                    position: absolute;
                    bottom: -40px; left: -40px;
                    width: 180px; height: 180px;
                    border-radius: 50%;
                    background: rgba(255,255,255,0.04);
                }

                .tt-hero-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    background: rgba(255,255,255,0.12);
                    border: 1px solid rgba(255,255,255,0.2);
                    border-radius: 100px;
                    padding: 6px 16px;
                    font-size: 13px;
                    color: rgba(255,255,255,0.9);
                    margin-bottom: 24px;
                    position: relative;
                    z-index: 1;
                }

                .tt-hero-title {
                    font-size: clamp(2rem, 5vw, 3rem);
                    font-weight: 800;
                    margin: 0 0 16px;
                    position: relative;
                    z-index: 1;
                    line-height: 1.15;
                }

                .tt-hero-title span { color: #ff7f50; }

                .tt-hero-sub {
                    font-size: 16px;
                    color: rgba(255,255,255,0.75);
                    max-width: 520px;
                    margin: 0 auto 36px;
                    line-height: 1.7;
                    position: relative;
                    z-index: 1;
                }

                /* Stats */
                .tt-stats {
                    display: inline-flex;
                    align-items: center;
                    gap: 0;
                    background: rgba(255,255,255,0.1);
                    border: 1px solid rgba(255,255,255,0.15);
                    border-radius: 14px;
                    padding: 20px 32px;
                    position: relative;
                    z-index: 1;
                }

                .tt-stat { text-align: center; padding: 0 24px; }

                .tt-stat-num {
                    display: block;
                    font-size: 1.6rem;
                    font-weight: 800;
                    color: #fff;
                    line-height: 1;
                    margin-bottom: 4px;
                }

                .tt-stat-label {
                    font-size: 12px;
                    color: rgba(255,255,255,0.6);
                    text-transform: uppercase;
                    letter-spacing: 0.06em;
                }

                .tt-stat-divider {
                    width: 1px;
                    height: 40px;
                    background: rgba(255,255,255,0.2);
                }

                /* Steps */
                .tt-steps-title {
                    font-size: 13px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                    color: #6b7280;
                    text-align: center;
                    margin-bottom: 20px;
                }

                .tt-steps {
                    display: flex;
                    align-items: flex-start;
                    justify-content: center;
                    gap: 0;
                    flex-wrap: wrap;
                    margin-bottom: 48px;
                }

                .tt-step {
                    flex: 0 0 220px;
                    text-align: center;
                    padding: 28px 20px;
                    background: #fff;
                    border-radius: 16px;
                    border: 1px solid #e5e7eb;
                    position: relative;
                    transition: transform 0.2s, box-shadow 0.2s;
                }

                .tt-step:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 12px 32px rgba(0,0,0,0.08);
                }

                .tt-step-icon {
                    width: 52px; height: 52px;
                    border-radius: 14px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 16px;
                }

                .tt-step-num {
                    position: absolute;
                    top: -10px; right: -10px;
                    width: 24px; height: 24px;
                    border-radius: 50%;
                    background: #037b90;
                    color: #fff;
                    font-size: 12px;
                    font-weight: 700;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .tt-step h4 {
                    font-size: 15px;
                    font-weight: 700;
                    color: #111827;
                    margin: 0 0 8px;
                }

                .tt-step p {
                    font-size: 13px;
                    color: #6b7280;
                    margin: 0;
                    line-height: 1.6;
                }

                .tt-step-arrow {
                    display: flex;
                    align-items: center;
                    padding: 0 8px;
                    margin-top: 26px;
                }

                @media (max-width: 700px) {
                    .tt-step-arrow { display: none; }
                    .tt-step { flex: 0 0 100%; }
                    .tt-stats { flex-direction: column; gap: 12px; }
                    .tt-stat-divider { width: 80px; height: 1px; }
                    .tt-hero { padding: 36px 24px; }
                }

                /* Info cards */
                .tt-cards {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                    gap: 20px;
                }

                .tt-card {
                    border-radius: 16px;
                    padding: 28px 24px;
                    border: 1px solid transparent;
                    transition: transform 0.2s, box-shadow 0.2s;
                }

                .tt-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 10px 28px rgba(0,0,0,0.07);
                }

                .tt-card--teal {
                    background: rgba(3,123,144,0.06);
                    border-color: rgba(3,123,144,0.12);
                }

                .tt-card--coral {
                    background: rgba(255,127,80,0.07);
                    border-color: rgba(255,127,80,0.15);
                }

                .tt-card-icon {
                    width: 40px; height: 40px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 14px;
                    background: rgba(255,255,255,0.7);
                }

                .tt-card--teal .tt-card-icon svg { stroke: #037b90; }
                .tt-card--coral .tt-card-icon svg { stroke: #ff7f50; }

                .tt-card h5 {
                    font-size: 15px;
                    font-weight: 700;
                    color: #111827;
                    margin: 0 0 8px;
                }

                .tt-card p {
                    font-size: 13px;
                    color: #6b7280;
                    margin: 0;
                    line-height: 1.6;
                }
            </style>
        @endif
    </div>

    <!-- Google Calendar Sync Modal -->
    <div class="modal fade" id="googleCalendarModal" tabindex="-1" aria-labelledby="googleCalendarModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
          <div class="modal-header" style="border-bottom: 1px solid var(--border-color); padding: 1.5rem;">
            <h5 class="modal-title fw-bold" id="googleCalendarModalLabel"><i class="fab fa-google text-primary me-2"></i>Sync with Google Calendar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" style="padding: 1.5rem;">
            <p class="text-muted mb-4">To sync your recurring timetable with Google Calendar, follow these simple steps:</p>
            <ol class="list-group list-group-numbered list-group-flush mb-4" style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                <li class="list-group-item bg-transparent" style="padding: 12px 0;"><strong>Download</strong> the timetable (.ics) file using the button below.</li>
                <li class="list-group-item bg-transparent" style="padding: 12px 0;">Open <a href="https://calendar.google.com" target="_blank" class="text-primary text-decoration-none fw-semibold">Google Calendar</a> on your computer.</li>
                <li class="list-group-item bg-transparent" style="padding: 12px 0;">In the top right, click <strong>Settings (⚙️) > Settings</strong>.</li>
                <li class="list-group-item bg-transparent" style="padding: 12px 0;">On the left panel, click <strong>Import & export</strong>.</li>
                <li class="list-group-item bg-transparent" style="padding: 12px 0;">Select the downloaded file and click <strong>Import</strong>.</li>
            </ol>
            <div class="d-grid gap-2">
                <a href="{{ route('timetable.sync.google', request()->all()) }}" class="btn btn-primary-modern btn-modern py-3 justify-content-center">
                    <i class="fas fa-download"></i> Download .ics File
                </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        let schoolTs, programmeTs, levelTs;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Tom Select on all filters
            schoolTs = new TomSelect('#school_id', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Search & Select School'
            });

            programmeTs = new TomSelect('#programme_id', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Search & Select Programme'
            });

            levelTs = new TomSelect('#level', {
                create: false,
                placeholder: 'Search & Select Level'
            });

            const form = document.querySelector('form.filter-form');

            // Auto-submit when all three have values
            const checkAndSubmit = () => {
                if (schoolTs.getValue() && programmeTs.getValue() && levelTs.getValue()) {
                    form.submit();
                }
            };

            schoolTs.on('change', function(value) {
                filterProgrammes(value);
            });
            
            programmeTs.on('change', function(value) {
                updateLevels(value);
                checkAndSubmit();
            });

            levelTs.on('change', function(value) {
                checkAndSubmit();
            });

            // Initial load handling based on URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const schoolId = urlParams.get('school_id');
            const programmeId = urlParams.get('programme_id');
            const levelId = urlParams.get('level');

            if (schoolId && programmeId) {
                // If everything is present in URL, the blade template already rendered options and selected them.
                // Tom select picked up on those selected values automatically.
                // We don't need to fetch API here since it's already rendered server side
            } else if (schoolId) {
                filterProgrammes(schoolId);
            }
        });

        function filterProgrammes(schoolId) {
            if (!schoolId) {
                programmeTs.clear();
                programmeTs.clearOptions();
                levelTs.clear();
                levelTs.clearOptions();
                return;
            }
            
            programmeTs.disable();
            programmeTs.clearOptions();
            programmeTs.addOption({value: '', text: 'Loading programmes...'});
            
            levelTs.disable();
            levelTs.clearOptions();
            
            fetch(`/api/programmes-by-school?school_id=${schoolId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    programmeTs.clearOptions();
                    
                    if (data.programmes && data.programmes.length > 0) {
                        data.programmes.forEach(programme => {
                            programmeTs.addOption({
                                value: programme.id,
                                text: `${programme.programme_code} - ${programme.name}`
                            });
                        });
                    } else {
                        programmeTs.addOption({value: '', text: 'No programmes found'});
                    }
                    
                    programmeTs.enable();
                    
                    const urlParams = new URLSearchParams(window.location.search);
                    const programmeId = urlParams.get('programme_id');
                    if (programmeId) {
                        programmeTs.setValue(programmeId, true); // true silences the event
                    }
                })
                .catch(error => {
                    console.error('Error fetching programmes:', error);
                    programmeTs.clearOptions();
                    programmeTs.addOption({value: '', text: 'Error loading programmes'});
                    programmeTs.enable();
                });
        }
        
        function updateLevels(programmeId) {
            const schoolId = schoolTs.getValue();
            
            if (!schoolId || !programmeId) {
                levelTs.clear();
                levelTs.clearOptions();
                levelTs.disable();
                return;
            }
            
            levelTs.disable();
            levelTs.clearOptions();
            levelTs.addOption({value: '', text: 'Loading levels...'});
            
            fetch(`/api/levels-with-timetables?school_id=${schoolId}&programme_id=${programmeId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    levelTs.clearOptions();
                    
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(level => {
                            levelTs.addOption({
                                value: level.id,
                                text: level.name
                            });
                        });
                        
                        const urlParams = new URLSearchParams(window.location.search);
                        const currentLevel = urlParams.get('level');
                        if (currentLevel) {
                            levelTs.setValue(currentLevel, true);
                        }
                    } else {
                        levelTs.addOption({value: '', text: 'No levels found'});
                    }
                    
                    levelTs.enable();
                })
                .catch(error => {
                    console.error('Error fetching levels:', error);
                    levelTs.clearOptions();
                    levelTs.addOption({value: '', text: 'Error loading levels.'});
                    levelTs.enable();
                });
        }
    </script>
</body>
</html>