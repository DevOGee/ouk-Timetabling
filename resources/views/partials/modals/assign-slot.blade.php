<!-- Assign Slot Modal -->
<div class="modal fade" id="assignSlotModal{{ $mapping->id }}" tabindex="-1"
    aria-labelledby="assignSlotLabel{{ $mapping->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.academic-sessions.programmes.scheduling.assign-slot', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" method="POST">
    <input type="hidden" name="mapping_id" value="{{ $mapping->id }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignSlotLabel{{ $mapping->id }}">
                        Assign Slot for {{ $course->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Day</label>
                        <select name="day_id" class="form-select" required>
                            @foreach ($days as $day)
                                <option value="{{ $day->id }}">{{ $day->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold"><i class="bi bi-sun"></i> Morning Slot</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="morning_start" class="form-control @error('morning_start') is-invalid @enderror">
                                @error('morning_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" name="morning_duration" class="form-control @error('morning_duration') is-invalid @enderror" min="1">
                                @error('morning_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold"><i class="bi bi-moon"></i> Evening Slot</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="evening_start" class="form-control @error('evening_start') is-invalid @enderror">
                                @error('evening_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" name="evening_duration" class="form-control @error('evening_duration') is-invalid @enderror" min="1">
                                @error('evening_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-plus"></i> Assign Slot
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
