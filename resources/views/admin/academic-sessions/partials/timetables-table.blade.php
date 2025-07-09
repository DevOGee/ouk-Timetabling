@if($programmes->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>School</th>
                    <th>Status</th>
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
                            // Safely get timetables collection
                            $timetables = $programme->relationLoaded('timetables') ? $programme->timetables : collect([]);
                            $hasTimetable = $timetables->isNotEmpty();
                            $timetable = $hasTimetable ? $timetables->first() : null;
                            
                            if ($hasTimetable) {
                                // Get pivot data
                                $pivotStatus = $timetable->pivot->status ?? 'draft';
                                $publishedAt = $timetable->pivot->published_at ?? null;
                                $isPublished = $pivotStatus === 'published' && $publishedAt !== null;
                                
                                // Check if all required fields are filled
                                $requiredFields = [
                                    'exam_dates' => !empty($timetable->exam_dates),
                                    'exam_venues' => !empty($timetable->exam_venues),
                                    'exam_times' => !empty($timetable->exam_times),
                                    'timetable_file' => !empty($timetable->timetable_file)
                                ];
                                
                                $filledFields = count(array_filter($requiredFields));
                                $totalFields = count($requiredFields);
                                $progress = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
                                
                                // Determine status based on progress and publication status
                                if ($isPublished) {
                                    $status = [
                                        'label' => 'Published',
                                        'class' => 'success',
                                        'progress' => $progress,
                                        'has_timetable' => true,
                                        'is_published' => true,
                                        'is_complete' => $progress === 100
                                    ];
                                } elseif ($progress === 100) {
                                    $status = [
                                        'label' => 'Ready to Publish',
                                        'class' => 'info',
                                        'progress' => $progress,
                                        'has_timetable' => true,
                                        'is_published' => false,
                                        'is_complete' => true
                                    ];
                                } else {
                                    $status = [
                                        'label' => 'In Progress',
                                        'class' => 'warning',
                                        'progress' => $progress,
                                        'has_timetable' => true,
                                        'is_published' => false,
                                        'is_complete' => false
                                    ];
                                }
                            } else {
                                // No timetable exists
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
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $status['class'] }}" role="progressbar" 
                                         style="width: {{ $status['progress'] }}%" 
                                         aria-valuenow="{{ $status['progress'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="badge bg-{{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                @if($hasTimetable)
                                    <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" class="btn btn-sm btn-outline-primary" title="Edit Schedule">
                                        <i class="bi bi-calendar-plus"></i> Schedule
                                    </a>
                                    <!-- Publish/Unpublish functionality moved to scheduling page -->
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
