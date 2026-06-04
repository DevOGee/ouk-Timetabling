@extends('layouts.app')
@section('title', 'Exam Timetables')

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

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.header-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;
}
.header-row h1 {
    font-size: 1.8rem; font-weight: 800; color: var(--slate-900);
    margin: 0; font-family: 'Outfit', sans-serif;
}

.btn-premium {
    background: var(--teal); color: #fff; border: none;
    padding: .65rem 1.25rem; font-size: .85rem; font-weight: 700;
    border-radius: var(--radius-sm); cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 0.4rem;
    text-decoration: none;
}
.btn-premium:hover {
    background: var(--teal-dark);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(3, 123, 144, 0.2);
}

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

.search-input-field {
    width: 100%;
    padding: .6rem .85rem;
    font-size: .85rem;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-sm);
    color: var(--slate-900);
    background: var(--slate-50);
    outline: none;
}
.search-input-field:focus {
    border-color: var(--teal);
    background: #fff;
    box-shadow: 0 0 0 3px var(--teal-bg);
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

.table-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: var(--border);
    box-shadow: var(--card-shadow);
    overflow: hidden;
}

.custom-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.custom-table th {
    background: var(--slate-50);
    padding: 1rem 1.5rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: .05em;
    border-bottom: 1px solid var(--slate-200);
}
.custom-table td {
    padding: 1.1rem 1.5rem;
    font-size: .85rem;
    color: var(--slate-700);
    border-bottom: 1px solid var(--slate-100);
    vertical-align: middle;
}
.custom-table tr:last-child td {
    border-bottom: none;
}
.custom-table tr {
    transition: background 0.15s;
}
.custom-table tr:hover td {
    background: var(--slate-50);
}

.schedule-link {
    font-weight: 800;
    color: var(--slate-900);
    text-decoration: none;
    transition: color 0.15s;
}
.schedule-link:hover {
    color: var(--teal);
}

