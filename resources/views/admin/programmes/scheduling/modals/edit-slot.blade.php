<!-- Edit Time Slot Modal -->
<div class="modal fade" id="editSlotModal" tabindex="-1" aria-labelledby="editSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editSlotForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="mapping_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSlotModalLabel">
                        <i class="bi bi-calendar-check me-2"></i>
                        <span id="editCourseCodeTitle">Edit Schedule</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_day_id" class="form-label">Day</label>
                            <select name="day_id" id="edit_day_id" class="form-select" required>
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
                                <input class="form-check-input" type="checkbox" id="edit_enable_morning" name="enable_morning" value="1">
                                <label class="form-check-label" for="edit_enable_morning">Enable Morning Session</label>
                            </div>
                            <div id="edit_morning_fields">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="edit_morning_start" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="edit_morning_start" name="morning_start" value="08:00">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_morning_duration" class="form-label">Duration (minutes)</label>
                                        <input type="number" class="form-control" id="edit_morning_duration" name="morning_duration" min="30" max="240" step="30" value="60">
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
                                <input class="form-check-input" type="checkbox" id="edit_enable_evening" name="enable_evening" value="1">
                                <label class="form-check-label" for="edit_enable_evening">Enable Evening Session</label>
                            </div>
                            <div id="edit_evening_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="edit_evening_start" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="edit_evening_start" name="evening_start" value="17:00">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_evening_duration" class="form-label">Duration (minutes)</label>
                                        <input type="number" class="form-control" id="edit_evening_duration" name="evening_duration" min="30" max="240" step="30" value="60">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger me-auto" id="deleteScheduleBtn">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle morning session fields
    $('#edit_enable_morning').change(function() {
        const isChecked = $(this).is(':checked');
        $('#edit_morning_fields input').prop('disabled', !isChecked);
        if (!isChecked) {
            $('#edit_morning_start, #edit_morning_duration').val('');
        } else {
            $('#edit_morning_start').val('08:00');
            $('#edit_morning_duration').val('60');
        }
    });

    // Toggle evening session fields
    $('#edit_enable_evening').change(function() {
        const isChecked = $(this).is(':checked');
        $('#edit_evening_fields').toggle(isChecked);
        $('#edit_evening_fields input').prop('disabled', !isChecked);
        if (!isChecked) {
            $('#edit_evening_start, #edit_evening_duration').val('');
        } else {
            $('#edit_evening_start').val('17:00');
            $('#edit_evening_duration').val('60');
        }
    });

    // Handle edit modal show event
    const editModal = document.getElementById('editSlotModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const row = button.closest('tr');
            const mappingId = row.attr('data-mapping-id');
            const courseCode = row.find('td:first').text().trim();
            const dayId = row.attr('data-day-id');
            const morningStart = row.attr('data-morning-start');
            const morningDuration = row.attr('data-morning-duration') || '60';
            const eveningStart = row.attr('data-evening-start');
            const eveningDuration = row.attr('data-evening-duration') || '60';
            
            console.log('Edit modal opened with data:', {
                mappingId,
                courseCode,
                dayId,
                morningStart,
                morningDuration,
                eveningStart,
                eveningDuration
            });
            
            const $form = $(this).find('form');
            const actionUrl = `{{ route('admin.academic-sessions.programmes.scheduling.update-slot', [$academicSession, $programme, '']) }}/${mappingId}`;
            
            // Reset form first
            $form[0].reset();
            
            // Update form action and method
            $form.attr('action', actionUrl);
            $form.find('input[name="mapping_id"]').val(mappingId);
            
            // Update title
            $('#editCourseCodeTitle').text(`Edit Schedule - ${courseCode}`);
            
            // Set day first
            if (dayId) {
                $('#edit_day_id').val(dayId);
                console.log('Set day_id to:', dayId);
            }
            
            // Set morning session
            if (morningStart) {
                $('#edit_enable_morning').prop('checked', true);
                $('#edit_morning_start').val(morningStart).prop('disabled', false);
                $('#edit_morning_duration').val(morningDuration).prop('disabled', false);
                console.log('Set morning session:', morningStart, 'Duration:', morningDuration);
            } else {
                $('#edit_enable_morning').prop('checked', false);
                $('#edit_morning_start, #edit_morning_duration').val('').prop('disabled', true);
                console.log('Morning session disabled');
            }
            
            // Set evening session
            if (eveningStart) {
                $('#edit_enable_evening').prop('checked', true);
                $('#edit_evening_start').val(eveningStart).prop('disabled', false);
                $('#edit_evening_duration').val(eveningDuration).prop('disabled', false);
                $('#edit_evening_fields').show();
                console.log('Set evening session:', eveningStart, 'Duration:', eveningDuration);
            } else {
                $('#edit_enable_evening').prop('checked', false);
                $('#edit_evening_start, #edit_evening_duration').val('').prop('disabled', true);
                $('#edit_evening_fields').hide();
                console.log('Evening session disabled');
            }
            
            // Set up delete button
            const deleteUrl = `{{ route('admin.academic-sessions.programmes.scheduling.delete-slot', [$academicSession, $programme, '']) }}/${mappingId}`;
            $('#deleteScheduleBtn').off('click').on('click', function() {
                if (confirm('Are you sure you want to delete this schedule?')) {
                    const deleteForm = document.createElement('form');
                    deleteForm.method = 'POST';
                    deleteForm.action = deleteUrl;
                    deleteForm.style.display = 'none';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    deleteForm.appendChild(csrfToken);
                    deleteForm.appendChild(methodInput);
                    document.body.appendChild(deleteForm);
                    deleteForm.submit();
                }
            });
        });
    }
    
    // Form submission validation
    $('#editSlotForm').on('submit', function(e) {
        const morningEnabled = $('#edit_enable_morning').is(':checked');
        const eveningEnabled = $('#edit_enable_evening').is(':checked');
        
        if (!morningEnabled && !eveningEnabled) {
            e.preventDefault();
            alert('Please enable at least one session (morning or evening)');
            return false;
        }
        
        // Disable disabled fields so they're not submitted
        $('input:disabled').prop('disabled', false);
        
        return true;
    });
});
</script>
@endpush
