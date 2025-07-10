<!-- Assign Instructor Modal -->
<div class="modal fade" id="assignInstructorModal{{ $mapping->id }}" tabindex="-1"
    aria-labelledby="assignInstructorLabel{{ $mapping->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.academic-sessions.programmes.scheduling.add-instructor', [
                'academicSession' => $academicSession->id,
                'programme' => $programme->id
            ]) }}" method="POST">
                @csrf
                <input type="hidden" name="mapping_id" value="{{ $mapping->id }}">
                <input type="hidden" name="course_unit_id" value="{{ $course->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="assignInstructorLabel{{ $mapping->id }}">
                        Assign Instructor to {{ $course->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id_{{ $mapping->id }}" class="form-label">Select Instructor</label>
                        <select name="user_id" id="user_id_{{ $mapping->id }}" class="form-control select2" style="width: 100%" required>
                            <option value="">-- Select Instructor --</option>
                            @foreach ($instructors->sortBy('name') as $instructor)
                                <option value="{{ $instructor->id }}">
                                    {{ $instructor->title ? $instructor->title->abbreviation . ' ' : '' }}{{ $instructor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

