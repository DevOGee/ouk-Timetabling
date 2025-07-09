@if($programmes->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>School</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programmes as $programme)
                    <tr>
                        <td>{{ $programme->programme_code ?? $programme->code ?? 'N/A' }}</td>
                        <td>{{ $programme->name }}</td>
                        <td>{{ $programme->school->name ?? 'N/A' }}</td>
                        @php
                            // Get all course unit mappings for this programme
                            $mappings = $programme->courseUnitMappings()
                                ->where('academic_session_id', $academicSession->id)
                                ->get();
                            
                            $totalMappings = $mappings->count();
                            $completedMappings = 0;
                            $inProgressMappings = 0;
                            
                            foreach ($mappings as $mapping) {
                                // Check if all required fields are filled for this mapping
                                $hasMorning = $mapping->morning_start_time !== null && $mapping->morning_duration !== null;
                                $hasEvening = $mapping->evening_start_time !== null && $mapping->evening_duration !== null;
                                
                                if ($hasMorning || $hasEvening) {
                                    $completedMappings++;
                                } elseif ($mapping->day_id !== null || $mapping->user_id !== null) {
                                    $inProgressMappings++;
                                }
                            }
                            
                            // Calculate progress percentage
                            $progress = $totalMappings > 0 ? round(($completedMappings / $totalMappings) * 100) : 0;
                            
                            // Determine status
                            if ($totalMappings === 0) {
                                $status = [
                                    'label' => 'No Mappings',
                                    'class' => 'secondary',
                                    'progress' => 0,
                                    'has_timetable' => false,
                                    'is_published' => false,
                                    'is_complete' => false
                                ];
                            } elseif ($completedMappings === $totalMappings) {
                                $status = [
                                    'label' => 'Done',
                                    'class' => 'success',
                                    'progress' => $progress,
                                    'has_timetable' => true,
                                    'is_published' => false,
                                    'is_complete' => true
                                ];
                            } elseif ($completedMappings > 0 || $inProgressMappings > 0) {
                                $status = [
                                    'label' => 'In Progress',
                                    'class' => 'warning',
                                    'progress' => $progress,
                                    'has_timetable' => true,
                                    'is_published' => false,
                                    'is_complete' => false
                                ];
                            } else {
                                $status = [
                                    'label' => 'Not Started',
                                    'class' => 'secondary',
                                    'progress' => 0,
                                    'has_timetable' => false,
                                    'is_published' => false,
                                    'is_complete' => false
                                ];
                            }
                        @endphp
                        <td>
                            <span class="badge bg-{{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $status['class'] }}" role="progressbar" 
                                         style="width: {{ $status['progress'] }}%" 
                                         aria-valuenow="{{ $status['progress'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small>{{ $status['progress'] }}%</small>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                @if($status['is_complete'] || $status['label'] === 'In Progress')
                                    <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" class="btn btn-sm btn-outline-primary" title="Edit Schedule">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @else
                                    <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" class="btn btn-sm btn-primary" title="Create Schedule">
                                        <i class="bi bi-calendar-plus"></i> Schedule
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(method_exists($programmes, 'hasPages') && $programmes->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $programmes->links() }}
        </div>
    @endif
@else
    <div class="alert alert-info mb-0">
        No programmes found for this academic session.
    </div>
@endif
