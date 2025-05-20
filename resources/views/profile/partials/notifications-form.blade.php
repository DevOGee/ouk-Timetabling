<form method="POST" action="{{ route('profile.notifications.update') }}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications"
                {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }}>
            <label class="form-check-label" for="email_notifications">
                <i class="bi bi-envelope me-2"></i>Receive email notifications
            </label>
        </div>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications"
                {{ old('sms_notifications', $user->sms_notifications) ? 'checked' : '' }}>
            <label class="form-check-label" for="sms_notifications">
                <i class="bi bi-chat-dots me-2"></i>Receive SMS notifications
            </label>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i>Save
        </button>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success mt-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>Profile updated successfully!
        </div>
    @endif
</form>
