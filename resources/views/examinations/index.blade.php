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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header-section {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 3px solid #0d6efd;
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
        <!-- Filters -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-light">
                <form action="{{ route('examinations.index') }}" method="GET" id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="school" class="form-label fw-bold small text-uppercase text-muted">Filter by School</label>
                        <select name="school" id="school" class="form-select">
                            <option value="">All Schools</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="programme" class="form-label fw-bold small text-uppercase text-muted">Programme</label>
                        <select name="programme" id="programme" class="form-select">
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
                    <div class="col-md-2">
                         <label for="level" class="form-label fw-bold small text-uppercase text-muted">Level</label>
                         <select name="level" id="level" class="form-select">
                            <option value="">All Levels</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                         </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label fw-bold small text-uppercase text-muted">From Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label fw-bold small text-uppercase text-muted">To Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('examinations.index') }}" class="btn btn-outline-secondary w-100" title="Reset Filters">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

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
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%">Time (Start - End)</th>
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
                                                // Get all programmes or filter if one is selected
                                                $programmes = $exam->courseUnit->programmes;
                                                
                                                if(request('programme')) {
                                                    $programmes = $programmes->where('id', request('programme'));
                                                }
                                                
                                                $programmeNames = $programmes->pluck('name')->unique();
                                            @endphp
                                            @if($programmeNames->count() > 0)
                                                @foreach($programmeNames as $name)
                                                    <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle mb-1">
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
                                        No exams scheduled yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-muted small text-center">
                Generated at {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    <!-- Filter Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schoolSelect = document.getElementById('school');
            const programmeSelect = document.getElementById('programme');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');

            const validMappings = @json($validMappings);

            // 1. Handle School -> Programme dependency
            function filterProgrammes() {
                const selectedSchoolId = schoolSelect.value;
                const options = programmeSelect.querySelectorAll('option');
                const groups = programmeSelect.querySelectorAll('optgroup');

                // Reset selection if the currently selected programme doesn't belong to the new school
                const currentProgOption = programmeSelect.options[programmeSelect.selectedIndex];
                if (selectedSchoolId && currentProgOption && currentProgOption.value && currentProgOption.dataset.school !== selectedSchoolId) {
                    programmeSelect.value = "";
                }

                // Show/Hide Optgroups and Options within them
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
                const levelOptions = document.getElementById('level').querySelectorAll('option');
                
                // If specific programme selected, only show levels valid for it
                if (selectedProgrammeId && validMappings[selectedProgrammeId]) {
                    const availableLevels = validMappings[selectedProgrammeId].map(m => m.level_id);
                    
                    levelOptions.forEach(opt => {
                        if (!opt.value) return; // Keep "All Levels"
                        if (availableLevels.includes(opt.value)) {
                            opt.hidden = false;
                            opt.disabled = false;
                        } else {
                            opt.hidden = true;
                            opt.disabled = true; // Disable to prevent selection
                        }
                    });

                    // Deselect if current level is now invalid
                    const currentLevel = document.getElementById('level').value;
                    if (currentLevel && !availableLevels.includes(currentLevel)) {
                        document.getElementById('level').value = "";
                    }
                } else {
                    // If no programme selected, show all levels? Or only levels that exist in GENERAL?
                    // User said "check if the levels do not yet exist do not display them"
                    // Better to just show all POSSIBLE levels if no programme is selected, 
                    // or ideally show distinct levels from ALL valid mappings.
                    // Let's reset to show all for now to avoid confusion.
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
                filterProgrammes();
                // When school changes, programme might reset, so level options might need update?
                // But form auto-submits, so page reload handles state. 
                // However, user might change school without submitting if we removed auto-submit?
                // Current logic: Change -> filterForm.submit().
                // So client-side filtering is mainly for the split second before reload 
                // OR if we remove auto-submit. 
                // Wait, logic says: change -> submit.
                // But "filterLevels" needs to run on load to set correct state.
                filterForm.submit();
            });

            document.getElementById('level').addEventListener('change', function() {
                filterForm.submit();
            });

            programmeSelect.addEventListener('change', function() {
                // filterLevels(); // Not strictly needed if we submit immediately
                filterForm.submit();
            });
            
            // ... dates ...


            startDateInput.addEventListener('change', function() {
                filterForm.submit();
            });

            endDateInput.addEventListener('change', function() {
                filterForm.submit();
            });

            // Client-side Search (Active)
            searchInput.addEventListener('keyup', function() {
                var input = this.value.toLowerCase();
                var rows = document.querySelectorAll('#examsTable tbody tr');
                
                rows.forEach(function(row) {
                    // Skip boundary rows (date headers)
                    if (row.classList.contains('boundary-row')) return;

                    var text = row.innerText.toLowerCase();
                    var shouldShow = text.includes(input);
                    row.style.display = shouldShow ? '' : 'none';
                    
                    // Logic to hide/show boundary rows if all children are hidden?
                    // For simplicity, we keep headers or hide them if we want advanced logic.
                    // Let's just filter the exam rows for now.
                });
            });
        });
    </script>
</body>
</html>
