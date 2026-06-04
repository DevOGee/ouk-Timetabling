@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Profile</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Profile Information</h3>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Notifications</h3>
            </div>
            <div class="card-body">
                @include('profile.partials.notifications-form')
            </div>
        </div>
    </div>
@endsection
