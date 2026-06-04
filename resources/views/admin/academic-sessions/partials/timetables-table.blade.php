@php
    // Compute totals for the summary bar
    $totalProgrammes  = $programmes->count();
    $doneCount        = 0;
    $inProgressCount  = 0;
    $notStartedCount  = 0;
    $noMappingsCount  = 0;
@endphp

@if($totalProgrammes > 0)
{{-- ── Summary Stats Bar ── --}}
@php
    // Pre-compute per-programme status for the stats bar
    $programmeStatuses = [];
    foreach ($programmes as $p) {
        $m  = $p->courseUnitMappings()->where('academic_session_id', $academicSession->id)->get();
        $tm = $m->count();
        $ff = 0;
        foreach ($m as $mp) {
            if ($mp->user_id !== null) $ff++;
            if ($mp->day_id  !== null) $ff++;
        }
        $tpf = $tm * 2;
        if ($tm === 0)              $programmeStatuses[] = 'none';
        elseif ($ff === $tpf)       $programmeStatuses[] = 'done';
        elseif ($ff > 0)            $programmeStatuses[] = 'progress';
        else                        $programmeStatuses[] = 'pending';
    }
    $doneCount        = count(array_filter($programmeStatuses, fn($s) => $s === 'done'));
    $inProgressCount  = count(array_filter($programmeStatuses, fn($s) => $s === 'progress'));
    $notStartedCount  = count(array_filter($programmeStatuses, fn($s) => $s === 'pending'));
    $noMappingsCount  = count(array_filter($programmeStatuses, fn($s) => $s === 'none'));
