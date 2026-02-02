@if($programmes->count() > 0)
    <div class="table-responsive">
        @php
            // Group first by School, then by Department
            $programmesBySchool = $programmes->groupBy(function($programme) {
                return $programme->school->name ?? 'No School';
            });
        @endphp

        @foreach($programmesBySchool as $schoolName => $schoolProgrammes)
            <div class="mt-4 mb-3">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-building"></i> {{ $schoolName }}
                    <span class="badge bg-secondary ms-2">{{ $schoolProgrammes->count() }} Programmes</span>
                </h5>
                
                @php
                    $programmesByDept = $schoolProgrammes->groupBy(function($programme) {
                        return $programme->department->name ?? 'No Department';
                    })->sortKeys();
                @endphp

                @foreach($programmesByDept as $deptName => $deptProgrammes)
                    <div class="ms-3 mb-4">
                        <h6 class="text-secondary mb-2">
                            <i class="bi bi-diagram-3"></i> {{ $deptName }}
                            <small class="text-muted">({{ $deptProgrammes->count() }})</small>
                        </h6>

                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 15%">Code</th>
                                    <th style="width: 35%">Name</th>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 20%">Progress</th>
                                    <th style="width: 15%" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deptProgrammes as $programme)
                                    <tr>
                                        <td>{{ $programme->programme_code ?? $programme->code ?? 'N/A' }}</td>
                                        <td>{{ $programme->name }}</td>
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
                                                
                                            // Determine status
                                            if ($totalMappings === 0) {
                                                $status = [
                                                    'label' => 'No Mappings',
                                                    'class' => 'secondary',
                                                    'progress' => 0,
                                                    'is_complete' => false
                                                ];
                                            } elseif ($filledFields === $totalPossibleFields) {
                                                $status = [
                                                    'label' => 'Done',
                                                    'class' => 'success',
                                                    'progress' => $progress,
                                                    'is_complete' => true
                                                ];
                                            } elseif ($filledFields > 0) {
                                                $status = [
                                                    'label' => 'In Progress',
                                                    'class' => 'warning',
                                                    'progress' => $progress,
                                                    'is_complete' => false
                                                ];
                                            } else {
                                                $status = [
                                                    'label' => 'Not Started',
                                                    'class' => 'secondary',
                                                    'progress' => 0,
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
                                                <div class="progress flex-grow-1 me-2" style="height: 15px;">
                                                    <div class="progress-bar bg-{{ $status['class'] }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $status['progress'] }}%" 
                                                         aria-valuenow="{{ $status['progress'] }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <small class="text-white" style="font-size: 0.7rem;">{{ $status['progress'] }}%</small>
                                                    </div>
                                                </div>
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
                @endforeach
            </div>
        @endforeach
    </div>
    
    <!-- Link to full list if filtered -->
    @if(request('department_id') || request('search'))
        <div class="mt-3 text-center">
             <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">View All Programmes</a>
        </div>
    @endif

@else
    <div class="alert alert-info mb-0">
        No programmes found matching your criteria.
    </div>
@endif
