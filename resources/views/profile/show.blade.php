@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">My Profile</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Edit Profile
            </a>
        </div>
    </div>

    <div class="row g-4 mx-0">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge me-2"></i>Profile
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        @if ($user->image_path)
                            <img src="{{ asset('storage/' . $user->image_path) }}" 
                                 class="rounded-circle border border-4 border-primary" 
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 alt="{{ $user->name }}">
                        @else
                            <img src="https://ouk.ac.ke/sites/default/files/Facilitators/alt.png"
                                 class="rounded-circle border border-4 border-primary"
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 alt="Default Image">
                        @endif
                        <span class="position-absolute bottom-0 end-0 bg-{{ $user->status === 'active' ? 'success' : 'secondary' }} rounded-circle p-2" 
                              style="width: 24px; height: 24px; border: 3px solid #fff;"
                              title="{{ ucfirst($user->status) }}">
                        </span>
                    </div>
                    
                    <h4 class="mb-1">{{ optional($user->title)->abbreviation ?? '' }} {{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    @if($user->phone)
                        <p class="mb-2">
                            <i class="bi bi-telephone me-2 text-primary"></i> {{ $user->phone }}
                        </p>
                    @endif
                    
                    <div class="mt-4">
                        <div class="d-flex flex-column gap-2">
                            <a href="mailto:{{ $user->email }}" class="btn btn-outline-secondary">
                                <i class="bi bi-envelope me-2"></i> Send Email
                            </a>
                            @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="btn btn-outline-secondary">
                                <i class="bi bi-telephone me-2"></i> Call
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Details -->
        <div class="col-lg-8">
            <!-- Details Card -->
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted small mb-2">Full Name</h6>
                                <p class="mb-0">{{ optional($user->title)->name ?? '' }} {{ $user->name }}</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-muted small mb-2">Email</h6>
                                <p class="mb-0">
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-muted small mb-2">Phone</h6>
                                <p class="mb-0">
                                    @if($user->phone)
                                        <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                            {{ $user->phone }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted small mb-2">Status</h6>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-muted small mb-2">School/Department</h6>
                                <p class="mb-0">{{ $user->school->name ?? 'Not assigned' }}</p>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-muted small mb-2">Role</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            <i class="bi bi-person-badge me-1"></i>
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activity Card -->
            {{-- <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-activity text-primary me-2"></i>Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="bi bi-box-arrow-in-right text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted small mb-1">Last Login</h6>
                                    <p class="mb-0">
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->diffForHumans() }}
                                            <small class="text-muted d-block">{{ $user->last_login_at->format('M d, Y \a\t h:i A') }}</small>
                                        @else
                                            <span class="text-muted">Never logged in</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted small mb-1">Member Since</h6>
                                    <p class="mb-0">
                                        {{ $user->created_at->format('M d, Y') }}
                                        <small class="text-muted d-block">({{ $user->created_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.25rem;
        border-radius: 10px 10px 0 0 !important;
    }
    
    .card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .small {
        font-size: 0.875em;
    }
    
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    
    .bg-success {
        background-color: #198754 !important;
    }
    
    .bg-secondary {
        background-color: #6c757d !important;
    }
    
    .bg-primary {
        background-color: #0d6efd !important;
    }
    
    .border-primary {
        border-color: #0d6efd !important;
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    
    .btn i {
        margin-right: 0.5rem;
    }
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: none;
    }
    
    .text-muted {
        font-size: 0.85rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
    
    .img-thumbnail {
        border: 3px solid #dee2e6;
        padding: 0.25rem;
    }
</style>
@endpush
