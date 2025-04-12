<!-- Edit Slot Modal -->
<div class="modal fade" id="editSlotModal{{ $mapping->id }}" tabindex="-1"
    aria-labelledby="editSlotLabel{{ $mapping->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('lesson_slots.update', [$programme->id, $course->id, $mapping->id]) }}" method="POST"
            class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editSlotLabel{{ $mapping->id }}">
                    Edit Slot for {{ $course->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Day</label>
                <select name="day_id" class="form-select" required>
                    @foreach ($days as $day)
                        <option value="{{ $day->id }}" {{ $mapping->day_id == $day->id ? 'selected' : '' }}>
                            {{ $day->name }}
                        </option>
                    @endforeach
                </select>

                <hr>
                <h6 class="fw-bold">Morning Slot</h6>
                <label class="form-label">Start Time</label>
                <input type="time" name="morning_start_time" class="form-control"
                    value="{{ $mapping->morning_start_time }}">

                <label class="mt-2 form-label">Duration (min)</label>
                <input type="number" name="morning_duration" class="form-control"
                    value="{{ $mapping->morning_duration }}" min="1">

                <hr>
                <h6 class="fw-bold">Evening Slot</h6>
                <label class="form-label">Start Time</label>
                <input type="time" name="evening_start_time" class="form-control"
                    value="{{ $mapping->evening_start_time }}">

                <label class="mt-2 form-label">Duration (min)</label>
                <input type="number" name="evening_duration" class="form-control"
                    value="{{ $mapping->evening_duration }}" min="1">
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Update Slot</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
