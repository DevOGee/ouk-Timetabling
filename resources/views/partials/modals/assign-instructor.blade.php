<!-- Assign Instructor Modal -->
<div class="modal fade" id="assignInstructorModal{{ $course->id }}" tabindex="-1"
    aria-labelledby="assignInstructorLabel{{ $course->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.programmes.add_instructor', [$programme, $course]) }}" method="POST"
            class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="assignInstructorLabel{{ $course->id }}">
                    Assign Instructor to {{ $course->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="user_id_{{ $course->id }}" class="form-label">Select Instructor</label>
                <select name="user_id" id="user_id_{{ $course->id }}" class="form-control select2"
                    style="width: 100%" required>
                    <option value="">-- Select Instructor --</option>
                    @foreach ($instructors->sortBy('name') as $instructor)
                        <option value="{{ $instructor->id }}">
                            {{ $instructor->title ? $instructor->title->abbreviation . ' ' : '' }}{{ $instructor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Assign</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

