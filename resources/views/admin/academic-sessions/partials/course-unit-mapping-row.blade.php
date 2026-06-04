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

<tr id="{{ $rowId }}" class="mapping-row" data-course-id="{{ $courseUnitId }}">
    <td class="course-unit-code fw-bold">
        {{ $courseUnitCode }}
    </td>
    <td class="course-unit-name">
        {{ $courseUnitName }}
    </td>
    
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-mapping" data-bs-toggle="tooltip" title="Remove">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
