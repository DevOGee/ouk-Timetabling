<!-- Change Instructor Modal -->
<div class="modal fade" id="changeInstructorModal" tabindex="-1" aria-labelledby="changeInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.academic-sessions.programmes.scheduling.add-instructor', [$academicSession, $programme]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="mapping_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeInstructorModalLabel">Change Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Current Instructor: <strong class="current-instructor"></strong>
                    </div>
                    <div class="mb-3">
                        <label for="new_user_id" class="form-label">Select New Instructor</label>
                        <select name="user_id" id="new_user_id" class="form-select select2-instructor" required>
                            <option value="">-- Select New Instructor --</option>
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
                    <button type="submit" class="btn btn-primary">Update Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>
