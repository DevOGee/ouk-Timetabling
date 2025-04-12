<!-- Assign Instructor Modal -->
<div class="modal fade" id="assignInstructorModal{{ $course->id }}" tabindex="-1"
    aria-labelledby="assignInstructorLabel{{ $course->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('programmes.add_instructor', [$programme, $course]) }}" method="POST"
            class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="assignInstructorLabel{{ $course->id }}">
                    Assign Instructor to {{ $course->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="lecturer_id_{{ $course->id }}" class="form-label">Select Instructor</label>
                <select name="lecturer_id" id="lecturer_id_{{ $course->id }}" class="form-select instructor-select"
                    required>
                    <option value="">-- Select Instructor --</option>
                    @foreach ($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}">
                            {{ $lecturer->title->name ?? '' }} {{ $lecturer->name }}
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

<script>
    // Initialize Select2 for the instructor dropdown
    $(document).ready(function() {
        $('#lecturer_id_{{ $course->id }}').select2({
            width: '100%', // Makes the select box expand to full width
            placeholder: 'Search for an instructor'
        });
    });
</script>
