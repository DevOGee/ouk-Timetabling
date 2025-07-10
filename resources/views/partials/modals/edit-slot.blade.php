<!-- Edit Slot Modal -->
<div class="modal fade" id="editSlotModal{{ $mapping->id }}" tabindex="-1"
    aria-labelledby="editSlotLabel{{ $mapping->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.lesson_slots.update', [
                'programme' => $programme->id, 
                'courseUnit' => $course->id, 
                'lessonSlot' => $mapping->id,
                'academicSession' => $academicSession->id
            ]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="academic_session_id" value="{{ $academicSession->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editSlotLabel{{ $mapping->id }}">
                        <i class="bi bi-calendar2-event"></i> Edit Slot for {{ $course->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Day</label>
                        <select name="day_id" class="form-select" required>
                            @foreach ($days as $day)
                                <option value="{{ $day->id }}" {{ $mapping->day_id == $day->id ? 'selected' : '' }}>
                                    {{ $day->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold"><i class="bi bi-sun"></i> Morning Slot</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="morning_start_time" class="form-control" 
                                    value="{{ $mapping->morning_start_time ? \Carbon\Carbon::parse($mapping->morning_start_time)->format('H:i') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" name="morning_duration" class="form-control" 
                                    value="{{ $mapping->morning_duration }}" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold"><i class="bi bi-moon"></i> Evening Slot</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="evening_start_time" class="form-control"
                                    value="{{ $mapping->evening_start_time ? \Carbon\Carbon::parse($mapping->evening_start_time)->format('H:i') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" name="evening_duration" class="form-control"
                                    value="{{ $mapping->evening_duration }}" min="1">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Update Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
