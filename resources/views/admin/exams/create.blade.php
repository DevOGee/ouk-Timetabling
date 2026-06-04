@extends('layouts.app')
@section('title', 'Create Exam Schedule')

@push('styles')
<style>
:root {
    --teal:       #037b90;
    --teal-dark:  #024d5c;
    --teal-bg:    rgba(3,123,144,.1);
    --coral:      #ff7f50;
    --coral-bg:   rgba(255,127,80,.1);
    --green:      #10b981;
    --green-bg:   rgba(16,185,129,.1);
    --slate-50:   #f8fafc;
    --slate-100:  #f1f5f9;
    --slate-200:  #e2e8f0;
    --slate-300:  #cbd5e1;
    --slate-500:  #64748b;
    --slate-700:  #334155;
    --slate-900:  #0f172a;
    --radius-lg:  20px;
    --radius-md:  14px;
    --radius-sm:  10px;
    --card-shadow:0 1px 3px rgba(15,23,42,.06), 0 4px 20px rgba(15,23,42,.06);
    --border:     1.5px solid rgba(226,232,240,.9);
}

.content-wrapper { background: transparent !important; box-shadow: none !important; padding: 1.8rem 2rem !important; }

.page-fade-in { animation: fadeIn 0.4s cubic-bezier(.34,1.56,.64,1) both; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.stagger-1 { animation: fadeIn .4s cubic-bezier(.34,1.56,.64,1) .05s both; }
.stagger-2 { animation: fadeIn .4s cubic-bezier(.34,1.56,.64,1) .12s both; }

/* Breadcrumb */
.breadcrumb-nav { display:flex; align-items:center; gap:.4rem; font-size:.78rem; color:var(--slate-500); margin-bottom:1.25rem; }
.breadcrumb-nav a { color:var(--slate-500); text-decoration:none; transition:color .15s; }
.breadcrumb-nav a:hover { color:var(--teal); }
.breadcrumb-nav .sep { color:var(--slate-300); }
.breadcrumb-nav .current { color:var(--slate-700); font-weight:600; }

/* Page Header */
.page-header { display:flex; align-items:center; gap:1rem; margin-bottom:1.75rem; }
.header-icon { width:52px; height:52px; border-radius:14px; background:var(--teal-bg); color:var(--teal); display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
.header-title { font-size:1.6rem; font-weight:800; color:var(--slate-900); margin:0; font-family:'Outfit',sans-serif; line-height:1.2; }
.header-subtitle { font-size:.82rem; color:var(--slate-500); margin:.2rem 0 0; }

/* Form Card */
.form-card { background:#fff; border-radius:var(--radius-lg); border:var(--border); box-shadow:var(--card-shadow); overflow:hidden; }
.form-card-header { padding:1.25rem 1.75rem; border-bottom:1px solid var(--slate-100); background:var(--slate-50); display:flex; align-items:center; gap:.65rem; }
.form-card-header h2 { font-size:.95rem; font-weight:800; color:var(--slate-900); margin:0; }
.form-card-body { padding:1.75rem; }

/* Form Elements */
.field-label { font-size:.75rem; font-weight:700; color:var(--slate-700); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.4rem; display:block; }
.field-input {
    width:100%; padding:.7rem .9rem; font-size:.875rem;
    border:1.5px solid var(--slate-200); border-radius:var(--radius-sm);
    color:var(--slate-900); background:var(--slate-50); outline:none;
    transition:all .2s; box-sizing:border-box; font-family:inherit;
}
.field-input:focus { border-color:var(--teal); background:#fff; box-shadow:0 0 0 3px var(--teal-bg); }
.field-input.is-error { border-color:var(--coral); background:var(--coral-bg); }
.field-hint { font-size:.75rem; color:var(--slate-500); margin-top:.3rem; }
.field-error { font-size:.75rem; color:var(--coral); font-weight:600; margin-top:.3rem; }

/* Toggle Switch */
.toggle-row { display:flex; align-items:center; gap:.85rem; padding:1rem 1.1rem; background:var(--slate-50); border-radius:var(--radius-sm); border:1.5px solid var(--slate-200); cursor:pointer; transition:all .2s; }
.toggle-row:hover { border-color:var(--teal); background:var(--teal-bg); }
.toggle-row input[type="checkbox"] { display:none; }
.toggle-switch { width:40px; height:22px; background:var(--slate-300); border-radius:100px; position:relative; flex-shrink:0; transition:background .2s; }
.toggle-switch::after { content:''; position:absolute; width:16px; height:16px; background:#fff; border-radius:50%; top:3px; left:3px; transition:left .2s; box-shadow:0 1px 3px rgba(0,0,0,.15); }
.toggle-row input:checked ~ .toggle-switch { background:var(--teal); }
.toggle-row input:checked ~ .toggle-switch::after { left:21px; }
.toggle-text { font-size:.875rem; font-weight:600; color:var(--slate-700); flex:1; }
.toggle-text small { display:block; font-weight:400; font-size:.75rem; color:var(--slate-500); margin-top:.1rem; }

/* Custom Select Styling */
select.field-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b' stroke='%2364748b' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.2em;
    padding-right: 2.5rem;
}
select.field-input:focus {
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23037b90' stroke='%23037b90' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
}

/* Grid helpers */
.field-row { display:grid; gap:1.25rem; }
.field-row-2 { grid-template-columns:1fr 1fr; }
.field-group { display:flex; flex-direction:column; }
.mb-field { margin-bottom:1.25rem; }

/* Footer */
.form-footer { padding:1.25rem 1.75rem; border-top:1px solid var(--slate-100); background:var(--slate-50); display:flex; justify-content:space-between; align-items:center; }

/* Buttons */
.btn-premium { background:var(--teal); color:#fff; border:none; padding:.65rem 1.4rem; font-size:.875rem; font-weight:700; border-radius:var(--radius-sm); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
.btn-premium:hover { background:var(--teal-dark); color:#fff; transform:translateY(-1px); box-shadow:0 4px 12px rgba(3,123,144,.2); }
.btn-outline-soft { background:#fff; color:var(--slate-700); border:1.5px solid var(--slate-200); padding:.65rem 1.25rem; font-size:.875rem; font-weight:600; border-radius:var(--radius-sm); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
.btn-outline-soft:hover { border-color:var(--teal); color:var(--teal); background:var(--teal-bg); }

@media(max-width:640px) { .field-row-2 { grid-template-columns:1fr; } .form-footer { flex-direction:column-reverse; gap:.75rem; } }
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">

    <nav class="breadcrumb-nav stagger-1">
        <a href="{{ route('admin.exams.index') }}"><i class="bi bi-calendar3"></i> Exam Timetables</a>
        <span class="sep">/</span>
        <span class="current">Create Schedule</span>
    </nav>

    <div class="page-header stagger-1">
        <div class="header-icon"><i class="bi bi-plus-square"></i></div>
        <div>
            <h1 class="header-title">Create Exam Schedule</h1>
            <p class="header-subtitle">Set up a new examination timetable for an academic session.</p>
        </div>
    </div>

    <div style="max-width: 100%;">
        <div class="form-card stagger-2">
            <div class="form-card-header">
                <i class="bi bi-journal-plus" style="color: var(--teal); font-size: 1rem;"></i>
                <h2>Schedule Details</h2>
            </div>

            <form action="{{ route('admin.exams.store') }}" method="POST">
                @csrf
                <div class="form-card-body">

                    <div class="field-row field-row-2 mb-field">
                        {{-- Name --}}
                        <div class="field-group">
                            <label class="field-label" for="name">Schedule Name <span style="color:var(--coral)">*</span></label>
                            <input type="text" class="field-input {{ $errors->has('name') ? 'is-error' : '' }}"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="e.g., Semester 1 2024 Final Exams" required>
                            @error('name')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Academic Session --}}
                        <div class="field-group">
                            <label class="field-label" for="academic_session_id">Academic Session <span style="color:var(--coral)">*</span></label>
                            <select class="field-input custom-select {{ $errors->has('academic_session_id') ? 'is-error' : '' }}"
                                id="academic_session_id" name="academic_session_id" required data-placeholder="Select a session...">
                            <option value=""></option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_session_id')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="mb-field field-row field-row-2">
                        <div class="field-group">
                            <label class="field-label" for="start_date">Start Date <span style="color:var(--coral)">*</span></label>
                            <input type="date" class="field-input {{ $errors->has('start_date') ? 'is-error' : '' }}"
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label" for="end_date">End Date <span style="color:var(--coral)">*</span></label>
                            <input type="date" class="field-input {{ $errors->has('end_date') ? 'is-error' : '' }}"
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Active Toggle --}}
                    <label class="toggle-row" for="is_active">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <div class="toggle-switch"></div>
                        <div class="toggle-text">
                            Set as Active Schedule
                            <small>This will mark the schedule as currently active.</small>
                        </div>
                    </label>

                </div>
                <div class="form-footer">
                    <a href="{{ route('admin.exams.index') }}" class="btn-outline-soft">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn-premium">
                        <i class="bi bi-check2-circle"></i> Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('start_date');
    const endInput   = document.getElementById('end_date');

    /* Auto-set end date ~2 weeks after start */
    startInput?.addEventListener('change', function () {
        if (startInput.value && !endInput.value) {
            const d = new Date(startInput.value);
            d.setDate(d.getDate() + 14);
            endInput.min   = startInput.value;
            endInput.value = d.toISOString().split('T')[0];
        }
        if (startInput.value && endInput.value) {
            endInput.min = startInput.value;
        }
    });

    /* Sync toggle visual state on load */
    document.querySelectorAll('.toggle-row input[type="checkbox"]').forEach(cb => {
        const sw = cb.nextElementSibling;
        if (!sw) return;
        cb.addEventListener('change', () => {
            sw.style.background = cb.checked ? 'var(--teal)' : 'var(--slate-300)';
        });
        sw.style.background = cb.checked ? 'var(--teal)' : 'var(--slate-300)';
    });
});
</script>
@endpush
