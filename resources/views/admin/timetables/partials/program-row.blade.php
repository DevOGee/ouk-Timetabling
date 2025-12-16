@php
    $timetable = $program->programmeTimetables->first();
    $status = $program->calculated_status ?? 'not_started';
    $stats = $program->stats ?? ['completed' => 0, 'inProgress' => 0, 'notStarted' => 0, 'total' => 0];
    
    $statusText = 'Not Started';
    $statusClass = 'secondary';
    
    switch($status) {
        case 'published':
            $statusText = 'Published';
            $statusClass = 'success';
            break;
        case 'ready':
            $statusText = 'Ready';
            $statusClass = 'info';
            break;
        case 'in_progress':
            $statusText = 'In Progress';
            $statusClass = 'primary';
            break;
        default:
            $statusText = 'Not Started';
            $statusClass = 'secondary';
    }
@endphp

<tr class="program-row" data-status="{{ $status }}">
    <td>{{ $loop->iteration }}</td>
    <td>
        <strong>{{ $program->programme_code }}:</strong> {{ $program->name }}
    </td>
    <td>
        <span class="badge bg-{{ $statusClass }}" 
              data-bs-toggle="tooltip" 
              title="Scheduled: {{ $stats['completed'] }} | In Progress: {{ $stats['inProgress'] }} | Not Started: {{ $stats['notStarted'] }}">
            {{ $statusText }}
        </span>
    </td>
    <td class="text-end">
        @if($status === 'in_progress' && !$timetable)
            <a href="{{ route('admin.timetables.create', ['programme_id' => $program->id, 'academic_session_id' => $academicSession->id]) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Create Timetable
            </a>
        @else
            <div class="btn-group" role="group">
                @if(auth()->user()->hasRole('admin'))
                    @if($status === 'published')
                        <button class="btn btn-sm btn-outline-warning btn-unpublish" 
                                data-timetable-id="{{ $timetable->id }}" 
                                data-program-name="{{ $program->name }}"
                                title="Unpublish">
                            <i class="bi bi-x-circle"></i> Unpublish
                        </button>
                    @elseif($status === 'ready')
                        <button class="btn btn-sm btn-success btn-publish" 
                                data-timetable-id="{{ $timetable->id }}" 
                                data-program-name="{{ $program->name }}"
                                title="Publish">
                            <i class="bi bi-check-circle"></i> Publish
                        </button>
                    @endif
                @endif
                
                @if($timetable)
                    <a href="/academic-sessions/{{ $academicSession->id }}/programmes/{{ $program->id }}/scheduling" class="btn btn-sm btn-outline-primary" title="Edit">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endif
            </div>
        @endif
    </td>
</tr>
