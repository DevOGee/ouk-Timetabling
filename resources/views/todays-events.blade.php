@extends('layouts.app')
@section('title', "Today's Events")

@push('styles')
<style>
:root {
    --teal:        #037b90;
    --teal-dark:   #024d5c;
    --teal-bg:     rgba(3,123,144,.1);
    --coral:       #ff7f50;
    --coral-bg:    rgba(255,127,80,.1);
    --purple:      #7c3aed;
    --purple-bg:   rgba(124,58,237,.1);
    --amber-bg:    rgba(245,158,11,.1);
    --slate-50:    #f8fafc;
    --slate-100:   #f1f5f9;
    --slate-200:   #e2e8f0;
    --slate-300:   #cbd5e1;
    --slate-500:   #64748b;
    --slate-700:   #334155;
    --slate-900:   #0f172a;
    --radius-lg:   20px;
    --radius-md:   14px;
    --radius-sm:   10px;
    --card-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 20px rgba(15,23,42,.06);
    --border:      1.5px solid rgba(226,232,240,.9);
}

.content-wrapper {
    background: transparent !important;
    box-shadow: none !important;
    padding: 1.8rem 2rem !important;
}

.page-fade-in {
    animation: fadeIn 0.4s cubic-bezier(.34,1.56,.64,1);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to   { opacity: 1; transform: translateY(0); }
}

.header-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;
}
.header-row h1 {
    font-size: 1.6rem; font-weight: 800; color: var(--slate-900);
    margin: 0; font-family: 'Outfit', sans-serif;
}
.back-link {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .85rem; font-weight: 700; color: var(--slate-500);
    text-decoration: none; transition: color .2s;
}
.back-link:hover { color: var(--teal); }

/* Filters */
.filter-card {
    background: #fff; border-radius: var(--radius-lg); border: var(--border);
    padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: var(--card-shadow);
}
.filter-form {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem; align-items: end;
}
.filter-group { display: flex; flex-direction: column; gap: .4rem; position: relative; }
.filter-label { font-size: .75rem; font-weight: 700; color: var(--slate-700); text-transform: uppercase; letter-spacing: .05em; }

/* Custom Searchable Dropdown */
.custom-select-wrapper {
    position: relative;
    width: 100%;
}
.custom-select-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .65rem .85rem;
    font-size: .85rem;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-sm);
    color: var(--slate-900);
    background: var(--slate-50);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
}
.custom-select-trigger:hover {
    border-color: var(--slate-300);
}
.custom-select-wrapper.open .custom-select-trigger {
    border-color: var(--teal);
    box-shadow: 0 0 0 3px var(--teal-bg);
    background: #fff;
}
.custom-dropdown-menu {
    position: absolute;
    top: 105%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-sm);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    z-index: 1000;
    display: none;
    flex-direction: column;
    max-height: 280px;
    overflow: hidden;
}
.custom-select-wrapper.open .custom-dropdown-menu {
    display: flex;
}
.dropdown-search-box {
    padding: 0.5rem;
    border-bottom: 1px solid var(--slate-100);
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 10;
}
.dropdown-search-input {
    width: 100%;
    padding: 0.4rem 0.6rem;
    font-size: 0.8rem;
    border: 1px solid var(--slate-200);
    border-radius: 6px;
    outline: none;
}
.dropdown-search-input:focus {
    border-color: var(--teal);
}
.custom-options-list {
    overflow-y: auto;
    flex: 1;
    max-height: 200px;
}
.custom-option {
    padding: 0.6rem 0.85rem;
    font-size: 0.85rem;
    color: var(--slate-700);
    cursor: pointer;
    transition: background 0.15s;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.custom-option:hover {
    background: var(--slate-50);
    color: var(--slate-900);
}
.custom-option.selected {
    background: var(--teal-bg);
    color: var(--teal);
    font-weight: 700;
}

.btn-filter {
    background: var(--teal); color: #fff; border: none;
    padding: .65rem 1.2rem; font-size: .85rem; font-weight: 700;
    border-radius: var(--radius-sm); cursor: pointer; transition: background .2s;
    height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
}
.btn-filter:hover { background: var(--teal-dark); }
.btn-clear {
    background: #fff; color: var(--slate-700); border: 1px solid var(--slate-200);
    padding: .65rem 1.2rem; font-size: .85rem; font-weight: 600;
    border-radius: var(--radius-sm); text-decoration: none; text-align: center;
    height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
}
.btn-clear:hover { background: var(--slate-50); color: var(--slate-900); }

/* View Toggle Controls */
.view-toggle-container {
    display: flex;
    align-items: center;
    background: var(--slate-100);
    padding: 0.25rem;
    border-radius: 8px;
    border: 1px solid var(--slate-200);
}
.view-toggle-btn {
    border: none;
    background: transparent;
    padding: 0.4rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--slate-500);
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.2s ease;
}
.view-toggle-btn.active {
    background: #fff;
    color: var(--teal);
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
}

