@extends('layouts.app')
@section('title', 'Edit — ' . $academicSession->name)

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
    --amber:      #f59e0b;
    --amber-bg:   rgba(245,158,11,.1);
    --purple:     #7c3aed;
    --purple-bg:  rgba(124,58,237,.1);
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
.content-wrapper { background:transparent!important; box-shadow:none!important; padding:1.8rem 2rem!important; }
.page-fade-in { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) both; }
.stagger-1    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .05s both; }
.stagger-2    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .12s both; }
.stagger-3    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .20s both; }
@keyframes fadeIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

/* Breadcrumb */
.breadcrumb-nav{display:flex;align-items:center;gap:.4rem;font-size:.78rem;color:var(--slate-500);margin-bottom:1.25rem}
.breadcrumb-nav a{color:var(--slate-500);text-decoration:none;transition:color .15s}
.breadcrumb-nav a:hover{color:var(--teal)}
.breadcrumb-nav .sep{color:var(--slate-300)}
.breadcrumb-nav .current{color:var(--slate-700);font-weight:600}

/* Page Header */
.page-header{display:flex;align-items:center;gap:1rem;margin-bottom:1.75rem}
.header-icon{width:52px;height:52px;border-radius:14px;background:var(--amber-bg);color:var(--amber);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0}
.header-title{font-size:1.6rem;font-weight:800;color:var(--slate-900);margin:0;font-family:'Outfit',sans-serif;line-height:1.2}
.header-subtitle{font-size:.82rem;color:var(--slate-500);margin:.2rem 0 0}

/* Session Info Pill */
.session-pill{display:inline-flex;align-items:center;gap:.5rem;background:var(--teal-bg);color:var(--teal);font-size:.75rem;font-weight:700;padding:.35rem .8rem;border-radius:100px;margin-left:.5rem}

