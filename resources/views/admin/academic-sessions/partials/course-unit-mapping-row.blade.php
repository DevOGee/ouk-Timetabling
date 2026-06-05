@php
    // Make sure $mapping is not null
    $mapping = $mapping ?? null;
    
    // Get related models
    $courseUnit = $mapping->courseUnit ?? null;
    $yearOfStudy = $mapping->yearOfStudy ?? null;
    $semester = $mapping->semester ?? null;
    
    // Get IDs
    $courseUnitId = $courseUnit->id ?? '';
    $yearOfStudyId = $yearOfStudy->id ?? '';
    $semesterId = $semester->id ?? '';
    
    // Get display values
    $courseUnitName = $courseUnit->name ?? '';
    $courseUnitCode = $courseUnit->code ?? '';
    $yearName = $yearOfStudy->name ?? '';
    $semesterName = $semester->name ?? '';
    
    // Generate a unique row ID
    $rowId = 'mapping-' . ($mapping->id ?? 'new-' . uniqid());
    
    // Check if year/semester should be shown (default to true if not set)
    $showYearSemester = $showYearSemester ?? true;
@endphp

<tr id="{{ $rowId }}" class="mapping-row" data-course-id="{{ $courseUnitId }}" style="border-bottom: 1px solid var(--slate-100); background: #fff; transition: all .2s;" onmouseover="this.style.background='var(--slate-50)'" onmouseout="this.style.background='#fff'">
    <td class="course-unit-code fw-bold text-slate-800" style="padding: 1rem 1.5rem; vertical-align: middle;">
        {{ $courseUnitCode }}
    </td>
    <td class="course-unit-name text-slate-600" style="padding: 1rem 1.5rem; vertical-align: middle;">
        {{ $courseUnitName }}
    </td>
    
    <td class="text-center" style="padding: 1rem 1.5rem; vertical-align: middle;">
        <button type="button" class="btn btn-sm btn-outline-danger remove-mapping" data-bs-toggle="tooltip" title="Remove" style="border-radius: 6px;">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
