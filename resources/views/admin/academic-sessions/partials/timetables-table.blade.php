@if($programmes->count() > 0)
    <div class="table-responsive" style="border-radius:0 0 var(--radius-md) var(--radius-md);">
        <table class="table premium-table mb-0" style="width:100%;border-collapse:collapse;">
            <thead style="background:var(--slate-50);border-bottom:1px solid var(--slate-200);">
                <tr>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">Programme Details</th>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">School</th>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:center;">Status</th>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;width:150px;">Progress</th>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programmes as $programme)
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
                                'color' => 'var(--slate-500)',
                                'bg' => 'var(--slate-100)',
                                'progress' => 0,
                                'has_timetable' => false,
                                'is_published' => false,
                                'is_complete' => false
                            ];
                        } elseif ($filledFields === $totalPossibleFields) {
                            $status = [
                                'label' => 'Done',
                                'color' => 'var(--green)',
                                'bg' => 'rgba(16,185,129,.1)',
                                'progress' => $progress,
                                'has_timetable' => true,
                                'is_published' => false,
                                'is_complete' => true
                            ];
                        } elseif ($filledFields > 0) {
                            $status = [
                                'label' => 'In Progress',
                                'color' => '#3b82f6',
                                'bg' => 'rgba(59,130,246,.1)',
                                'progress' => $progress,
                                'has_timetable' => true,
                                'is_published' => false,
                                'is_complete' => false
                            ];
                        } else {
                            $status = [
                                'label' => 'Not Started',
                                'color' => 'var(--slate-500)',
                                'bg' => 'var(--slate-100)',
                                'progress' => 0,
                                'has_timetable' => false,
                                'is_published' => false,
                                'is_complete' => false
                            ];
                        }
                    @endphp
                    <tr style="border-bottom:1px solid var(--slate-100);transition:background .2s;background:#fff;" onmouseover="this.style.background='var(--slate-50)'" onmouseout="this.style.background='#fff'">
                        <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;">
                            <div style="font-weight:600;color:var(--slate-900);font-size:.9rem;">
                                {{ $programme->name }}
                            </div>
                            <div style="font-size:.8rem;color:var(--slate-500);margin-top:.2rem;">
                                {{ $programme->programme_code ?? $programme->code ?? 'N/A' }}
                            </div>
                        </td>
                        <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;font-size:.85rem;color:var(--slate-700);">
                            {{ $programme->school->name ?? 'N/A' }}
                        </td>
                        <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;text-align:center;">
                            <span style="display:inline-flex;align-items:center;padding:.25rem .75rem;border-radius:100px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $status['color'] }};background:{{ $status['bg'] }};">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;">
                            <div style="width:100%;height:8px;background:var(--slate-100);border-radius:100px;overflow:hidden;">
                                <div style="height:100%;background:{{ $status['color'] }};width:{{ $status['progress'] }}%;border-radius:100px;transition:width .5s ease;"></div>
                            </div>
                            <div style="font-size:.7rem;font-weight:600;color:var(--slate-500);margin-top:.3rem;text-align:right;">
                                {{ $status['progress'] }}%
                            </div>
                        </td>
                        <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;text-align:right;">
                            @if($status['is_complete'] || $status['label'] === 'In Progress')
                                <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" class="btn-outline-soft" style="padding:.4rem .6rem;" title="Edit Schedule">
                                    <i class="bi bi-pencil" style="color:var(--teal);"></i> Edit
                                </a>
                            @else
                                <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" class="btn-premium" style="padding:.4rem .6rem;" title="Create Schedule">
                                    <i class="bi bi-calendar-plus"></i> Schedule
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Custom Pagination -->
    @if(method_exists($programmes, 'hasPages') && $programmes->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 px-4 pb-4">
            <div style="font-size:.85rem;color:var(--slate-500);">
                Showing <strong>{{ $programmes->firstItem() }}</strong> to <strong>{{ $programmes->lastItem() }}</strong> of <strong>{{ $programmes->total() }}</strong> programmes
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0" style="gap:.25rem;">
                    @if ($programmes->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" style="border:none;background:var(--slate-50);color:var(--slate-400);border-radius:6px;font-size:.85rem;"><i class="bi bi-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $programmes->previousPageUrl() }}&{{ http_build_query(request()->except('page', '_token')) }}" rel="prev" style="border:none;background:var(--slate-50);color:var(--slate-700);border-radius:6px;font-size:.85rem;"><i class="bi bi-chevron-left"></i></a>
                        </li>
                    @endif

                    @for ($page = 1; $page <= $programmes->lastPage(); $page++)
                        <li class="page-item {{ $page == $programmes->currentPage() ? 'active' : '' }}">
                            @if($page == $programmes->currentPage())
                                <span class="page-link" style="border:none;background:var(--teal);color:#fff;border-radius:6px;font-weight:600;font-size:.85rem;">{{ $page }}</span>
                            @else
                                <a class="page-link" href="{{ $programmes->url($page) }}&{{ http_build_query(request()->except('page', '_token')) }}" style="border:none;background:var(--slate-50);color:var(--slate-700);border-radius:6px;font-size:.85rem;">{{ $page }}</a>
                            @endif
                        </li>
                    @endfor

                    @if ($programmes->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $programmes->nextPageUrl() }}&{{ http_build_query(request()->except('page', '_token')) }}" rel="next" style="border:none;background:var(--slate-50);color:var(--slate-700);border-radius:6px;font-size:.85rem;"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" style="border:none;background:var(--slate-50);color:var(--slate-400);border-radius:6px;font-size:.85rem;"><i class="bi bi-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
@else
    <div style="padding:4rem 2rem;text-align:center;">
        <div style="font-size:3rem;color:var(--slate-200);margin-bottom:1rem;"><i class="bi bi-calendar3"></i></div>
        <h4 style="font-size:1.1rem;font-weight:700;color:var(--slate-700);margin-bottom:.5rem;">No Timetables Found</h4>
        <p style="font-size:.9rem;color:var(--slate-500);margin-bottom:0;max-width:400px;margin-left:auto;margin-right:auto;">
            There are no programmes available for scheduling timetables in this session.
        </p>
    </div>
@endif
