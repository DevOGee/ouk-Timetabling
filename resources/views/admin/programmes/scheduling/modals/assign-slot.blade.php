<!-- Assign Time Slot Modal -->
<div class="modal fade" id="assignSlotModal" tabindex="-1" aria-labelledby="assignSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.academic-sessions.programmes.scheduling.assign-slot', [$academicSession, $programme]) }}" method="POST">
                @csrf
                <input type="hidden" name="mapping_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignSlotModalLabel">
                        <i class="bi bi-calendar-plus me-2"></i>
                        <span id="courseCodeTitle">Schedule Time Slot</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="day_id" class="form-label">Day</label>
                            <select name="day_id" id="day_id" class="form-select" required>
                                <option value="">-- Select Day --</option>
                                @foreach(\App\Models\Day::all() as $day)
                                    <option value="{{ $day->id }}">{{ $day->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Morning Session</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="enableMorning" name="enable_morning" value="1" checked>
                                <label class="form-check-label" for="enableMorning">Enable Morning Session</label>
                            </div>
                            <div id="morningFields">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="morning_start" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="morning_start" name="morning_start" value="08:00">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="morning_duration" class="form-label">Duration (minutes)</label>
                                        <input type="number" class="form-control" id="morning_duration" name="morning_duration" min="30" max="240" step="30" value="60">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Evening Session</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="enableEvening" name="enable_evening" value="1">
                                <label class="form-check-label" for="enableEvening">Enable Evening Session</label>
                            </div>
                            <div id="eveningFields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="evening_start" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="evening_start" name="evening_start" value="17:00">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="evening_duration" class="form-label">Duration (minutes)</label>
                                        <input type="number" class="form-control" id="evening_duration" name="evening_duration" min="30" max="240" step="30" value="60">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle morning session fields
    $('#enableMorning').change(function() {
        const isChecked = $(this).is(':checked');
        $('#morningFields input').prop('disabled', !isChecked);
        if (!isChecked) {
            $('#morning_start, #morning_duration').val('');
        } else {
            $('#morning_start').val('08:00');
            $('#morning_duration').val('60');
        }
    });

    // Toggle evening session fields
    $('#enableEvening').change(function() {
        const isChecked = $(this).is(':checked');
        $('#eveningFields').toggle(isChecked);
        $('#eveningFields input').prop('disabled', !isChecked);
        if (!isChecked) {
            $('#evening_start, #evening_duration').val('');
        } else {
            $('#evening_start').val('17:00');
            $('#evening_duration').val('60');
        }
    });

    // Set default values when modal is shown
    $('#assignSlotModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const mappingId = button.getAttribute('data-mapping-id');
        const courseCode = button.getAttribute('data-course-code');
        const modalForm = document.getElementById('assignSlotModal').querySelector('form');
        
        // Update title with course code
        document.getElementById('courseCodeTitle').textContent = `Schedule Time Slot - ${courseCode}`;
        
        // Reset form
        modalForm.reset();
        modalForm.querySelector('input[name="mapping_id"]').value = mappingId;
        
        // Set default values
        $('#enableMorning').prop('checked', true).trigger('change');
        $('#enableEvening').prop('checked', false).trigger('change');
    });

    // Form submission validation
    $('form').on('submit', function(e) {
        const morningEnabled = $('#enableMorning').is(':checked');
        const eveningEnabled = $('#enableEvening').is(':checked');
        
        if (!morningEnabled && !eveningEnabled) {
            e.preventDefault();
            alert('Please enable at least one session (morning or evening)');
            return false;
        }
        
        // Disable disabled fields so they're not submitted
        $('input:disabled').prop('disabled', false);
        
        return true;
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
