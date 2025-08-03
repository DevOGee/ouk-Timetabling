@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="profile-page-container">
    {{-- Header with page title and edit button --}}
    <div class="profile-page-header">
        <div>
            <h2 class="page-title">My Profile</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Profile
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card profile-summary-card">
                <div class="card-body">
                    <div class="avatar-wrapper" style="width: 150px; height: 150px; border-radius: 50%; overflow: hidden; position: relative; border: 3px solid var(--primary-color);">
                        @if ($user->image_path)
                            <img 
                                src="{{ asset('storage/' . $user->image_path) }}" 
                                alt="{{ $user->name }}"
                                style="
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                    object-position: center;
                                "
                                onerror="this.onerror=null; this.src='https://ouk.ac.ke/sites/default/files/Facilitators/alt.png'"
                            >
                        @else
                            <img 
                                src="https://ouk.ac.ke/sites/default/files/Facilitators/alt.png" 
                                alt="Default Image"
                                style="
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                    object-position: center;
                                "
                            >
                        @endif
                        <span class="status-indicator bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}" 
                              style="
                                  position: absolute;
                                  bottom: 10px;
                                  right: 10px;
                                  width: 20px;
                                  height: 20px;
                                  border: 2px solid #fff;
                                  border-radius: 50%;
                              "
                              title="{{ ucfirst($user->status) }}">
                        </span>
                    </div>
                    
                    <h4 class="user-name">{{ optional($user->title)->abbreviation ?? '' }} {{ $user->name }}</h4>
                    <p class="user-email">{{ $user->email }}</p>
                    
                    @if($user->phone)
                        <p class="user-phone">
                            <i class="bi bi-telephone"></i> {{ $user->phone }}
                        </p>
                    @endif
                    
                    <div class="action-buttons">
                        <a href="mailto:{{ $user->email }}" class="btn btn-outline-secondary">
                            <i class="bi bi-envelope"></i> Send Email
                        </a>
                        @if($user->phone)
                        <a href="tel:{{ $user->phone }}" class="btn btn-outline-secondary">
                            <i class="bi bi-telephone"></i> Call
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card details-card">
                 <div class="card-header">
                    <h5 class="card-title-icon"><i class="bi bi-info-circle"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="details-grid">
                        <div class="detail-item">
                            <label>Full Name</label>
                            <p>{{ optional($user->title)->name ?? '' }} {{ $user->name }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Email Address</label>
                            <p><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
                        </div>
                        <div class="detail-item">
                            <label>Phone Number</label>
                            <p>
                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <p>
                                <span class="badge status-badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="detail-item">
                            <label>School / Department</label>
                            <p>{{ $user->school->name ?? 'Not assigned' }}</p>
                        </div>
                         <div class="detail-item">
                            <label>Role(s)</label>
                            <div class="roles-container">
                                @foreach($user->roles as $role)
                                    <span class="badge role-badge bg-primary">
                                        <i class="bi bi-person-badge"></i>
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card activity-card">
                <div class="card-header">
                    <h5 class="card-title-icon"><i class="bi bi-activity"></i> Activity</h5>
                </div>
                <div class="card-body">
                    <div class="activity-grid">
                        <div class="activity-item">
                            <div class="activity-icon-wrapper">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </div>
                            <div>
                                <label>Last Login</label>
                                <p>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->diffForHumans() }}
                                        <small class="d-block text-muted">{{ $user->last_login_at->format('M d, Y, h:i A') }}</small>
                                    @else
                                        <span>Never logged in</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="activity-item">
                           <div class="activity-icon-wrapper">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <label>Member Since</label>
                                 <p>
                                    {{ $user->created_at->format('M d, Y') }}
                                    <small class="d-block text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* --- CSS Variables for Easy Theming --- */
    :root {
        --primary-color: #0d6efd;
        --success-color: #198754;
        --secondary-color: #6c757d;
        --light-gray-color: #f8f9fa;
        --border-color: #dee2e6;
        --text-dark: #212529;
        --text-muted: #6c757d;
        --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --card-shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        --card-border-radius: 12px;
    }

    /* --- General Layout & Page Header --- */
    .profile-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .page-title {
        margin-bottom: 0.25rem;
        color: var(--text-dark);
    }
    .breadcrumb {
        margin-bottom: 0;
        font-size: 0.9em;
    }
    .breadcrumb-item a {
        text-decoration: none;
        color: var(--primary-color);
    }
    .btn i {
        margin-right: 0.5rem;
    }

    /* --- General Card Styles --- */
    .card {
        border: 1px solid var(--border-color);
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        height: 100%;
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-shadow-hover);
    }
    .card-header {
        background-color: var(--light-gray-color);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        border-radius: var(--card-border-radius) var(--card-border-radius) 0 0;
    }
    .card-title-icon {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .card-title-icon i {
        color: var(--primary-color);
    }
    .card-body {
        padding: 1.5rem;
    }

    /* --- Profile Summary Card (Left) --- */
    .profile-summary-card .card-body {
        text-align: center;
    }
    .avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 1rem;
    }
    .avatar {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid var(--primary-color);
        padding: 4px;
        background-color: white;
    }
    .status-indicator {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 24px;
        height: 24px;
        border: 3px solid #fff;
        border-radius: 50%;
    }
    .user-name {
        margin-bottom: 0.25rem;
        font-size: 1.5rem;
    }
    .user-email {
        color: var(--text-muted);
        margin-bottom: 1rem;
        word-break: break-all;
    }
    .user-phone {
        margin-bottom: 1.5rem;
        color: var(--text-dark);
    }
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    /* --- Details & Activity Cards (Right) --- */
    .details-grid, .activity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .detail-item label, .activity-item label {
        font-size: 0.85em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        display: block;
    }
    .detail-item p, .activity-item p {
        font-weight: 500;
        margin: 0;
        color: var(--text-dark);
    }
    .detail-item a {
        text-decoration: none;
        color: var(--primary-color);
    }
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
    .status-badge {
        border-radius: 50rem;
        font-size: 0.8em;
    }
    .roles-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .activity-icon-wrapper {
        background-color: var(--light-gray-color);
        color: var(--primary-color);
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
@endpush