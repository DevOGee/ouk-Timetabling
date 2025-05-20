<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('roles', 'school')->get();
        $roles = Role::all();
        $schools = School::all();
        return view('roles.index', compact('users', 'roles', 'schools'));
    }

    /**
     * Show the form for editing the specified user's roles.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::all();
        $schools = School::all();
        $userRoles = $user->roles()->pluck('id')->toArray();
        return view('roles.edit', compact('user', 'roles', 'schools', 'userRoles'));
    }

    /**
     * Update the specified user's roles.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        // Get all roles that require a school assignment
        $schoolRequiredRoles = ['dean', 'school_timetabler', 'instructor'];
        
        // If user has any school-required roles, they must be assigned to a school
        if (array_intersect($validated['roles'], $schoolRequiredRoles) && !$validated['school_id']) {
            return back()->withErrors(['school_id' => 'A school must be selected for this role']);
        }

        // Update user's school assignment
        if ($validated['school_id']) {
            $user->school_id = $validated['school_id'];
        } else {
            $user->school_id = null;
        }
        $user->save();

        // Update user's roles
        $user->roles()->sync(Role::whereIn('name', $validated['roles'])->pluck('id'));

        return redirect()->route('roles.index')->with('success', 'User roles and school assignment updated successfully');
    }

    /**
     * Assign a role to a user.
     */
    public function assign(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->roles()->attach($validated['role_id']);
        return redirect()->route('roles.index')->with('success', 'Role assigned successfully');
    }

    /**
     * Remove a role from a user.
     */
    public function remove(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->roles()->detach($validated['role_id']);
        return redirect()->route('roles.index')->with('success', 'Role removed successfully');
    }
}
