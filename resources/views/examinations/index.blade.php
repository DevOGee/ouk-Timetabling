<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Timetable - {{ config('app.name') }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --uni-teal: #037b90;
            --uni-teal-hover: #025f70;
            --uni-gold: #ff7f50;
            --uni-bg: #f5f7f9;
        }
        body {
            background-color: var(--uni-bg);
            font-family: 'Outfit', sans-serif;
        }
        .header-section {
            background-color: var(--uni-teal);
            color: #ffffff;
            background: linear-gradient(135deg, var(--uni-teal) 0%, #025f70 100%);
            box-shadow: 0 4px 12px rgba(3, 123, 144, 0.15);
            padding: 3rem 0;
            margin-bottom: 2.5rem;
            border-bottom: 5px solid var(--uni-gold);
        }
        .header-section .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .text-primary {
            color: var(--uni-teal) !important;
        }
        .btn-uni-primary {
            background-color: var(--uni-teal);
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .btn-uni-primary:hover {
            background-color: var(--uni-teal-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .card {
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.08) !important;
        }
        .card-header {
            background-color: #ffffff;
            color: var(--uni-teal);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            border-radius: 12px 12px 0 0 !important;
        }
        #examsTable th {
            background-color: var(--uni-teal);
            color: #ffffff;
            font-weight: 500;
            border-bottom: none;
            padding: 1rem;
        }
        .table-secondary.boundary-row td {
            background-color: rgba(255, 127, 80, 0.1) !important;
            color: var(--uni-teal);
            border-bottom: 2px solid rgba(255, 127, 80, 0.2);
        }
        .badge-uni {
            background-color: rgba(3, 123, 144, 0.1);
            color: var(--uni-teal);
            border: 1px solid rgba(3, 123, 144, 0.2);
            font-weight: 500;
        }
        .badge.bg-secondary {
            background-color: var(--uni-gold) !important;
            color: #fff;
        }
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(3, 123, 144, 0.3);
            border-radius: 4px;
        }
        .btn-outline-secondary {
            color: var(--uni-teal);
            border-color: rgba(3, 123, 144, 0.3);
        }
        .btn-outline-secondary:hover {
            background-color: rgba(3, 123, 144, 0.1);
            color: var(--uni-teal);
            border-color: var(--uni-teal);
        }
    </style>
</head>
<body>

    <div class="header-section text-center">
        <div class="container">
            <h1 class="fw-bold mb-2">{{ $activeSchedule->name }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar-range me-1"></i>
                {{ $activeSchedule->start_date->format('d M Y') }} - {{ $activeSchedule->end_date->format('d M Y') }}
            </p>
            <p class="text-muted small">Academic Session: {{ $activeSchedule->academicSession->name }}</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <!-- Filters & Calendar Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-funnel me-1"></i> Filters
                    </div>
                    <div class="card-body bg-light">
                        <form action="{{ route('examinations.index') }}" method="GET" id="filterForm">
                            <!-- Hidden date inputs populated by calendar -->
                            <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="view" id="view_input" value="{{ request('view') }}">
                            <input type="hidden" name="page" id="page_input" value="{{ request('page', 1) }}">

                            <div class="mb-3">
                                <label for="school" class="form-label small text-muted text-uppercase fw-bold">School</label>
                                <select name="school" id="school" class="form-select form-select-sm">
                                    <option value="">All Schools</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}" {{ request('school') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="programme" class="form-label small text-muted text-uppercase fw-bold">Programme</label>
                                <select name="programme" id="programme" class="form-select form-select-sm">
                                    <option value="">All Programmes</option>
                                    @foreach($schools as $school)
                                        <optgroup label="{{ $school->name }}" data-school="{{ $school->id }}">
                                            @foreach($school->programmes as $prog)
                                                <option value="{{ $prog->id }}" 
                                                    {{ request('programme') == $prog->id ? 'selected' : '' }}
                                                    data-school="{{ $school->id }}">
                                                    {{ $prog->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="level" class="form-label small text-muted text-uppercase fw-bold">Level</label>
                                <select name="level" id="level" class="form-select form-select-sm">
                                    <option value="">All Levels</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('examinations.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle"></i> Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Calendar Widget -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar3 me-1"></i> {{ now()->format('F Y') }}</span>
                        <!-- Simple Nav (Could be enhanced later) -->
                    </div>
                    <div class="card-body p-2">
                        @php
                            $currentMonth = now()->startOfMonth();
                            $daysInMonth = $currentMonth->daysInMonth;
                            $startDayOfWeek = $currentMonth->dayOfWeek; // 0 (Sun) - 6 (Sat)
                            $today = now()->format('Y-m-d');
                            $selectedDate = request('start_date');
                            
                            $schedStart = $activeSchedule->start_date->format('Y-m-d');
                            $schedEnd = $activeSchedule->end_date->format('Y-m-d');
                            
                            // Determine the currently viewed date from the pagination
                            $currentPageItem = $paginatedDates->first();
                            $currentViewDate = null;
                            if($currentPageItem) {
                                $currentViewDate = is_object($currentPageItem) ? $currentPageItem->format('Y-m-d') : $currentPageItem;
                            }
                        @endphp
                        <div class="d-flex text-center small fw-bold text-muted mb-2">
                            <div style="width: 14.28%">Sun</div>
                            <div style="width: 14.28%">Mon</div>
                            <div style="width: 14.28%">Tue</div>
                            <div style="width: 14.28%">Wed</div>
                            <div style="width: 14.28%">Thu</div>
                            <div style="width: 14.28%">Fri</div>
                            <div style="width: 14.28%">Sat</div>
                        </div>
                        <div class="d-flex flex-wrap text-center">
                            {{-- Empty slots for previous month --}}
                            @for($i = 0; $i < $startDayOfWeek; $i++)
                                <div style="width: 14.28%" class="p-1"></div>
                            @endfor

                            {{-- Days --}}
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $dateStr = $currentMonth->copy()->day($day)->format('Y-m-d');
                                    $hasExam = in_array($dateStr, $examDates);
                                    $isSelected = ($dateStr == $currentViewDate);
                                    $isToday = ($dateStr == $today);
                                    $isWithinRange = ($dateStr >= $schedStart && $dateStr <= $schedEnd);
                                    
                                    // Base classes
                                    $btnClasses = 'btn btn-sm w-100 p-0 d-flex align-items-center justify-content-center';
                                    $inlineStyle = 'height: 32px;';
                                    $disabledAttr = '';

                                    if (!$isWithinRange) {
                                        $btnClasses .= ' text-muted opacity-25';
                                        $disabledAttr = 'disabled';
                                    } else {
                                        if ($isToday) {
                                            $inlineStyle .= ' background-color: #037b90 !important; color: white !important; font-weight: bold; border-radius: 4px;';
                                        } 
                                        elseif ($hasExam) {
                                            $inlineStyle .= ' color: #ff7f50 !important; font-weight: 900;';
                                        } 
                                        else {
                                            $btnClasses .= ' text-dark';
                                        }

                                        // Selection indicator (Border)
                                        if ($isSelected) {
                                            $inlineStyle .= ' border: 2px solid #037b90;';
                                        }
                                    }
                                @endphp
                                <div style="width: 14.28%" class="p-1">
                                    <button type="button" 
                                        class="{{ $btnClasses }}" 
                                        style="{{ $inlineStyle }}"
                                        {{ $disabledAttr }}
                                        onclick="selectDate('{{ $dateStr }}')">
                                        {{ $day }}
                                    </button>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="card-footer bg-white p-2">
                         <div class="d-grid">
                            <button type="button" class="btn btn-uni-primary btn-sm" onclick="showAll()">
                                <i class="bi bi-collection me-1"></i> Show All Timetables
                            </button>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search by Course Code, Name, or Programme...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="examsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 20%">Time</th>
                                        <th style="width: 25%">Course</th>
                                        <th style="width: 15%">Level</th>
                                        <th style="width: 40%">Programmes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($exams as $date => $dailyExams)
                                        <tr class="table-secondary boundary-row">
                                            <td colspan="4" class="fw-bold py-3 px-3">
                                                @if($date === 'Unscheduled')
                                                    TBA / Unscheduled
                                                @else
                                                    <i class="bi bi-calendar-event me-2"></i>
                                                    {{ \Carbon\Carbon::parse($date)->format('l, jS F Y') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @foreach($dailyExams as $exam)
                                            <tr>
                                                <td class="fw-bold text-primary">
                                                    @if($exam->start_time)
                                                        {{ $exam->start_time->format('H:i') }} - 
                                                        {{ $exam->start_time->copy()->addMinutes($exam->duration_minutes)->format('H:i') }}
                                                    @else
                                                        TBA
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $exam->courseUnit->code }}</div>
                                                    <small class="text-muted">{{ $exam->courseUnit->name }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $exam->mapping->yearOfStudy->name ?? '?' }}.{{ $exam->mapping->semester->name ?? '?' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $programmes = $exam->courseUnit->programmes;
                                                        if(request('programme')) {
                                                            $programmes = $programmes->where('id', request('programme'));
                                                        }
                                                        $programmeNames = $programmes->pluck('name')->unique();
                                                    @endphp
                                                    @if($programmeNames->count() > 0)
                                                        @foreach($programmeNames as $name)
                                                            <span class="badge badge-uni mb-1">
                                                                {{ $name }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted small"><em>Not specified</em></span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-calendar-x display-6 mb-3 d-block"></i>
                                                No exams found for this selection.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pagination Removed as per request (handled by Calendar) -->
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schoolSelect = document.getElementById('school');
            const programmeSelect = document.getElementById('programme');
            const levelSelect = document.getElementById('level');
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const pageInput = document.getElementById('page_input');

            // Exam dates (All possible dates given School/Prog/Level filters)
            const examDates = @json($examDates);
            // Filtered dates (Currently visible in pagination given View/DateRange filters)
            const filteredDates = @json($filteredUniqueDates);

            // Helper to submit form without empty params
            function submitFilterForm() {
                const startDate = document.getElementById('start_date');
                const endDate = document.getElementById('end_date');
                
                if (!startDate.value) startDate.disabled = true;
                if (!endDate.value) endDate.disabled = true;
                
                filterForm.submit();
            }

            // Expose selectDate globally
            window.selectDate = function(dateStr) {
                // 1. Try to find in current filtered list
                let index = filteredDates.indexOf(dateStr);
                
                if (index !== -1) {
                    // It's in the current view, just jump to page
                    pageInput.value = index + 1;
                    submitFilterForm();
                } else {
                    // 2. Not in current view (e.g. Past date while in Upcoming view)
                    // We must switch to 'Show All' and find its index there
                    // `examDates` contains the ALL List.
                    index = examDates.indexOf(dateStr);
                    
                    if (index !== -1) {
                        // Switch to All View
                        document.getElementById('view_input').value = 'all';
                        // Clear specific date ranges so we see everything
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                        
                        pageInput.value = index + 1;
                        submitFilterForm();
                    } else {
                        console.warn('Selected date found in UI but not in logic lists:', dateStr);
                    }
                }
            }

            window.showAll = function() {
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                document.getElementById('view_input').value = 'all';
                pageInput.value = 1;
                submitFilterForm();
            }
            
            // Reset page to 1 when filters change
            function resetPage() {
                pageInput.value = 1;
            }

            const validMappings = @json($validMappings);

            // 1. Handle School -> Programme dependency
            function filterProgrammes() {
                const selectedSchoolId = schoolSelect.value;
                const options = programmeSelect.querySelectorAll('option');
                const groups = programmeSelect.querySelectorAll('optgroup');

                const currentProgOption = programmeSelect.options[programmeSelect.selectedIndex];
                if (selectedSchoolId && currentProgOption && currentProgOption.value && currentProgOption.dataset.school !== selectedSchoolId) {
                    programmeSelect.value = "";
                }

                groups.forEach(group => {
                    if (!selectedSchoolId || group.dataset.school === selectedSchoolId) {
                        group.style.display = '';
                        Array.from(group.querySelectorAll('option')).forEach(opt => opt.hidden = false);
                    } else {
                        group.style.display = 'none';
                        Array.from(group.querySelectorAll('option')).forEach(opt => opt.hidden = true);
                    }
                });
                
                options.forEach(option => {
                    if (!option.value) return; 
                    if (selectedSchoolId && option.dataset.school !== selectedSchoolId) {
                        option.hidden = true;
                    } else {
                        option.hidden = false;
                    }
                });
            }

            // 2. Handle Programme -> Level dependency
            function filterLevels() {
                const selectedProgrammeId = programmeSelect.value;
                const levelOptions = levelSelect.querySelectorAll('option');
                
                if (selectedProgrammeId && validMappings[selectedProgrammeId]) {
                    const availableLevels = validMappings[selectedProgrammeId].map(m => m.level_id);
                    
                    levelOptions.forEach(opt => {
                        if (!opt.value) return; 
                        if (availableLevels.includes(opt.value)) {
                            opt.hidden = false;
                            opt.disabled = false;
                        } else {
                            opt.hidden = true;
                            opt.disabled = true;
                        }
                    });

                    const currentLevel = levelSelect.value;
                    if (currentLevel && !availableLevels.includes(currentLevel)) {
                        levelSelect.value = "";
                    }
                } else {
                    levelOptions.forEach(opt => {
                         opt.hidden = false;
                         opt.disabled = false;
                    });
                }
            }

            // Initial run
            filterProgrammes();
            filterLevels();

            // Event Listeners
            schoolSelect.addEventListener('change', function() {
                resetPage();
                filterProgrammes();
                submitFilterForm();
            });

            levelSelect.addEventListener('change', function() {
                resetPage();
                submitFilterForm();
            });

            programmeSelect.addEventListener('change', function() {
                resetPage();
                submitFilterForm();
            });
            
            // Client-side Search (Active)
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    var input = this.value.toLowerCase();
                    var rows = document.querySelectorAll('#examsTable tbody tr');
                    
                    rows.forEach(function(row) {
                        if (row.classList.contains('boundary-row')) return;
                        var text = row.innerText.toLowerCase();
                        var shouldShow = text.includes(input);
                        row.style.display = shouldShow ? '' : 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>