@endphp

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--slate-100);border-bottom:1px solid var(--slate-100);">
    <div style="background:#fff;padding:1.1rem 1.5rem;display:flex;align-items:center;gap:.85rem;">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-check-circle-fill" style="color:#10b981;font-size:1.1rem;"></i>
        </div>
        <div>
            <div style="font-size:1.4rem;font-weight:800;color:var(--slate-900);line-height:1;">{{ $doneCount }}</div>
            <div style="font-size:.72rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;letter-spacing:.5px;margin-top:.1rem;">Done</div>
        </div>
    </div>
    <div style="background:#fff;padding:1.1rem 1.5rem;display:flex;align-items:center;gap:.85rem;">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(59,130,246,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-arrow-repeat" style="color:#3b82f6;font-size:1.1rem;"></i>
        </div>
        <div>
            <div style="font-size:1.4rem;font-weight:800;color:var(--slate-900);line-height:1;">{{ $inProgressCount }}</div>
            <div style="font-size:.72rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;letter-spacing:.5px;margin-top:.1rem;">In Progress</div>
        </div>
    </div>
    <div style="background:#fff;padding:1.1rem 1.5rem;display:flex;align-items:center;gap:.85rem;">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(245,158,11,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:1.1rem;"></i>
        </div>
        <div>
            <div style="font-size:1.4rem;font-weight:800;color:var(--slate-900);line-height:1;">{{ $notStartedCount }}</div>
            <div style="font-size:.72rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;letter-spacing:.5px;margin-top:.1rem;">Not Started</div>
        </div>
    </div>
    <div style="background:#fff;padding:1.1rem 1.5rem;display:flex;align-items:center;gap:.85rem;">
        <div style="width:38px;height:38px;border-radius:10px;background:var(--slate-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-dash-circle" style="color:var(--slate-400);font-size:1.1rem;"></i>
        </div>
        <div>
            <div style="font-size:1.4rem;font-weight:800;color:var(--slate-900);line-height:1;">{{ $noMappingsCount }}</div>
            <div style="font-size:.72rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;letter-spacing:.5px;margin-top:.1rem;">No Mappings</div>
        </div>
    </div>
</div>

{{-- ── Programme Cards Grid ── --}}
<div style="padding:1.5rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem;">
    @foreach($programmes as $programme)
        @php
            $mappings          = $programme->courseUnitMappings()->where('academic_session_id', $academicSession->id)->get();
            $totalMappings     = $mappings->count();
            $totalPossible     = $totalMappings * 2;
            $filledFields      = 0;
            $completedMappings = 0;

            foreach ($mappings as $mapping) {
                if ($mapping->user_id !== null) $filledFields++;
                if ($mapping->day_id  !== null) $filledFields++;
                if ($mapping->user_id !== null && $mapping->day_id !== null) $completedMappings++;
            }

            $progress = $totalPossible > 0 ? round(($filledFields / $totalPossible) * 100) : 0;

            if ($totalMappings === 0) {
                $status = ['label' => 'No Mappings', 'color' => 'var(--slate-500)', 'bg' => 'var(--slate-100)', 'icon' => 'bi-dash-circle', 'progress' => 0, 'is_complete' => false];
            } elseif ($filledFields === $totalPossible) {
                $status = ['label' => 'Done',        'color' => '#10b981',           'bg' => 'rgba(16,185,129,.1)',   'icon' => 'bi-check-circle-fill', 'progress' => $progress, 'is_complete' => true];
            } elseif ($filledFields > 0) {
                $status = ['label' => 'In Progress', 'color' => '#3b82f6',           'bg' => 'rgba(59,130,246,.1)',   'icon' => 'bi-arrow-repeat',       'progress' => $progress, 'is_complete' => false];
            } else {
                $status = ['label' => 'Not Started', 'color' => '#f59e0b',           'bg' => 'rgba(245,158,11,.1)',   'icon' => 'bi-hourglass-split',    'progress' => 0,         'is_complete' => false];
            }

            $delay = $loop->index * 0.055;
        @endphp

        <div style="background:#fff;border-radius:14px;border:1px solid rgba(226,232,240,.8);box-shadow:0 2px 8px rgba(15,23,42,.03);padding:1.25rem;display:flex;flex-direction:column;gap:1rem;transition:all .25s ease;animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) {{ $delay }}s both;cursor:default;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(15,23,42,.08)';"
             onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 8px rgba(15,23,42,.03)';">

            {{-- Card Header --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;color:var(--slate-900);font-size:.92rem;line-height:1.35;word-break:break-word;">
                        {{ $programme->name }}
                    </div>
                    <div style="font-size:.78rem;color:var(--slate-500);margin-top:.3rem;display:flex;align-items:center;gap:.4rem;">
                        <i class="bi bi-upc-scan" style="font-size:.7rem;"></i>
                        {{ $programme->programme_code ?? $programme->code ?? 'N/A' }}
                        @if($programme->school)
                        <span style="color:var(--slate-300);">·</span>
                        <i class="bi bi-building" style="font-size:.7rem;"></i>
                        {{ Str::limit($programme->school->name, 22) }}
                        @endif
                    </div>
                </div>
                <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .7rem;border-radius:100px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;color:{{ $status['color'] }};background:{{ $status['bg'] }};flex-shrink:0;">
                    <i class="bi {{ $status['icon'] }}" style="font-size:.75rem;"></i>
                    {{ $status['label'] }}
                </span>
            </div>

            {{-- Progress Bar --}}
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
                    <span style="font-size:.72rem;font-weight:600;color:var(--slate-500);text-transform:uppercase;letter-spacing:.5px;">Schedule Progress</span>
                    <span style="font-size:.78rem;font-weight:700;color:{{ $status['color'] }};">{{ $status['progress'] }}%</span>
                </div>
                <div style="height:7px;background:var(--slate-100);border-radius:100px;overflow:hidden;">
                    <div style="height:100%;border-radius:100px;background:linear-gradient(90deg, {{ $status['color'] }}, {{ $status['color'] }}cc);width:{{ $status['progress'] }}%;transition:width 1.2s cubic-bezier(.34,1.56,.64,1) {{ $delay + 0.2 }}s;animation:slideProgress 1.2s cubic-bezier(.34,1.56,.64,1) {{ $delay + 0.2 }}s both;"></div>
                </div>
            </div>

            {{-- Stats Row --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;">
                <div style="background:var(--slate-50);border-radius:8px;padding:.6rem .75rem;text-align:center;">
                    <div style="font-size:1.1rem;font-weight:800;color:var(--slate-900);">{{ $totalMappings }}</div>
                    <div style="font-size:.65rem;color:var(--slate-500);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-top:.1rem;">Units</div>
                </div>
                <div style="background:var(--slate-50);border-radius:8px;padding:.6rem .75rem;text-align:center;">
                    <div style="font-size:1.1rem;font-weight:800;color:#10b981;">{{ $completedMappings }}</div>
                    <div style="font-size:.65rem;color:var(--slate-500);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-top:.1rem;">Mapped</div>
                </div>
                <div style="background:var(--slate-50);border-radius:8px;padding:.6rem .75rem;text-align:center;">
                    <div style="font-size:1.1rem;font-weight:800;color:{{ $totalMappings > 0 && $completedMappings < $totalMappings ? '#f59e0b' : 'var(--slate-400)' }};">{{ $totalMappings - $completedMappings }}</div>
                    <div style="font-size:.65rem;color:var(--slate-500);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-top:.1rem;">Pending</div>
                </div>
            </div>

            {{-- Action --}}
            <div style="border-top:1px solid var(--slate-100);padding-top:1rem;display:flex;justify-content:flex-end;">
                @if($status['is_complete'] || $status['label'] === 'In Progress')
                    <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}"
                       style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;background:#fff;color:var(--teal);border:1.5px solid rgba(3,123,144,.3);text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='rgba(3,123,144,.08)';this.style.borderColor='var(--teal)';"
                       onmouseout="this.style.background='#fff';this.style.borderColor='rgba(3,123,144,.3)';">
                        <i class="bi bi-pencil-square"></i> Edit Schedule
                    </a>
                @elseif($totalMappings > 0)
                    <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}"
                       style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;background:var(--teal);color:#fff;border:none;text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='var(--teal-dark)';"
                       onmouseout="this.style.background='var(--teal)';">
                        <i class="bi bi-calendar-plus"></i> Start Scheduling
                    </a>
                @else
                    <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;background:var(--slate-100);color:var(--slate-400);border:1.5px solid var(--slate-200);">
                        <i class="bi bi-lock"></i> Map Units First
                    </span>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- ── Pagination ── --}}
@if(method_exists($programmes, 'hasPages') && $programmes->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem 1.5rem;border-top:1px solid var(--slate-100);background:var(--slate-50);">
        <div style="font-size:.82rem;color:var(--slate-500);">
            Showing <strong style="color:var(--slate-800);">{{ $programmes->firstItem() }}</strong> –
            <strong style="color:var(--slate-800);">{{ $programmes->lastItem() }}</strong>
            of <strong style="color:var(--slate-800);">{{ $programmes->total() }}</strong> programmes
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0" style="gap:.3rem;">
                @if($programmes->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" style="border:none;background:#fff;color:var(--slate-300);border-radius:8px;border:1.5px solid var(--slate-200);width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $programmes->previousPageUrl() }}&{{ http_build_query(request()->except('page', '_token')) }}" style="border:none;background:#fff;color:var(--slate-700);border-radius:8px;border:1.5px solid var(--slate-200);width:32px;height:32px;display:flex;align-items:center;justify-content:center;transition:all .15s;" onmouseover="this.style.borderColor='var(--teal)';this.style.color='var(--teal)';" onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)';"><i class="bi bi-chevron-left"></i></a>
                    </li>
                @endif

                @for ($page = 1; $page <= $programmes->lastPage(); $page++)
                    <li class="page-item">
                        @if($page == $programmes->currentPage())
                            <span class="page-link" style="border:none;background:var(--teal);color:#fff;border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $programmes->url($page) }}&{{ http_build_query(request()->except('page', '_token')) }}" style="border:none;background:#fff;color:var(--slate-700);border-radius:8px;border:1.5px solid var(--slate-200);width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:.85rem;transition:all .15s;" onmouseover="this.style.borderColor='var(--teal)';this.style.color='var(--teal)';" onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)';">{{ $page }}</a>
                        @endif
                    </li>
                @endfor

                @if($programmes->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $programmes->nextPageUrl() }}&{{ http_build_query(request()->except('page', '_token')) }}" style="border:none;background:#fff;color:var(--slate-700);border-radius:8px;border:1.5px solid var(--slate-200);width:32px;height:32px;display:flex;align-items:center;justify-content:center;transition:all .15s;" onmouseover="this.style.borderColor='var(--teal)';this.style.color='var(--teal)';" onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)';"><i class="bi bi-chevron-right"></i></a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" style="border:none;background:#fff;color:var(--slate-300);border-radius:8px;border:1.5px solid var(--slate-200);width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif

@else
{{-- ── Empty State ── --}}
<div style="padding:5rem 2rem;text-align:center;animation:fadeIn .5s cubic-bezier(.34,1.56,.64,1) both;">
    <div style="display:inline-flex;align-items:center;justify-content:center;width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,rgba(3,123,144,.08),rgba(3,123,144,.02));box-shadow:0 12px 30px rgba(15,23,42,.06);margin-bottom:1.5rem;">
        <i class="bi bi-calendar-x" style="font-size:2.2rem;color:var(--slate-400);"></i>
    </div>
    <h4 style="font-size:1.2rem;font-weight:800;color:var(--slate-800);margin-bottom:.5rem;">No Timetables Yet</h4>
    <p style="font-size:.88rem;color:var(--slate-500);max-width:380px;margin:0 auto;line-height:1.7;">
        No programmes with course unit mappings found. Map course units to programmes first, then return here to schedule timetables.
    </p>
</div>
@endif
