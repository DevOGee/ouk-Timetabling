@php
    $timetable = $program->programmeTimetables->first();
    $status = $program->calculated_status ?? 'not_started';
    $stats = $program->stats ?? ['completed' => 0, 'inProgress' => 0, 'notStarted' => 0, 'total' => 0];
    
    $statusText = 'Not Started';
    $statusColor = 'var(--slate-500)';
    $statusBg = 'var(--slate-100)';
    
    switch($status) {
        case 'published':
            $statusText = 'Published';
            $statusColor = 'var(--green)';
            $statusBg = 'rgba(16,185,129,.1)';
            break;
        case 'ready':
            $statusText = 'Ready';
            $statusColor = 'var(--teal)';
            $statusBg = 'var(--teal-bg)';
            break;
        case 'in_progress':
            $statusText = 'In Progress';
            $statusColor = '#3b82f6';
            $statusBg = 'rgba(59,130,246,.1)';
            break;
        default:
            $statusText = 'Not Started';
            $statusColor = 'var(--slate-500)';
            $statusBg = 'var(--slate-100)';
    }
@endphp

<tr class="program-row" data-status="{{ $status }}" style="border-bottom:1px solid var(--slate-100);transition:background .2s;background:#fff;" onmouseover="this.style.background='var(--slate-50)'" onmouseout="this.style.background='#fff'">
    <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;font-size:.85rem;color:var(--slate-400);font-weight:600;">
        {{ $index ?? '' }}
    </td>
    <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;">
        <div style="font-weight:600;color:var(--slate-900);font-size:.9rem;">
            {{ $program->name }}
        </div>
        <div style="font-size:.8rem;color:var(--slate-500);margin-top:.2rem;">
            {{ $program->programme_code ?? $program->code ?? 'N/A' }}
        </div>
    </td>
    <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;text-align:center;">
        <span style="display:inline-flex;align-items:center;padding:.25rem .75rem;border-radius:100px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $statusColor }};background:{{ $statusBg }};"
              data-bs-toggle="tooltip" 
              title="Scheduled: {{ $stats['completed'] }} | In Progress: {{ $stats['inProgress'] }} | Not Started: {{ $stats['notStarted'] }}">
            {{ $statusText }}
        </span>
    </td>
    <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;text-align:right;">
        <div class="d-flex justify-content-end gap-2">
            @if($status === 'in_progress' && !$timetable)
                <a href="{{ route('admin.timetables.create', ['programme_id' => $program->id, 'academic_session_id' => $academicSession->id]) }}" class="btn-premium" style="padding:.4rem .6rem;">
                    <i class="bi bi-plus-circle"></i> Create Timetable
                </a>
            @else
                @if(auth()->user()->hasRole('admin'))
                    @if($status === 'published')
                        <button class="btn-outline-soft btn-unpublish" style="padding:.4rem .6rem;color:#f59e0b;border-color:rgba(245,158,11,.4);"
                                data-timetable-id="{{ $timetable->id }}" 
                                data-program-name="{{ $program->name }}"
                                title="Unpublish">
                            <i class="bi bi-x-circle"></i> Unpublish
                        </button>
                    @elseif($status === 'ready')
                        <button class="btn-outline-soft btn-publish" style="padding:.4rem .6rem;color:var(--green);border-color:rgba(16,185,129,.4);"
                                data-timetable-id="{{ $timetable->id }}" 
                                data-program-name="{{ $program->name }}"
                                title="Publish">
                            <i class="bi bi-check-circle"></i> Publish
                        </button>
                    @endif
                @endif
                
                @if($timetable)
                    <a href="/academic-sessions/{{ $academicSession->id }}/programmes/{{ $program->id }}/scheduling" class="btn-outline-soft" style="padding:.4rem .6rem;" title="Edit">
                        <i class="bi bi-pencil" style="color:var(--teal);"></i> Edit
                    </a>
                @endif
            @endif
        </div>
    </td>
</tr>
