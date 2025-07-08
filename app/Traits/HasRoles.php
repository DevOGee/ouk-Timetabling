<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Role;

trait HasRoles
{
    /**
     * Get all roles associated with the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Scope the user query to only include users with a given role.
     */
    public function scopeRole(Builder $query, $roles, $guard = null): Builder
    {
        if ($roles === '') {
            return $query;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        
        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        });
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roles, string $guard = null): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if (is_array($roles)) {
            return $this->roles->whereIn('name', $roles)->count() > 0;
        }

        return false;
    }

    /**
     * Alias for hasRole for Spatie compatibility
     */
    public function hasAnyRole($roles, string $guard = null): bool
    {
        return $this->hasRole($roles, $guard);
    }

    /**
     * Check if user has all of the specified roles.
     */
    public function hasAllRoles($roles, string $guard = null): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if (is_array($roles)) {
            return $this->roles->whereIn('name', $roles)->count() === count($roles);
        }

        return false;
    }
}
