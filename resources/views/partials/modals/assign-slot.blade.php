<!-- Assign Slot Modal -->
<div class="modal fade" id="assignSlotModal{{ $mapping->id }}" tabindex="-1"
    aria-labelledby="assignSlotLabel{{ $mapping->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.lesson_slots.store', [$programme->id, $course->id]) }}" method="POST"
            class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="assignSlotLabel{{ $mapping->id }}">
                    Assign Slot for {{ $course->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Day</label>
                <select name="day_id" class="form-select" required>
                    @foreach ($days as $day)
                        <option value="{{ $day->id }}">{{ $day->name }}</option>
                    @endforeach
                </select>

                <hr>
                <h6 class="fw-bold">Morning Slot</h6>
                <label class="form-label">Start Time</label>
                <input type="time" name="morning_start_time" class="form-control">

                <label class="mt-2 form-label">Duration (min)</label>
                <input type="number" name="morning_duration" class="form-control" min="1">

                <hr>
                <h6 class="fw-bold">Evening Slot</h6>
                <label class="form-label">Start Time</label>
                <input type="time" name="evening_start_time" class="form-control">

                <label class="mt-2 form-label">Duration (min)</label>
                <input type="number" name="evening_duration" class="form-control" min="1">
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Assign Slot</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
