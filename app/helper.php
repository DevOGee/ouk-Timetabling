<?php

use Illuminate\Support\Facades\Auth;

/**
 * Check if the authenticated user is an admin.
 */
function isAdmin(): bool
{
    return Auth::check() && optional(Auth::user()->role)->role === 'admin';
}

/**
 * Check if the authenticated user is a doctor.
 */
function isDoctor(): bool
{
    return Auth::check() && optional(Auth::user()->role)->role === 'doctor';
}

/**
 * Check if the authenticated user has a specific role.
 */
function hasRole(string $role): bool
{
    return Auth::check() && optional(Auth::user()->role)->role === $role;
}

/**
 * Get the current authenticated user.
 *
 * @return \App\Models\User|null
 */
function currentUser()
{
    return Auth::user();
}
