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
    // Log form submission
    $('#assignSlotModal form').on('submit', function(e) {
        const formData = $(this).serializeArray();
        console.log('Form submitted with data:', formData);
        
        // Verify mapping_id is present
        const mappingId = $(this).find('input[name="mapping_id"]').val();
        console.log('Mapping ID on submit:', mappingId);
        
        if (!mappingId) {
            console.error('Error: No mapping_id found in form submission!');
            e.preventDefault();
            alert('Error: Missing course mapping information. Please try again.');
            return false;
        }
        
        return true;
    });
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
        const button = $(event.relatedTarget);
        const row = button.closest('tr');
        const mappingId = button.data('mapping-id');
        const courseCode = row.find('td:first').text().trim();
        const isEdit = button.data('edit') || false;
        
        console.log('Modal opened with mapping ID:', mappingId, 'for course:', courseCode, 'isEdit:', isEdit);
        
        // Get the form and reset it completely
        const $form = $(this).find('form');
        $form[0].reset();
        
        // Set the mapping ID in the form
        $form.find('input[name="mapping_id"]').val(mappingId);
        console.log('Form mapping_id value set to:', $form.find('input[name="mapping_id"]').val());
        
        // Update title with course code
        const modalTitle = isEdit ? 'Edit Schedule' : 'Schedule Time Slot';
        $(this).find('#courseCodeTitle').text(`${modalTitle} - ${courseCode}`);
        
        // If editing, populate the form with existing data
        if (isEdit) {
            // Get data directly from row attributes
            const dayId = row.attr('data-day-id');
            const morningStart = row.attr('data-morning-start');
            const morningDuration = row.attr('data-morning-duration');
            const eveningStart = row.attr('data-evening-start');
            const eveningDuration = row.attr('data-evening-duration');
            
            console.log('Editing schedule - Raw data:', {
                dayId, 
                morningStart, 
                morningDuration, 
                eveningStart, 
                eveningDuration
            });
            
            // Set day first
            if (dayId && dayId !== '') {
                $form.find('select[name="day_id"]').val(dayId);
                console.log('Set day_id to:', dayId);
            } else {
                console.log('No day_id found or empty');
            }
            
            // Set morning session
            if (morningStart && morningStart !== '') {
                $form.find('#enableMorning').prop('checked', true);
                $form.find('#morning_start').val(morningStart).prop('disabled', false);
                $form.find('#morning_duration').val(morningDuration || '60').prop('disabled', false);
                console.log('Set morning:', morningStart, 'Duration:', morningDuration);
            } else {
                $form.find('#enableMorning').prop('checked', false);
                $form.find('#morning_start, #morning_duration').val('').prop('disabled', true);
                console.log('Morning session disabled');
            }
            
            // Set evening session
            if (eveningStart && eveningStart !== '') {
                $form.find('#enableEvening').prop('checked', true);
                $form.find('#evening_start').val(eveningStart).prop('disabled', false);
                $form.find('#evening_duration').val(eveningDuration || '60').prop('disabled', false);
                console.log('Set evening:', eveningStart, 'Duration:', eveningDuration);
            } else {
                $form.find('#enableEvening').prop('checked', false);
                $form.find('#evening_start, #evening_duration').val('').prop('disabled', true);
                console.log('Evening session disabled');
            }
            
            // Force UI update
            $form.find('#enableMorning, #enableEvening').trigger('change');
        } else {
            // Set default values for new schedule
            $form.find('#enableMorning').prop('checked', true).trigger('change');
            $form.find('#enableEvening').prop('checked', false).trigger('change');
            
            // Set default times
            $form.find('#morning_start').val('08:00');
            $form.find('#morning_duration').val('60');
            $form.find('#evening_start').val('17:00');
            $form.find('#evening_duration').val('60');
        }
        
        // Log final form state for debugging
        console.log('Final form state:', {
            mappingId: $form.find('input[name="mapping_id"]').val(),
            dayId: $form.find('select[name="day_id"]').val(),
            morningEnabled: $form.find('#enableMorning').is(':checked'),
            morningStart: $form.find('#morning_start').val(),
            morningDuration: $form.find('#morning_duration').val(),
            eveningEnabled: $form.find('#enableEvening').is(':checked'),
            eveningStart: $form.find('#evening_start').val(),
            eveningDuration: $form.find('#evening_duration').val()
        });
        
        // Force a small delay to ensure all values are set before showing the modal
        setTimeout(() => {
            console.log('Form state after delay:', {
                dayId: $form.find('select[name="day_id"]').val(),
                morningStart: $form.find('#morning_start').val(),
                eveningStart: $form.find('#evening_start').val()
            });
        }, 100);
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
