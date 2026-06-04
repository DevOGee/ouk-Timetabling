@extends('layouts.app')

@section('title', 'Email Setup - OUK Timetabling')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">

            {{-- Page header --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(3,123,144,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#037b90" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="h4 mb-0 fw-bold">Email / SMTP Setup</h1>
                    <p class="text-muted small mb-0">Configure Google App Passwords or any SMTP provider for sending emails</p>
                </div>
            </div>

            {{-- Alerts --}}
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('test_success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('test_success') }}
                </div>
            @endif

            @if (session('test_error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('test_error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Google App Password Guide --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;border-left:4px solid #ff7f50 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#ff7f50" stroke-width="2" style="flex-shrink:0;margin-top:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div>
                            <h6 class="fw-semibold mb-2" style="color:#ff7f50;">Using Gmail? You need a Google App Password</h6>
                            <ol class="text-muted small mb-2" style="padding-left:1.2rem;">
                                <li>Go to your Google Account → <strong>Security</strong></li>
                                <li>Under "How you sign in to Google", enable <strong>2-Step Verification</strong></li>
                                <li>Search for <strong>App passwords</strong> in the search bar</li>
                                <li>Create a new App Password for <strong>Mail</strong> and copy it</li>
                                <li>Paste the 16-character code in the <em>App Password</em> field below</li>
                            </ol>
                            <p class="text-muted small mb-0">
                                Set <strong>SMTP Host</strong> to <code>smtp.gmail.com</code>, <strong>Port</strong> to <code>587</code>, and <strong>Encryption</strong> to <code>TLS</code>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Settings Form --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <h5 class="fw-semibold mb-0">SMTP Configuration</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.settings.email.update') }}" id="smtp-form">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">SMTP Host</label>
                                <input type="text" name="MAIL_HOST" class="form-control @error('MAIL_HOST') is-invalid @enderror"
                                       value="{{ old('MAIL_HOST', $settings['MAIL_HOST']) }}"
                                       placeholder="smtp.gmail.com" required>
                                @error('MAIL_HOST')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Port</label>
                                <select name="MAIL_PORT" class="form-select @error('MAIL_PORT') is-invalid @enderror">
                                    @foreach ([587 => 'TLS — 587', 465 => 'SSL — 465', 25 => 'SMTP — 25', 2525 => 'Alt — 2525'] as $port => $label)
                                        <option value="{{ $port }}" {{ (int) old('MAIL_PORT', $settings['MAIL_PORT']) === $port ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('MAIL_PORT')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Username / Gmail Address</label>
                                <input type="email" name="MAIL_USERNAME" class="form-control @error('MAIL_USERNAME') is-invalid @enderror"
                                       value="{{ old('MAIL_USERNAME', $settings['MAIL_USERNAME']) }}"
                                       placeholder="your-email@gmail.com" required>
                                @error('MAIL_USERNAME')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">App Password</label>
                                <div class="input-group">
                                    <input type="password" name="MAIL_PASSWORD" id="mail-password"
                                           class="form-control @error('MAIL_PASSWORD') is-invalid @enderror"
                                           value="{{ old('MAIL_PASSWORD', $settings['MAIL_PASSWORD']) }}"
                                           placeholder="16-character app password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleMailPwd()">
                                        <svg id="mail-eye" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                @error('MAIL_PASSWORD')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Encryption</label>
                                <select name="MAIL_ENCRYPTION" class="form-select @error('MAIL_ENCRYPTION') is-invalid @enderror">
                                    <option value="tls" {{ old('MAIL_ENCRYPTION', $settings['MAIL_ENCRYPTION']) === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('MAIL_ENCRYPTION', $settings['MAIL_ENCRYPTION']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ old('MAIL_ENCRYPTION', $settings['MAIL_ENCRYPTION']) === 'none' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('MAIL_ENCRYPTION')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">From Address</label>
                                <input type="email" name="MAIL_FROM_ADDRESS" class="form-control @error('MAIL_FROM_ADDRESS') is-invalid @enderror"
                                       value="{{ old('MAIL_FROM_ADDRESS', $settings['MAIL_FROM_ADDRESS']) }}"
                                       placeholder="noreply@ouk.ac.ke" required>
                                @error('MAIL_FROM_ADDRESS')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">From Name</label>
                                <input type="text" name="MAIL_FROM_NAME" class="form-control @error('MAIL_FROM_NAME') is-invalid @enderror"
                                       value="{{ old('MAIL_FROM_NAME', $settings['MAIL_FROM_NAME']) }}"
                                       placeholder="OUK Timetabling" required>
                                @error('MAIL_FROM_NAME')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4"
                                    style="background:linear-gradient(135deg,#037b90,#025f70);border:none;border-radius:10px;">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="me-2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Test Email --}}
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <h5 class="fw-semibold mb-0">Send Test Email</h5>
                    <p class="text-muted small mb-0">Verify that your SMTP configuration is working correctly</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.settings.email.test') }}" id="test-form">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Send test email to</label>
                                <input type="email" name="test_email" class="form-control"
                                       placeholder="recipient@example.com" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn w-100"
                                        style="background:linear-gradient(135deg,#ff7f50,#e86c3a);color:#fff;border:none;border-radius:10px;">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="me-2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Send Test
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #037b90;
        box-shadow: 0 0 0 0.2rem rgba(3,123,144,0.15);
    }

    .card { transition: box-shadow 0.2s; }
</style>

<script>
    function toggleMailPwd() {
        const input = document.getElementById('mail-password');
        const icon = document.getElementById('mail-eye');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }
</script>
@endsection
