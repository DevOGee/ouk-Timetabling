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
                            $totalPossibleFields = $totalMappings * 2; // user_id and day_id for each mapping
                            $filledFields = 0;
                            $completedMappings = 0;
                            
                            foreach ($mappings as $mapping) {
                                // Count filled fields
                                if ($mapping->user_id !== null) $filledFields++;
                                if ($mapping->day_id !== null) $filledFields++;
                                
                                // Count completed mappings (both fields filled)
                                if ($mapping->user_id !== null && $mapping->day_id !== null) {
                                    $completedMappings++;
                                }
                            }
                            
                            // Calculate progress based on filled fields
                            $progress = $totalPossibleFields > 0 
                                ? round(($filledFields / $totalPossibleFields) * 100) 
                                : 0;
                                
                            // For backward compatibility with the view
                            $inProgressMappings = $filledFields - ($completedMappings * 2); // Partially filled mappings
                            
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
                            } elseif ($filledFields === $totalPossibleFields) {
                                $status = [
                                    'label' => 'Done',
                                    'class' => 'success',
                                    'progress' => $progress,
                                    'has_timetable' => true,
                                    'is_published' => false,
                                    'is_complete' => true
                                ];
                            } elseif ($filledFields > 0) {
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
                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $status['class'] }} d-flex align-items-center justify-content-center" 
                                         role="progressbar" 
                                         style="width: {{ $status['progress'] }}%; font-size: 0.75rem;" 
                                         aria-valuenow="{{ $status['progress'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $status['progress'] }}%
                                    </div>
                                </div>
                                {{-- <small>{{ $status['progress'] }}%</small> --}}
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