/* Event List View (Default) */
.events-list {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
.event-list-row {
    background: #fff;
    border-radius: var(--radius-sm);
    border: var(--border);
    padding: 0.85rem 1.25rem;
    box-shadow: var(--card-shadow);
    display: grid;
    grid-template-columns: 80px 4px 1.5fr 1fr 1.2fr;
    align-items: center;
    gap: 1.25rem;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
}
.event-list-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15,23,42,.08);
}
.event-list-row::before {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 4px; border-radius: var(--radius-sm) 0 0 var(--radius-sm);
}
.event-list-row.type-morning::before { background: #f59e0b; }
.event-list-row.type-evening::before { background: #7c3aed; }

.list-time {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--teal);
    font-family: 'Outfit', sans-serif;
}
.list-divider {
    height: 30px;
    width: 1px;
    background: var(--slate-200);
}

/* Event Grid View */
.events-grid {
    display: none;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
}
.event-card {
    background: #fff; border-radius: var(--radius-md); border: var(--border);
    padding: 1.2rem; box-shadow: var(--card-shadow);
    display: flex; flex-direction: column; gap: .8rem;
    position: relative; overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15,23,42,.08);
}
.event-card::before {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 4px;
}
.event-card.type-morning::before { background: #f59e0b; }
.event-card.type-evening::before { background: #7c3aed; }

.event-header { display: flex; justify-content: space-between; align-items: flex-start; }
.event-time { font-size: 1.1rem; font-weight: 800; color: var(--teal); font-family: 'Outfit', sans-serif; }
.session-tag {
    font-size: .68rem; font-weight: 700; padding: .2rem .6rem; border-radius: 100px;
    white-space: nowrap;
}
.tag-morning { background: var(--amber-bg); color: #92400e; }
.tag-evening { background: var(--purple-bg); color: var(--purple); }

.event-course { font-size: .95rem; font-weight: 800; color: var(--slate-900); line-height: 1.3; }
.event-prog { font-size: .8rem; color: var(--slate-500); margin-top: .2rem; }

.instructor-box {
    background: var(--slate-50); border: 1px solid var(--slate-100);
    border-radius: var(--radius-sm); padding: .75rem; margin-top: auto;
}
.instructor-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: .4rem; gap: 0.5rem; }
.instructor-name { font-size: .8rem; font-weight: 700; color: var(--slate-700); display: flex; align-items: center; gap: .4rem; }
.instructor-email { font-size: .75rem; color: var(--slate-500); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* General Zoom Button styles */
.btn-zoom {
    border: none; padding: .3rem .7rem; font-size: .7rem; font-weight: 700;
    border-radius: 6px; cursor: pointer; transition: all .2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}
.btn-zoom-assign { background: #fff; color: var(--slate-700); border: 1px solid var(--slate-200); }
.btn-zoom-assign:hover { background: var(--teal-bg); color: var(--teal); border-color: var(--teal); }
.btn-zoom-unassign { background: var(--teal-bg); color: var(--teal); border: 1px solid var(--teal); }
.btn-zoom-unassign:hover { background: var(--coral-bg); color: var(--coral); border-color: var(--coral); }
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">

    <div class="header-row">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <h1 class="mt-2">Today's Events</h1>
            <div style="font-size: .85rem; color: var(--slate-500); margin-top: .2rem;">
                {{ \Carbon\Carbon::today()->format('l, d F Y') }} — Showing {{ $todaysEvents->count() }} events
            </div>
        </div>
        
        <!-- Grid / List Switcher -->
        <div class="view-toggle-container">
            <button class="view-toggle-btn active" id="btn-list-view">
                <i class="bi bi-list-task"></i> List
            </button>
            <button class="view-toggle-btn" id="btn-grid-view">
                <i class="bi bi-grid-3x3-gap-fill"></i> Grid
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.todays-events') }}" class="filter-form">
            <!-- Custom dropdown: Programme -->
            <div class="filter-group">
                <label class="filter-label">Programme</label>
                <div class="custom-select-wrapper" id="select-programme">
                    <input type="hidden" name="programme_id" value="{{ request('programme_id') }}">
                    <div class="custom-select-trigger">
                        <span>Loading...</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="custom-dropdown-menu">
                        <div class="dropdown-search-box">
                            <input type="text" class="dropdown-search-input" placeholder="Search programmes...">
                        </div>
                        <div class="custom-options-list">
                            <div class="custom-option selected" data-value="">All Programmes</div>
                            @foreach($programmes as $prog)
                                <div class="custom-option" data-value="{{ $prog->id }}">{{ $prog->name }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Custom dropdown: Instructor -->
            <div class="filter-group">
                <label class="filter-label">Instructor</label>
                <div class="custom-select-wrapper" id="select-instructor">
                    <input type="hidden" name="instructor_id" value="{{ request('instructor_id') }}">
                    <div class="custom-select-trigger">
                        <span>Loading...</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="custom-dropdown-menu">
                        <div class="dropdown-search-box">
                            <input type="text" class="dropdown-search-input" placeholder="Search instructors...">
                        </div>
                        <div class="custom-options-list">
                            <div class="custom-option selected" data-value="">All Instructors</div>
                            @foreach($instructors as $inst)
                                <div class="custom-option" data-value="{{ $inst->id }}">{{ $inst->name }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom dropdown: Time of Day -->
            <div class="filter-group">
                <label class="filter-label">Time of Day</label>
                <div class="custom-select-wrapper" id="select-time">
                    <input type="hidden" name="time_of_day" value="{{ request('time_of_day') }}">
                    <div class="custom-select-trigger">
                        <span>Loading...</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="custom-dropdown-menu">
                        <div class="custom-options-list">
                            <div class="custom-option selected" data-value="">All Times</div>
                            <div class="custom-option" data-value="morning">Morning</div>
                            <div class="custom-option" data-value="evening">Evening</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom dropdown: Zoom License -->
            <div class="filter-group">
                <label class="filter-label">Zoom License</label>
                <div class="custom-select-wrapper" id="select-zoom">
                    <input type="hidden" name="zoom_status" value="{{ request('zoom_status') }}">
                    <div class="custom-select-trigger">
                        <span>Loading...</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="custom-dropdown-menu">
                        <div class="custom-options-list">
                            <div class="custom-option selected" data-value="">Any Status</div>
                            <div class="custom-option" data-value="licensed">Licensed</div>
                            <div class="custom-option" data-value="unlicensed">Unlicensed</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-group" style="flex-direction: row; gap: .5rem;">
                <button type="submit" class="btn-filter" style="flex: 1;"><i class="bi bi-funnel"></i> Apply</button>
                <a href="{{ route('dashboard.todays-events') }}" class="btn-clear" style="flex: 1;"><i class="bi bi-x-circle"></i> Clear</a>
            </div>
        </form>
    </div>

    <!-- Events Container -->
    @if($todaysEvents->isEmpty())
        <div class="filter-card" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 2.5rem; color: var(--slate-300); margin-bottom: 1rem;"><i class="bi bi-calendar-x"></i></div>
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--slate-700);">No events found</h3>
            <p style="color: var(--slate-500); font-size: .9rem;">Try adjusting your filters or check back later.</p>
        </div>
    @else
        <!-- List View (Default) -->
        <div class="events-list" id="events-list-container">
            @foreach($todaysEvents as $event)
                @php
                    $time = \Carbon\Carbon::parse($event->start_time)->format('H:i');
                    $isMorning = str_contains(strtolower($event->type), 'morning');
                @endphp
                <div class="event-list-row {{ $isMorning ? 'type-morning' : 'type-evening' }}">
                    <div class="list-time">{{ $time }}</div>
                    <div class="list-divider"></div>
                    <div>
                        <div class="event-course">{{ $event->courseUnit->code ?? '' }} - {{ $event->courseUnit->name ?? '' }}</div>
                        <div class="event-prog">{{ $event->programme->name ?? '' }}</div>
                    </div>
                    <div>
                        <span class="session-tag {{ $isMorning ? 'tag-morning' : 'tag-evening' }}">{{ $event->type }}</span>
                    </div>
                    @if(isset($event->instructor) && !empty($event->instructor->id))
                        <div style="display: flex; align-items: center; justify-content: space-between; background: var(--slate-50); padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--slate-100); min-width: 0; gap: 0.5rem;">
                            <div style="display: flex; flex-direction: column; min-width: 0; flex: 1;">
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--slate-700); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="bi bi-person-badge"></i> {{ $event->instructor->name }}</span>
                                <span style="font-size: 0.75rem; color: var(--slate-500); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $event->instructor->email }}</span>
                            </div>
                            <form action="{{ route('users.toggle-zoom', $event->instructor->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                @if($event->instructor->zoom_email)
                                    <button type="submit" class="btn-zoom btn-zoom-unassign" title="Remove License">
                                        <i class="bi bi-camera-video-fill"></i> Licensed
                                    </button>
                                @else
                                    <button type="submit" class="btn-zoom btn-zoom-assign" title="Assign License">
                                        <i class="bi bi-camera-video"></i> Unlicensed
                                    </button>
                                @endif
                            </form>
                        </div>
                    @else
                        <div style="font-size: 0.8rem; color: var(--slate-500); font-style: italic;">No instructor mapped</div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Grid View (Hidden by default) -->
        <div class="events-grid" id="events-grid-container">
            @foreach($todaysEvents as $event)
                @php
                    $time = \Carbon\Carbon::parse($event->start_time)->format('H:i');
                    $isMorning = str_contains(strtolower($event->type), 'morning');
                @endphp
                <div class="event-card {{ $isMorning ? 'type-morning' : 'type-evening' }}">
                    <div class="event-header">
                        <div class="event-time">{{ $time }}</div>
                        <span class="session-tag {{ $isMorning ? 'tag-morning' : 'tag-evening' }}">{{ $event->type }}</span>
                    </div>
                    
                    <div>
                        <div class="event-course">{{ $event->courseUnit->code ?? '' }} - {{ $event->courseUnit->name ?? '' }}</div>
                        <div class="event-prog">{{ $event->programme->name ?? '' }}</div>
                    </div>

                    @if(isset($event->instructor) && !empty($event->instructor->id))
                        <div class="instructor-box">
                            <div class="instructor-head">
                                <div class="instructor-name"><i class="bi bi-person-badge"></i> {{ $event->instructor->name }}</div>
                                <form action="{{ route('users.toggle-zoom', $event->instructor->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @if($event->instructor->zoom_email)
                                        <button type="submit" class="btn-zoom btn-zoom-unassign" title="Remove License">
                                            <i class="bi bi-camera-video-fill"></i> Unassign
                                        </button>
                                    @else
                                        <button type="submit" class="btn-zoom btn-zoom-assign" title="Assign License">
                                            <i class="bi bi-camera-video"></i> Assign
                                        </button>
                                    @endif
                                </form>
                            </div>
                            <div class="instructor-email">{{ $event->instructor->email }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if($todaysEvents->hasPages())
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $todaysEvents->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ══ View Toggle System ══ */
    const listBtn = document.getElementById('btn-list-view');
    const gridBtn = document.getElementById('btn-grid-view');
    const listContainer = document.getElementById('events-list-container');
    const gridContainer = document.getElementById('events-grid-container');

    // Retrieve active view selection from localStorage or default to 'list'
    const currentView = localStorage.getItem('todaysEventsView') || 'list';
    setView(currentView);

    listBtn.addEventListener('click', () => setView('list'));
    gridBtn.addEventListener('click', () => setView('grid'));

    function setView(view) {
        if (view === 'grid') {
            listBtn.classList.remove('active');
            gridBtn.classList.add('active');
            if (listContainer) listContainer.style.display = 'none';
            if (gridContainer) gridContainer.style.display = 'grid';
            localStorage.setItem('todaysEventsView', 'grid');
        } else {
            gridBtn.classList.remove('active');
            listBtn.classList.add('active');
            if (gridContainer) gridContainer.style.display = 'none';
            if (listContainer) listContainer.style.display = 'flex';
            localStorage.setItem('todaysEventsView', 'list');
        }
    }

    /* ══ Custom Searchable Dropdown System ══ */
    document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
        const trigger = wrapper.querySelector('.custom-select-trigger');
        const triggerText = trigger.querySelector('span');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const menu = wrapper.querySelector('.custom-dropdown-menu');
        const optionsList = wrapper.querySelector('.custom-options-list');
        const options = wrapper.querySelectorAll('.custom-option');
        const searchInput = wrapper.querySelector('.dropdown-search-input');

        // Set initial selected value text
        const currentValue = hiddenInput.value;
        let selectedOption = Array.from(options).find(opt => opt.dataset.value == currentValue);
        if (!selectedOption) {
            selectedOption = options[0]; // fallback to first (All/Any)
        }
        
        options.forEach(opt => opt.classList.remove('selected'));
        if (selectedOption) {
            selectedOption.classList.add('selected');
            triggerText.textContent = selectedOption.textContent;
        }

        // Toggle dropdown open
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close other dropdowns
            document.querySelectorAll('.custom-select-wrapper').forEach(other => {
                if (other !== wrapper) other.classList.remove('open');
            });
            wrapper.classList.toggle('open');
            if (wrapper.classList.contains('open') && searchInput) {
                searchInput.focus();
            }
        });

        // Search options
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                options.forEach(opt => {
                    const text = opt.textContent.toLowerCase();
                    if (text.includes(query)) {
                        opt.style.display = 'flex';
                    } else {
                        opt.style.display = 'none';
                    }
                });
            });
            // Stop click propagation inside search box
            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        // Option selection
        optionsList.addEventListener('click', function(e) {
            const option = e.target.closest('.custom-option');
            if (!option) return;
            e.stopPropagation();

            options.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            
            triggerText.textContent = option.textContent;
            hiddenInput.value = option.dataset.value;
            wrapper.classList.remove('open');
            if (searchInput) searchInput.value = '';
            options.forEach(opt => opt.style.display = 'flex');
        });
    });

    // Close dropdowns on body click
    document.addEventListener('click', function() {
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            wrapper.classList.remove('open');
            const searchInput = wrapper.querySelector('.dropdown-search-input');
            if (searchInput) searchInput.value = '';
            const options = wrapper.querySelectorAll('.custom-option');
            options.forEach(opt => opt.style.display = 'flex');
        });
    });
});
</script>
@endpush