/* Form Card */
.form-card{background:#fff;border-radius:var(--radius-lg);border:var(--border);box-shadow:var(--card-shadow);overflow:hidden}
.form-card-header{padding:1.25rem 1.75rem;border-bottom:1px solid var(--slate-100);background:var(--slate-50);display:flex;align-items:center;gap:.65rem}
.form-card-header h2{font-size:.95rem;font-weight:800;color:var(--slate-900);margin:0}
.form-card-body{padding:1.75rem}

/* Fields */
.field-label{font-size:.75rem;font-weight:700;color:var(--slate-700);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;display:block}
.field-input{width:100%;padding:.7rem .9rem;font-size:.875rem;border:1.5px solid var(--slate-200);border-radius:var(--radius-sm);color:var(--slate-900);background:var(--slate-50);outline:none;transition:all .2s;box-sizing:border-box;font-family:inherit}
.field-input:focus{border-color:var(--teal);background:#fff;box-shadow:0 0 0 3px var(--teal-bg)}
.field-input.is-error{border-color:var(--coral);background:var(--coral-bg)}
.field-hint{font-size:.75rem;color:var(--slate-500);margin-top:.3rem}
.field-error{font-size:.75rem;color:var(--coral);font-weight:600;margin-top:.3rem}

.field-row-2{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem}
.field-group{display:flex;flex-direction:column}
.mb-field{margin-bottom:1.25rem}

/* Status Select Pill Styles */
.status-upcoming { color: var(--purple); }
.status-active   { color: var(--green);  }
.status-completed{ color: var(--slate-500); }
.status-archived { color: var(--coral); }

/* Toggle */
.toggle-row{display:flex;align-items:center;gap:.85rem;padding:1rem 1.1rem;background:var(--slate-50);border-radius:var(--radius-sm);border:1.5px solid var(--slate-200);cursor:pointer;transition:all .2s;user-select:none}
.toggle-row:hover{border-color:var(--teal);background:var(--teal-bg)}
.toggle-row input[type="checkbox"]{display:none}
.toggle-switch{width:40px;height:22px;background:var(--slate-300);border-radius:100px;position:relative;flex-shrink:0;transition:background .2s}
.toggle-switch::after{content:'';position:absolute;width:16px;height:16px;background:#fff;border-radius:50%;top:3px;left:3px;transition:left .2s;box-shadow:0 1px 3px rgba(0,0,0,.15)}
.toggle-text{font-size:.875rem;font-weight:600;color:var(--slate-700);flex:1}
.toggle-text small{display:block;font-weight:400;font-size:.75rem;color:var(--slate-500);margin-top:.1rem}

/* Danger Zone Card */
.danger-card{background:#fff;border-radius:var(--radius-lg);border:1.5px solid rgba(255,127,80,.25);box-shadow:var(--card-shadow);overflow:hidden;margin-top:1.5rem}
.danger-card-header{padding:1rem 1.75rem;border-bottom:1px solid rgba(255,127,80,.15);background:var(--coral-bg);display:flex;align-items:center;gap:.65rem}
.danger-card-header h2{font-size:.9rem;font-weight:800;color:var(--coral);margin:0}
.danger-card-body{padding:1.25rem 1.75rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.danger-text{font-size:.85rem;color:var(--slate-700);font-weight:500}
.danger-text small{display:block;color:var(--slate-500);font-size:.78rem;margin-top:.15rem}

/* Footer & Buttons */
.form-footer{padding:1.25rem 1.75rem;border-top:1px solid var(--slate-100);background:var(--slate-50);display:flex;justify-content:space-between;align-items:center}
.btn-premium{background:var(--teal);color:#fff;border:none;padding:.65rem 1.4rem;font-size:.875rem;font-weight:700;border-radius:var(--radius-sm);cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none}
.btn-premium:hover{background:var(--teal-dark);color:#fff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(3,123,144,.2)}
.btn-outline-soft{background:#fff;color:var(--slate-700);border:1.5px solid var(--slate-200);padding:.65rem 1.25rem;font-size:.875rem;font-weight:600;border-radius:var(--radius-sm);cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none}
.btn-outline-soft:hover{border-color:var(--teal);color:var(--teal);background:var(--teal-bg)}
.btn-danger{background:#fff;color:var(--coral);border:1.5px solid rgba(255,127,80,.4);padding:.6rem 1.2rem;font-size:.85rem;font-weight:700;border-radius:var(--radius-sm);cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none}
.btn-danger:hover{background:var(--coral);color:#fff;border-color:var(--coral)}

/* Custom Confirm Modal */
@keyframes slideUp{from{opacity:0;transform:translateY(24px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}

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

@media(max-width:640px){.field-row-2{grid-template-columns:1fr}.form-footer{flex-direction:column-reverse;gap:.75rem}}
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav stagger-1">
        <a href="{{ route('admin.academic-sessions.index') }}"><i class="bi bi-calendar2-week"></i> Academic Sessions</a>
        <span class="sep">/</span>
        <span class="current">Edit &mdash; {{ Str::limit($academicSession->name, 35) }}</span>
    </nav>

    {{-- Page Header --}}
    <div class="page-header stagger-1">
        <a href="{{ route('admin.academic-sessions.index') }}" class="btn-outline-soft" style="padding:.5rem .9rem;flex-shrink:0;" title="Back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="header-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
            <h1 class="header-title">
                Edit Session
                @if($academicSession->is_current)
                    <span class="session-pill"><i class="bi bi-star-fill" style="font-size:.6rem"></i> Current</span>
                @endif
            </h1>
            <p class="header-subtitle">Modify the details of <strong>{{ $academicSession->name }}</strong>.</p>
        </div>
    </div>

    <div style="max-width:100%;">

        {{-- Main Edit Form --}}
        <div class="form-card stagger-2">
            <div class="form-card-header">
                <i class="bi bi-calendar2-check" style="color:var(--teal);font-size:1rem;"></i>
                <h2>Session Details</h2>
            </div>

            <form action="{{ route('admin.academic-sessions.update', $academicSession) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-card-body">

                    <div class="mb-field field-row-2">
                        {{-- Name --}}
                        <div class="field-group">
                            <label class="field-label" for="name">Session Name <span style="color:var(--coral)">*</span></label>
                            <input type="text" class="field-input {{ $errors->has('name') ? 'is-error' : '' }}"
                                   id="name" name="name"
                                   value="{{ old('name', $academicSession->name) }}"
                                   placeholder="e.g., May-August 2026" required>
                            @error('name')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Code --}}
                        <div class="field-group">
                            <label class="field-label" for="code">Session Code <span style="color:var(--coral)">*</span></label>
                            <input type="text" class="field-input {{ $errors->has('code') ? 'is-error' : '' }}"
                                   id="code" name="code"
                                   value="{{ old('code', $academicSession->code) }}" required>
                            @error('code')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Status + Current Toggle --}}
                    <div class="mb-field field-row-2">
                        <div class="field-group">
                            <label class="field-label" for="status">Status <span style="color:var(--coral)">*</span></label>
                            <select class="field-input custom-select {{ $errors->has('status') ? 'is-error' : '' }}"
                                    id="status" name="status" required>
                                @foreach(['upcoming' => 'Upcoming', 'active' => 'Active', 'completed' => 'Completed', 'archived' => 'Archived'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status', $academicSession->status) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label" style="visibility:hidden;">Current</label>
                            <label class="toggle-row" for="is_current" id="current-toggle-label" style="height:fit-content;">
                                <input type="checkbox" id="is_current" name="is_current" value="1"
                                       {{ old('is_current', $academicSession->is_current) ? 'checked' : '' }}>
                                <div class="toggle-switch" id="current-switch"></div>
                                <div class="toggle-text">
                                    Set as Current
                                    <small>Active for timetabling</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="mb-field field-row-2">
                        <div class="field-group">
                            <label class="field-label" for="start_date">Start Date <span style="color:var(--coral)">*</span></label>
                            <input type="date" class="field-input {{ $errors->has('start_date') ? 'is-error' : '' }}"
                                   id="start_date" name="start_date"
                                   value="{{ old('start_date', $academicSession->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label" for="end_date">End Date <span style="color:var(--coral)">*</span></label>
                            <input type="date" class="field-input {{ $errors->has('end_date') ? 'is-error' : '' }}"
                                   id="end_date" name="end_date"
                                   value="{{ old('end_date', $academicSession->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="field-group">
                        <label class="field-label" for="description">Description <span style="color:var(--slate-300);font-weight:400;text-transform:none;letter-spacing:0;">optional</span></label>
                        <textarea class="field-input {{ $errors->has('description') ? 'is-error' : '' }}"
                                  id="description" name="description" rows="3"
                                  placeholder="Any notes about this session...">{{ old('description', $academicSession->description) }}</textarea>
                        @error('description')
                            <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                </div>
                <div class="form-footer">
                    <a href="{{ route('admin.academic-sessions.index') }}" class="btn-outline-soft">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn-premium">
                        <i class="bi bi-check2-circle"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="danger-card stagger-3">
            <div class="danger-card-header">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <h2>Danger Zone</h2>
            </div>
            <div class="danger-card-body">
                <div class="danger-text">
                    Delete this session
                    <small>This permanently removes the session and all associated timetable data. This cannot be undone.</small>
                </div>
                <form action="{{ route('admin.academic-sessions.destroy', $academicSession) }}" method="POST"
                      class="confirm-form"
                      data-confirm-title="Delete Academic Session"
                      data-confirm-msg="This will permanently delete &ldquo;{{ addslashes($academicSession->name) }}&rdquo; and all its timetable data. This action cannot be undone."
                      data-confirm-icon="bi-trash3-fill"
                      data-confirm-color="#ff7f50"
                      data-confirm-btn="Delete Session"
                      style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-danger btn-confirm-trigger">
                        <i class="bi bi-trash3"></i> Delete Session
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- Custom Confirm Modal --}}
<div id="confirmModal" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.18);width:100%;max-width:400px;margin:1rem;overflow:hidden;animation:slideUp .25s cubic-bezier(.34,1.56,.64,1);">
        <div style="padding:1.4rem 1.5rem 1rem;display:flex;align-items:center;gap:.85rem;">
            <div id="confirmIconWrap" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                <i id="confirmIcon" class="bi"></i>
            </div>
            <h3 id="confirmTitle" style="font-size:1rem;font-weight:800;color:#0f172a;margin:0;"></h3>
        </div>
        <div style="padding:0 1.5rem 1.25rem;">
            <p id="confirmMsg" style="font-size:.875rem;color:#64748b;margin:0;line-height:1.6;"></p>
        </div>
        <div style="padding:1rem 1.5rem;background:#f8fafc;display:flex;justify-content:flex-end;gap:.65rem;border-top:1px solid #f1f5f9;">
            <button id="confirmCancel" style="padding:.6rem 1.2rem;border-radius:10px;border:1.5px solid #e2e8f0;background:#fff;color:#334155;font-size:.85rem;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="confirmOk" style="padding:.6rem 1.4rem;border-radius:10px;border:none;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;">Confirm</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* Toggle switch visual sync */
    const cb = document.getElementById('is_current');
    const sw = document.getElementById('current-switch');
    function syncToggle() {
        if (!sw) return;
        sw.style.background = cb.checked ? 'var(--teal)' : '';
        sw.style.setProperty('--dot-left', cb.checked ? '21px' : '3px');
        // Force ::after position via attribute
        if (cb.checked) {
            sw.setAttribute('data-checked', '1');
        } else {
            sw.removeAttribute('data-checked');
        }
    }
    cb?.addEventListener('change', syncToggle);
    if (cb) syncToggle();

    /* Date range guard */
    const startInput = document.getElementById('start_date');
    const endInput   = document.getElementById('end_date');
    startInput?.addEventListener('change', () => {
        if (startInput.value) endInput.min = startInput.value;
    });

    /* Custom Confirm Modal */
    const confirmModal   = document.getElementById('confirmModal');
    const confirmTitle   = document.getElementById('confirmTitle');
    const confirmMsg     = document.getElementById('confirmMsg');
    const confirmIcon    = document.getElementById('confirmIcon');
    const confirmIconWrap= document.getElementById('confirmIconWrap');
    const confirmOkBtn   = document.getElementById('confirmOk');
    const confirmCancel  = document.getElementById('confirmCancel');
    let   pendingForm    = null;

    function showConfirm({ title, msg, icon, color, btnLabel }) {
        confirmTitle.textContent = title    || 'Are you sure?';
        confirmMsg.innerHTML     = msg      || 'This action cannot be undone.';
        confirmIcon.className    = `bi ${icon || 'bi-exclamation-triangle-fill'}`;
        const c = color || '#ef4444';
        confirmIconWrap.style.background = c + '18';
        confirmIconWrap.style.color      = c;
        confirmOkBtn.style.background    = c;
        confirmOkBtn.textContent = btnLabel || 'Confirm';
        confirmModal.style.display = 'flex';
    }

    function closeConfirm() { confirmModal.style.display = 'none'; pendingForm = null; }

    document.querySelectorAll('.confirm-form').forEach(form => {
        const trigger = form.querySelector('.btn-confirm-trigger');
        if (!trigger) return;
        trigger.addEventListener('click', e => {
            e.preventDefault();
            pendingForm = form;
            showConfirm({
                title: form.dataset.confirmTitle, msg: form.dataset.confirmMsg,
                icon: form.dataset.confirmIcon, color: form.dataset.confirmColor,
                btnLabel: form.dataset.confirmBtn,
            });
        });
    });

    confirmOkBtn?.addEventListener('click', () => { if (pendingForm) { closeConfirm(); pendingForm.submit(); } });
    confirmCancel?.addEventListener('click', closeConfirm);
    confirmModal?.addEventListener('click', e => { if (e.target === confirmModal) closeConfirm(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeConfirm(); });
    confirmCancel?.addEventListener('mouseenter', () => confirmCancel.style.background = '#f1f5f9');
    confirmCancel?.addEventListener('mouseleave', () => confirmCancel.style.background = '#fff');
});
</script>

<style>
/* Toggle switch checked state via attribute */
.toggle-switch[data-checked] { background: var(--teal) !important; }
.toggle-switch[data-checked]::after { left: 21px; }
</style>
@endpush