.badge-pill {
    font-size: .7rem;
    font-weight: 700;
    padding: .25rem .6rem;
    border-radius: 100px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.badge-active { background: var(--teal-bg); color: var(--teal); }
.badge-inactive { background: var(--slate-100); color: var(--slate-500); }
.badge-published { background: rgba(16, 185, 129, 0.1); color: rgb(16, 185, 129); }
.badge-draft { background: var(--coral-bg); color: var(--coral); }

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--slate-200);
    background: #fff;
    color: var(--slate-500);
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.action-btn:hover {
    border-color: var(--teal);
    color: var(--teal);
    background: var(--teal-bg);
}
.action-btn-danger:hover {
    border-color: var(--coral);
    color: var(--coral);
    background: var(--coral-bg);
}
.action-btn-success {
    background: var(--teal);
    color: #fff;
    border-color: var(--teal);
}
.action-btn-success:hover {
    background: var(--teal-dark);
    color: #fff;
    border-color: var(--teal-dark);
}
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">
    <div class="header-row">
        <div>
            <h1>Exam Timetables</h1>
            <p style="font-size: .85rem; color: var(--slate-500); margin: 0.2rem 0 0 0;">Manage examinations schedules, releases, and drafts.</p>
        </div>
        <div>
            <a href="{{ route('admin.exams.create') }}" class="btn-premium">
                <i class="bi bi-plus-lg"></i> Create New Schedule
            </a>
        </div>
    </div>

    <!-- Advanced Server-Side Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.exams.index') }}" class="filter-form">
            <div class="filter-group">
                <label class="filter-label">Search Schedule</label>
                <input type="text" name="search" class="search-input-field" placeholder="Search by name..." value="{{ request('search') }}">
            </div>

            <!-- Custom select: Academic Session -->
            <div class="filter-group">
                <label class="filter-label">Academic Session</label>
                <div class="custom-select-wrapper" id="select-session">
                    <input type="hidden" name="academic_session_id" value="{{ request('academic_session_id') }}">
                    <div class="custom-select-trigger">
                        <span>Loading...</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="custom-dropdown-menu">
                        <div class="dropdown-search-box">
                            <input type="text" class="dropdown-search-input" placeholder="Search sessions...">
                        </div>
                        <div class="custom-options-list">
                            <div class="custom-option selected" data-value="">All Sessions</div>
                            @foreach($sessions as $session)
                                <div class="custom-option" data-value="{{ $session->id }}">{{ $session->name }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom select: Status -->
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <div class="custom-select-wrapper" id="select-status">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <div class="custom-select-trigger">
                        <span>Loading...</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="custom-dropdown-menu">
                        <div class="custom-options-list">
                            <div class="custom-option selected" data-value="">Any Status</div>
                            <div class="custom-option" data-value="active">Active</div>
                            <div class="custom-option" data-value="inactive">Inactive</div>
                            <div class="custom-option" data-value="published">Published</div>
                            <div class="custom-option" data-value="draft">Draft</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-group" style="flex-direction: row; gap: .5rem;">
                <button type="submit" class="btn-filter" style="flex: 1;"><i class="bi bi-funnel"></i> Apply</button>
                <a href="{{ route('admin.exams.index') }}" class="btn-clear" style="flex: 1;"><i class="bi bi-x-circle"></i> Clear</a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); color: rgb(16, 185, 129); padding: 1rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.85rem; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        @if($schedules->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 2.5rem; color: var(--slate-300); margin-bottom: 1rem;"><i class="bi bi-calendar-x"></i></div>
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--slate-700);">No schedules found</h3>
                <p style="color: var(--slate-500); font-size: .9rem;">Try adjusting your filters or create a new schedule.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Schedule Info</th>
                            <th>Period</th>
                            <th>Status Flags</th>
                            <th style="text-align: right; padding-right: 2rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                            <tr>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <a href="{{ route('admin.exams.show', $schedule) }}" class="schedule-link">
                                            {{ $schedule->name }}
                                        </a>
                                        <span style="font-size: 0.72rem; color: var(--slate-500); font-weight: 600; margin-top: 0.15rem;">
                                            Academic Session: {{ $schedule->academicSession->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: var(--slate-500); display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <i class="bi bi-calendar3"></i> {{ $schedule->start_date->format('M d, Y') }} - {{ $schedule->end_date->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <span class="badge-pill {{ $schedule->is_active ? 'badge-active' : 'badge-inactive' }}">
                                            <i class="bi bi-circle-fill" style="font-size: 5px;"></i> {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <span class="badge-pill {{ $schedule->is_published ? 'badge-published' : 'badge-draft' }}">
                                            <i class="bi bi-circle-fill" style="font-size: 5px;"></i> {{ $schedule->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>
                                </td>
                                <td style="text-align: right; padding-right: 2rem;">
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                        @if($schedule->is_published)
                                            <form action="{{ route('admin.exams.unpublish', $schedule) }}" method="POST" style="margin:0;" class="confirm-form"
                                                data-confirm-title="Unpublish Schedule"
                                                data-confirm-msg="This schedule will be hidden from students. Are you sure you want to unpublish &ldquo;{{ addslashes($schedule->name) }}&rdquo;?"
                                                data-confirm-icon="bi-eye-slash-fill"
                                                data-confirm-color="#f59e0b"
                                                data-confirm-btn="Unpublish">
                                                @csrf
                                                <button type="button" class="action-btn btn-confirm-trigger" title="Unpublish Schedule">
                                                    <i class="bi bi-eye-slash-fill"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.exams.publish', $schedule) }}" method="POST" style="margin:0;" class="confirm-form"
                                                data-confirm-title="Publish Schedule"
                                                data-confirm-msg="This will make the schedule visible to students. Publish &ldquo;{{ addslashes($schedule->name) }}&rdquo;?"
                                                data-confirm-icon="bi-eye-fill"
                                                data-confirm-color="#10b981"
                                                data-confirm-btn="Publish">
                                                @csrf
                                                <button type="button" class="action-btn action-btn-success btn-confirm-trigger" title="Publish Schedule">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.exams.show', $schedule) }}" class="action-btn" title="Manage Schedule">
                                            <i class="bi bi-gear-fill"></i>
                                        </a>

                                        <form action="{{ route('admin.exams.destroy', $schedule) }}" method="POST" style="margin:0;" class="confirm-form"
                                            data-confirm-title="Delete Schedule"
                                            data-confirm-msg="This action cannot be undone. All exam slots within &ldquo;{{ addslashes($schedule->name) }}&rdquo; will be permanently deleted."
                                            data-confirm-icon="bi-trash3-fill"
                                            data-confirm-color="#ff7f50"
                                            data-confirm-btn="Delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="action-btn action-btn-danger btn-confirm-trigger" title="Delete Schedule">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($schedules->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--slate-100); display: flex; justify-content: center;">
                    {{ $schedules->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

{{-- ═══════════════ CUSTOM CONFIRM MODAL ═══════════════ --}}
<div id="confirmModal" style="
    display: none;
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
    animation: fadeIn .2s ease;
">
    <div id="confirmBox" style="
        background: #fff; border-radius: 20px;
        box-shadow: 0 24px 60px rgba(0,0,0,.18);
        width: 100%; max-width: 400px; margin: 1rem;
        overflow: hidden;
        animation: slideUp .25s cubic-bezier(.34,1.56,.64,1);
    ">
        {{-- Header --}}
        <div id="confirmHeader" style="
            padding: 1.4rem 1.5rem 1rem;
            display: flex; align-items: center; gap: .85rem;
        ">
            <div id="confirmIconWrap" style="
                width: 44px; height: 44px; border-radius: 12px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.2rem; flex-shrink: 0;
            ">
                <i id="confirmIcon" class="bi"></i>
            </div>
            <div>
                <h3 id="confirmTitle" style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;"></h3>
            </div>
        </div>
        {{-- Body --}}
        <div style="padding: 0 1.5rem 1.25rem;">
            <p id="confirmMsg" style="font-size: .875rem; color: #64748b; margin: 0; line-height: 1.6;"></p>
        </div>
        {{-- Footer --}}
        <div style="
            padding: 1rem 1.5rem;
            background: #f8fafc;
            display: flex; justify-content: flex-end; gap: .65rem;
            border-top: 1px solid #f1f5f9;
        ">
            <button id="confirmCancel" style="
                padding: .6rem 1.2rem; border-radius: 10px;
                border: 1.5px solid #e2e8f0; background: #fff;
                color: #334155; font-size: .85rem; font-weight: 600;
                cursor: pointer; transition: all .15s;
            ">Cancel</button>
            <button id="confirmOk" style="
                padding: .6rem 1.4rem; border-radius: 10px;
                border: none; color: #fff;
                font-size: .85rem; font-weight: 700;
                cursor: pointer; transition: all .15s;
            ">Confirm</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    document.addEventListener('click', function() {
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            wrapper.classList.remove('open');
            const searchInput = wrapper.querySelector('.dropdown-search-input');
            if (searchInput) searchInput.value = '';
            const options = wrapper.querySelectorAll('.custom-option');
            options.forEach(opt => opt.style.display = 'flex');
        });
    });

    /* ══════════════════════════════════════
       Custom Confirmation Modal System
       ══════════════════════════════════════ */
    const confirmModal  = document.getElementById('confirmModal');
    const confirmBox    = document.getElementById('confirmBox');
    const confirmTitle  = document.getElementById('confirmTitle');
    const confirmMsg    = document.getElementById('confirmMsg');
    const confirmIcon   = document.getElementById('confirmIcon');
    const confirmIconWrap = document.getElementById('confirmIconWrap');
    const confirmOkBtn  = document.getElementById('confirmOk');
    const confirmCancel = document.getElementById('confirmCancel');

    let pendingForm = null;

    function showConfirm({ title, msg, icon, color, btnLabel }) {
        confirmTitle.textContent  = title  || 'Are you sure?';
        confirmMsg.innerHTML      = msg    || 'This action cannot be undone.';
        confirmIcon.className     = `bi ${icon || 'bi-exclamation-triangle-fill'}`;
        const safeColor           = color || 'var(--coral)';
        confirmIconWrap.style.background = safeColor + '18';
        confirmIconWrap.style.color      = safeColor;
        confirmOkBtn.style.background    = safeColor;
        confirmOkBtn.textContent  = btnLabel || 'Confirm';
        confirmModal.style.display = 'flex';
    }

    function closeConfirm() {
        confirmModal.style.display = 'none';
        pendingForm = null;
    }

    /* Intercept all .confirm-form buttons */
    document.querySelectorAll('.confirm-form').forEach(form => {
        const trigger = form.querySelector('.btn-confirm-trigger');
        if (!trigger) return;
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            pendingForm = form;
            showConfirm({
                title   : form.dataset.confirmTitle,
                msg     : form.dataset.confirmMsg,
                icon    : form.dataset.confirmIcon,
                color   : form.dataset.confirmColor,
                btnLabel: form.dataset.confirmBtn,
            });
        });
    });

    confirmOkBtn.addEventListener('click', function () {
        if (pendingForm) {
            closeConfirm();
            pendingForm.submit();
        }
    });

    confirmCancel.addEventListener('click', closeConfirm);

    /* Close on backdrop click */
    confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) closeConfirm();
    });

    /* ESC key */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeConfirm();
    });

    /* Hover states for cancel button */
    confirmCancel.addEventListener('mouseenter', () => { confirmCancel.style.background = '#f1f5f9'; });
    confirmCancel.addEventListener('mouseleave', () => { confirmCancel.style.background = '#fff'; });
});
</script>
@endpush
