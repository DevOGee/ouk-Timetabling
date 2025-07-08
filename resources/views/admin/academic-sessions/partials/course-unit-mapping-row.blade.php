@php
    $mapping = $mapping ?? null;
    $index = $index ?? 0;
    $isNew = $mapping === null;
    
    $courseUnit = $mapping ? $mapping->courseUnit : null;
    $yearOfStudy = $mapping ? $mapping->yearOfStudy : null;
    $semester = $mapping ? $mapping->semester : null;
    
    $courseUnitId = $courseUnit->id ?? '';
    $yearOfStudyId = $yearOfStudy->id ?? '';
    $semesterId = $semester->id ?? '';
    
    $courseUnitName = $courseUnit ? "{$courseUnit->code} - {$courseUnit->name}" : '';
    $yearName = $yearOfStudy->name ?? '';
    $semesterName = $semester->name ?? '';
@endphp

<tr data-course-id="{{ $courseUnitId }}">
    <td class="course-unit-name">{{ $courseUnitName }}</td>
    <td class="course-unit-code">{{ $courseUnit->code ?? '' }}</td>
    <td class="course-year">{{ $yearName }}</td>
    <td class="course-semester">{{ $semesterName }}</td>
    <td>
        <input type="hidden" name="mappings[{{ $index }}][course_unit_id]" class="course-unit-id" value="{{ $courseUnitId }}">
        <input type="hidden" name="mappings[{{ $index }}][year_of_study_id]" class="year-id" value="{{ $yearOfStudyId }}">
        <input type="hidden" name="mappings[{{ $index }}][semester_id]" class="semester-id" value="{{ $semesterId }}">
        @if(!$isNew && isset($mapping->id))
            <input type="hidden" name="mappings[{{ $index }}][id]" value="{{ $mapping->id }}">
        @endif
        <button type="button" class="btn btn-sm btn-outline-danger remove-mapping">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
