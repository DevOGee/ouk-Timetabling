@extends('layouts.app')

@section('title', 'User Details: ' . $user->name)

@section('content')
<div class="container-fluid px-4">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">User Details</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    <div class="row">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
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
                    
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to deactivate this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-person-x me-1"></i> {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Details -->
        <div class="col-lg-8">
            <!-- Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Full Name</h6>
                                <p class="mb-3">{{ optional($user->title)->name ?? 'N/A' }} {{ $user->name }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Email</h6>
                                <p class="mb-3">{{ $user->email }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Phone</h6>
                                <p class="mb-3">{{ $user->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Status</h6>
                                <p class="mb-3">
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">School/Department</h6>
                                <p class="mb-3">{{ $user->school->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Member Since</h6>
                                <p class="mb-3">{{ $user->created_at->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Roles & Permissions -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock text-primary me-2"></i>Roles & Permissions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted small mb-2">Assigned Roles</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($user->roles as $role)
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @empty
                                <p class="text-muted mb-0">No roles assigned</p>
                            @endforelse
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="text-muted small mb-2">Roles</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No roles assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Log -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history text-primary me-2"></i>Recent Activity
            </h5>
        </div>
        <div class="card-body">
            @if($user->last_login_at)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-box-arrow-in-right text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Last Login</h6>
                        <p class="text-muted mb-0">
                            {{ $user->last_login_at->diffForHumans() }} 
                            <small class="text-muted">({{ $user->last_login_at->format('F j, Y \a\t g:i A') }})</small>
                        </p>
                    </div>
                </div>
            @endif
            
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="bi bi-person-plus text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Account Created</h6>
                    <p class="text-muted mb-0">
                        {{ $user->created_at->diffForHumans() }}
                        <small class="text-muted">({{ $user->created_at->format('F j, Y') }})</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.45em 0.75em;
    }
    
    .bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }
</style>
@endpush
@endsection
