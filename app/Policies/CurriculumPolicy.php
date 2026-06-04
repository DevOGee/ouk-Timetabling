<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Curriculum;
use App\Models\AcademicSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurriculumPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, AcademicSession $academicSession = null): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Curriculum $curriculum): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, AcademicSession $academicSession = null): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Curriculum $curriculum): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Curriculum $curriculum): bool
    {
        // Only allow deletion if the curriculum is not the only active one in its session
        if ($curriculum->is_active) {
            $activeCount = $curriculum->academicSession->curricula()
                ->where('is_active', true)
                ->count();
                
            if ($activeCount <= 1) {
                return false;
            }
        }
        
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Curriculum $curriculum): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Curriculum $curriculum): bool
    {
        return $user->hasRole('admin');
    }
    
    /**
     * Determine whether the user can roll forward the curriculum to a new academic session.
     */
    public function rollForward(User $user, Curriculum $curriculum, AcademicSession $targetSession = null): bool
    {
        // Only admins can roll forward curricula
        if (!$user->hasRole('admin')) {
            return false;
        }
        
        // Can't roll forward to the same academic session
        if ($targetSession && $targetSession->id === $curriculum->academic_session_id) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Determine whether the user can set a curriculum as active.
     */
    public function setActive(User $user, Curriculum $curriculum): bool
    {
        return $user->hasRole('admin');
    }
}
