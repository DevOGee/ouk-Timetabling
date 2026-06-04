@if(session('success'))
    <div class="alert alert-success" style="border:none;border-left:4px solid var(--green);background:rgba(16,185,129,.1);color:var(--green);border-radius:var(--radius-sm);margin-bottom:1rem;padding:1rem;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="border:none;border-left:4px solid var(--coral);background:rgba(255,127,80,.1);color:var(--coral);border-radius:var(--radius-sm);margin-bottom:1rem;padding:1rem;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
@endif

<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.academic-sessions.bulk-upload', $academicSession) }}" class="btn-outline-soft" style="background:#fff;">
        <i class="bi bi-upload"></i> Bulk Upload Mappings
    </a>
</div>

@if($programmes->count() > 0)
    <div class="table-responsive" style="border-radius:0 0 var(--radius-md) var(--radius-md);">
        <table class="table premium-table mb-0" style="width:100%;border-collapse:collapse;">
            <thead style="background:var(--slate-50);border-bottom:1px solid var(--slate-200);">
                <tr>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">Programme Details</th>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">School</th>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:center;">Course Mapping</th>
                    <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programmes as $programme)
                    <tr style="border-bottom:1px solid var(--slate-100);transition:all .25s ease;background:#fff;animation:fadeIn 0.4s cubic-bezier(0.34,1.56,0.64,1) {{ $loop->index * 0.06 }}s both;" onmouseover="this.style.background='var(--slate-50)';this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#fff';this.style.transform='none';">
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
                            @php
                                $mappingCount = $programme->mapped_course_units_count ?? 0;
                                $mappingBg = $mappingCount > 0 ? 'rgba(3,123,144,.1)' : 'var(--slate-100)';
                                $mappingColor = $mappingCount > 0 ? 'var(--teal)' : 'var(--slate-500)';
                            @endphp
                            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 .6rem;background:{{ $mappingBg }};color:{{ $mappingColor }};border-radius:100px;font-weight:700;font-size:.85rem;">
                                {{ $mappingCount }}
                            </span>
                        </td>
                        <td style="padding:1rem 1.5rem;vertical-align:middle;border:none;text-align:right;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                   class="btn-outline-soft" style="padding:.4rem .6rem;" title="Manage Schedule">
                                    <i class="bi bi-calendar-plus" style="color:var(--teal);"></i>
                                </a>
                                <a href="{{ route('admin.academic-sessions.programmes.map-course-units', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                   class="btn-outline-soft" style="padding:.4rem .6rem;" title="Map Course Units">
                                    <i class="bi bi-list-check" style="color:var(--slate-700);"></i>
                                </a>
                                @if(!auth()->user()->hasRole('timetabler'))
                                <form action="{{ route('admin.academic-sessions.programmes.detach', ['academicSession' => $academicSession, 'programme' => $programme]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-outline-soft btn-confirm-trigger" style="padding:.4rem .6rem;"
                                            data-confirm-title="Remove Programme"
                                            data-confirm-text="Are you sure you want to remove this programme from the session?"
                                            data-confirm-color="#ff7f50"
                                            data-confirm-icon="bi-trash">
                                        <i class="bi bi-trash" style="color:var(--coral);"></i>
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
    
    <!-- Custom Pagination -->
    @if(method_exists($programmes, 'hasPages') && $programmes->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 px-4 pb-4">
            <div style="font-size:.85rem;color:var(--slate-500);">
                Showing <strong>{{ $programmes->firstItem() }}</strong> to <strong>{{ $programmes->lastItem() }}</strong> of <strong>{{ $programmes->total() }}</strong> programmes
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0" style="gap:.25rem;">
                    {{-- Previous Page Link --}}
                    @if ($programmes->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" style="border:none;background:var(--slate-50);color:var(--slate-400);border-radius:6px;font-size:.85rem;"><i class="bi bi-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $programmes->previousPageUrl() }}&{{ http_build_query(request()->except('page', '_token')) }}" rel="prev" style="border:none;background:var(--slate-50);color:var(--slate-700);border-radius:6px;font-size:.85rem;"><i class="bi bi-chevron-left"></i></a>
                        </li>
                    @endif

                    {{-- Page Number Links --}}
                    @for ($page = 1; $page <= $programmes->lastPage(); $page++)
                        <li class="page-item {{ $page == $programmes->currentPage() ? 'active' : '' }}">
                            @if($page == $programmes->currentPage())
                                <span class="page-link" style="border:none;background:var(--teal);color:#fff;border-radius:6px;font-weight:600;font-size:.85rem;">{{ $page }}</span>
                            @else
                                <a class="page-link" href="{{ $programmes->url($page) }}&{{ http_build_query(request()->except('page', '_token')) }}" style="border:none;background:var(--slate-50);color:var(--slate-700);border-radius:6px;font-size:.85rem;">{{ $page }}</a>
                            @endif
                        </li>
                    @endfor

                    {{-- Next Page Link --}}
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
    <div style="padding:5rem 2rem;text-align:center;background:var(--slate-50);border-radius:var(--radius-md);border:1px dashed var(--slate-200);animation:fadeIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both;">
        <div style="display:inline-flex;align-items:center;justify-content:center;width:80px;height:80px;border-radius:50%;background:#fff;box-shadow:0 10px 25px rgba(15,23,42,.05);margin-bottom:1.5rem;">
            <i class="bi bi-diagram-3" style="font-size:2rem;color:var(--slate-400);"></i>
        </div>
        <h4 style="font-size:1.15rem;font-weight:700;color:var(--slate-800);margin-bottom:.5rem;">No Programmes Attached</h4>
        <p style="font-size:.9rem;color:var(--slate-500);margin-bottom:1.5rem;max-width:400px;margin-left:auto;margin-right:auto;line-height:1.6;">
            There are currently no programmes attached to this academic session. Use the Add Programmes button to attach them.
        </p>
    </div>
@endif
