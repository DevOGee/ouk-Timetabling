@php
    $mapping = $mapping ?? null;
    $index = $index ?? 0;
    $isNew = $mapping === null;
    $showYearSemester = $showYearSemester ?? true;
    
    $courseUnit = $mapping ? $mapping->courseUnit : null;
    $yearOfStudy = $mapping ? $mapping->yearOfStudy : null;
    $semester = $mapping ? $mapping->semester : null;
    
    $courseUnitId = $courseUnit->id ?? '';
    $yearOfStudyId = $yearOfStudy->id ?? '';
    $semesterId = $semester->id ?? '';
    
    $courseUnitName = $courseUnit ? $courseUnit->name : '';
    $courseUnitCode = $courseUnit->code ?? '';
    $yearName = $yearOfStudy->name ?? '';
    $semesterName = $semester->name ?? '';
    
    // Generate a unique identifier for this row
    $rowId = 'mapping-' . ($mapping->id ?? 'new-' . uniqid());
@endphp

<tr id="mapping-{{ $index }}" class="mapping-row" data-course-id="{{ $courseUnitId }}">
    @if($showYearSemester)
        <td class="course-year">{{ $yearName }}</td>
        <td class="course-semester">{{ $semesterName }}</td>
    @endif
    
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
        <input type="hidden" name="mappings[{{ $index }}][course_unit_id]" class="course-unit-id" value="{{ $courseUnitId }}">
        <input type="hidden" name="mappings[{{ $index }}][year_of_study_id]" class="year-id" value="{{ $yearOfStudyId }}">
        <input type="hidden" name="mappings[{{ $index }}][semester_id]" class="semester-id" value="{{ $semesterId }}">
        @if(!$isNew && isset($mapping->id))
            <input type="hidden" name="mappings[{{ $index }}][id]" value="{{ $mapping->id }}">
        @endif
    </td>
</tr>
