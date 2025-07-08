<!-- Assign Instructor Modal -->
<div class="modal fade" id="assignInstructorModal" tabindex="-1" aria-labelledby="assignInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.academic-sessions.programmes.scheduling.add-instructor', [$academicSession, $programme]) }}" method="POST">
                @csrf
                <input type="hidden" name="mapping_id" value="">
                <input type="hidden" name="course_unit_id" id="course_unit_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignInstructorModalLabel">
                        Assign Instructor - <span id="courseCodeDisplay"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select Instructor</label>
                        <select name="user_id" id="user_id" class="form-select select2-instructor" required>
                            <option value="">-- Select Instructor --</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}">
                                    {{ optional($instructor->title)->abbreviation }} {{ $instructor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>
