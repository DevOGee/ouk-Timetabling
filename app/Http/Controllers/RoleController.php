<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\School;
use App\Models\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with(['roles', 'school', 'title'])
            ->latest()
            ->filter(request(['search', 'status', 'role']))
            ->paginate(15)
            ->withQueryString();
            
        $roles = Role::all();
        $schools = School::all();
        
        return view('admin.users.index', compact('users', 'roles', 'schools'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $titles = Title::orderBy('name')->get();
        $schools = School::orderBy('name')->get();
        $roles = Role::all();
        
        return view('admin.users.create', compact('titles', 'schools', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'title_id' => ['required', 'exists:titles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile-images', 'public');
        }

        $user = User::create([
            'title_id' => $validated['title_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'school_id' => $validated['school_id'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
            'image_path' => $imagePath,
        ]);

        // Assign roles
        $user->roles()->sync($validated['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $titles = Title::orderBy('name')->get();
        $schools = School::orderBy('name')->get();
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('admin.users.edit', compact('user', 'titles', 'schools', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'title_id' => ['required', 'exists:titles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $updateData = [
            'title_id' => $validated['title_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'school_id' => $validated['school_id'],
            'status' => $validated['status'],
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image_path) {
                Storage::disk('public')->delete($user->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('profile-images', 'public');
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        
        // Sync roles
        $user->roles()->sync($validated['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        $user->delete();
        
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deactivated successfully');
    }
    
    /**
     * Show the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load(['roles', 'school', 'title']);
        
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);
        
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);
        
        return back()->with('success', 'User status updated successfully');
    }
}
