@if(!isset($currentSession))
    <tr>
        <td colspan="4" class="text-center py-4">
            <div class="text-muted">
                <i class="bi bi-exclamation-triangle me-1"></i>
                No active academic session found.
            </div>
        </td>
    </tr>
@else
    @forelse($programs as $program)
        @php
            // Get the timetable and mapping status for this program
            $timetable = $program->programmeTimetables->first();
            
            // Get mapping counts for this program in the current session
            $mappings = $program->courseUnitMappings()
                ->where('academic_session_id', $currentSession->id)
                ->get();
            
            // Count completed and in-progress mappings based on actual scheduled times
            $completed = 0;
            $inProgress = 0;
            $notStarted = 0;
        
        foreach ($mappings as $mapping) {
            $hasMorning = $mapping->morning_start_time !== null && $mapping->morning_duration !== null;
            $hasEvening = $mapping->evening_start_time !== null && $mapping->evening_duration !== null;
            
            if ($hasMorning || $hasEvening) {
                $completed++;
            } elseif ($mapping->day_id !== null || $mapping->user_id !== null) {
                $inProgress++;
            } else {
                $notStarted++;
            }
        }
        
        $totalMappings = $mappings->count();
        
        // Determine status
        $status = 'not_started';
        $statusText = 'Not Started';
        $statusClass = 'secondary';
        
        if ($timetable && $timetable->status === 'published') {
            $status = 'published';
            $statusText = 'Published';
            $statusClass = 'success';
        } elseif ($completed > 0) {
            $status = 'ready';
            $statusText = 'Ready to Publish';
            $statusClass = 'info';
        } elseif ($inProgress > 0) {
            $status = 'in_progress';
            $statusText = 'In Progress';
            $statusClass = 'primary';
        }
    @endphp
    <tr data-school-id="{{ $program->school_id }}">
        <td>{{ $loop->iteration }}</td>
        <td>
            <strong>{{ $program->programme_code }}:</strong> {{ $program->name }}
        </td>
        <td>
            <span class="badge bg-{{ $statusClass }}" 
                  data-bs-toggle="tooltip" 
                  title="Scheduled: {{ $completed }} | In Progress: {{ $inProgress }} | Not Started: {{ $notStarted }}">
                {{ $statusText }}
            </span>
        </td>
        <td class="text-end">
            @if($timetable)
                @if($timetable->status === 'published')
                    <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $currentSession->id, 'programme' => $program->id]) }}" 
                       class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button onclick="unpublishTimetable({{ $timetable->id }}, this)" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-x-circle"></i> Unpublish
                    </button>
                @else
                    <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $currentSession->id, 'programme' => $program->id]) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @if($status === 'ready')
                        <button onclick="publishTimetable({{ $timetable->id }}, this)" class="btn btn-sm btn-success">
                            <i class="bi bi-check-circle"></i> Publish
                        </button>
                    @endif
                @endif
            @else
                <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $currentSession->id, 'programme' => $program->id]) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Create
                </a>
            @endif
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center py-4">
                <div class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    No programs found for the current academic session.
                </div>
                @if(isset($currentSession))
                    <a href="{{ route('admin.academic-sessions.show', $currentSession) }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="bi bi-plus-circle me-1"></i> Map Programs to This Session
                    </a>
                @endif
            </td>
        </tr>
    @endforelse
@endif
