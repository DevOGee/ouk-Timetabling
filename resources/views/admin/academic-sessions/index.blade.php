@extends('layouts.app')
@section('title', 'Academic Sessions')

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
    --green:       #10b981;
    --green-bg:    rgba(16, 185, 129, 0.1);
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
    margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap;
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
.custom-table tr.current-session-row {
    background: rgba(3, 123, 144, 0.02);
}
.custom-table tr:hover td {
    background: var(--slate-50);
}
.custom-table tr.current-session-row:hover td {
    background: rgba(3, 123, 144, 0.04);
}

.session-link {
    font-weight: 800;
    color: var(--slate-900);
    text-decoration: none;
    transition: color 0.15s;
}
.session-link:hover {
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
.badge-status-active { background: var(--green-bg); color: var(--green); }
.badge-status-upcoming { background: var(--purple-bg); color: var(--purple); }
.badge-status-completed { background: var(--slate-100); color: var(--slate-500); }
.badge-status-archived { background: var(--slate-200); color: var(--slate-700); }

.badge-current {
    background: var(--teal-bg);
    color: var(--teal);
    border: 1px solid rgba(3, 123, 144, 0.2);
    font-size: .72rem;
    font-weight: 700;
    padding: .2rem .5rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-set-current {
    background: #fff;
    color: var(--slate-700);
    border: 1px solid var(--slate-200);
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.35rem 0.7rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-set-current:hover {
    background: var(--teal-bg);
    color: var(--teal);
    border-color: var(--teal);
}

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
.action-btn-warning:hover {
    border-color: #f59e0b;
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
}
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">
    <div class="header-row">
        <div>
            <h1>Academic Sessions</h1>
            <p style="font-size: .85rem; color: var(--slate-500); margin: 0.2rem 0 0 0;">Configure active semesters, roll forward mappings, and publish sessions.</p>
        </div>
        <div>
            <a href="{{ route('admin.academic-sessions.create') }}" class="btn-premium">
                <i class="bi bi-plus-lg"></i> Add New Session
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); color: rgb(16, 185, 129); padding: 1rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.85rem; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        @if($sessions->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 2.5rem; color: var(--slate-300); margin-bottom: 1rem;"><i class="bi bi-calendar-x"></i></div>
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--slate-700);">No academic sessions found</h3>
                <p style="color: var(--slate-500); font-size: .9rem;">Add a new academic session to manage course units and schedules.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Current Active</th>
                            <th style="text-align: right; padding-right: 2rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr class="{{ $session->is_current ? 'current-session-row' : '' }}">
                                <td>
                                    <a href="{{ route('admin.academic-sessions.show', $session) }}" class="session-link">
                                        {{ $session->name }}
                                    </a>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: var(--slate-900);">{{ $session->code }}</span>
                                </td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--slate-500); font-weight: 600;">
                                        <i class="bi bi-clock"></i> {{ $session->duration }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'badge-status-' . ($session->status ?? 'completed');
                                    @endphp
                                    <span class="badge-pill {{ $statusClass }}">
                                        <i class="bi bi-circle-fill" style="font-size: 5px;"></i> {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($session->is_current)
                                        <span class="badge-current">
                                            <i class="bi bi-check-circle-fill"></i> Current Active
                                        </span>
                                    @else
                                        <form action="{{ route('admin.academic-sessions.set-current', $session) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-set-current">
                                                Set as Current
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td style="text-align: right; padding-right: 2rem;">
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                        <a href="{{ route('admin.academic-sessions.edit', $session) }}" class="action-btn" title="Edit Session">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if ($session->status !== 'archived')
                                            <form action="{{ route('admin.academic-sessions.archive', $session) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to archive this session?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-btn action-btn-warning" title="Archive Session">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (!$session->timetables()->exists())
                                            <form action="{{ route('admin.academic-sessions.destroy', $session) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this session?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn action-btn-danger" title="Delete Session">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($sessions->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--slate-100);">
                    {{ $sessions->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
