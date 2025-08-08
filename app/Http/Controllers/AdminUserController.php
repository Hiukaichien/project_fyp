<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users (admin only)
     */
    public function index(): View
    {
        // Check if current user is superadmin
        if (Auth::user()->superadmin !== 'yes') {
            abort(403, 'Access denied. Only admin can manage users.');
        }

        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user (admin only)
     */
    public function create(): View
    {
        // Check if current user is superadmin
        if (Auth::user()->superadmin !== 'yes') {
            abort(403, 'Access denied. Only admin can create users.');
        }

        // Get all projects for project visibility selection
        $projects = Project::with('user')->orderBy('name')->get();

        return view('admin.users.create', compact('projects'));
    }

    /**
     * Store a newly created user (admin only)
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if current user is superadmin
        if (Auth::user()->superadmin !== 'yes') {
            abort(403, 'Access denied. Only admin can create users.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Superadmin field commented out - only creating regular users for now
            // 'superadmin' => ['required', 'in:yes,no'],
            'project_visibility' => ['required', 'in:all,selected'],
            'visible_projects' => ['nullable', 'array'],
            'visible_projects.*' => ['exists:projects,id'],
        ]);

        // Determine project visibility based on form input
        if ($request->project_visibility === 'all') {
            $visibleProjects = null; // null means all projects
        } else {
            $visibleProjects = $request->visible_projects ?? []; // selected projects or empty array
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'superadmin' => 'no', // Always create regular users for now
            'can_be_deleted' => true, // Regular users can be deleted
            'visible_projects' => $visibleProjects,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berjaya dicipta.');
    }

    /**
     * Show the form for editing a user (admin only)
     */
    public function edit(User $user): View
    {
        // Check if current user is superadmin
        if (Auth::user()->superadmin !== 'yes') {
            abort(403, 'Access denied. Only admin can edit users.');
        }

        // Get all projects for the project selection
        $projects = Project::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'projects'));
    }

    /**
     * Update the specified user (admin only)
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Check if current user is superadmin
        if (Auth::user()->superadmin !== 'yes') {
            abort(403, 'Access denied. Only admin can update users.');
        }

        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            // Superadmin field is optional since it's commented out in the form
            'superadmin' => ['nullable', 'in:yes,no'],
            'project_visibility' => ['required_unless:user,' . Auth::id(), 'in:all,selected'],
            'visible_projects' => ['nullable', 'array'],
            'visible_projects.*' => ['exists:projects,id'],
        ];

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Handle project visibility (only for other users, not self)
        if ($user->id !== Auth::id()) {
            if ($request->project_visibility === 'all') {
                $updateData['visible_projects'] = null; // null means all projects
            } else {
                // Get the selected projects from the form (default to empty array if none selected)
                $selectedProjects = $request->visible_projects ?? [];
                
                // Always include projects owned by this user (they cannot be unticked)
                $ownedProjectIds = $user->projects()->pluck('id')->toArray();
                
                // Merge selected projects with owned projects and remove duplicates
                $finalVisibleProjects = array_unique(array_merge($selectedProjects, $ownedProjectIds));
                
                $updateData['visible_projects'] = $finalVisibleProjects;
            }
        }

        // Since superadmin input is commented out, preserve existing role
        // Keep the existing superadmin status - don't allow changes through this form
        $updateData['superadmin'] = $user->superadmin;
        $updateData['can_be_deleted'] = $user->can_be_deleted;

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        $successMessage = $user->id === Auth::id() 
            ? 'Profil anda berjaya dikemaskini.' 
            : 'Pengguna berjaya dikemaskini.';

        return redirect()->route('admin.users.index')->with('success', $successMessage);
    }

    /**
     * Remove the specified user (admin only)
     */
    public function destroy(User $user): RedirectResponse
    {
        // Check if current user is superadmin
        if (Auth::user()->superadmin !== 'yes') {
            abort(403, 'Access denied. Only superadmin can delete users.');
        }

        // Prevent deletion of non-deletable users
        if (!$user->can_be_deleted) {
            return redirect()->route('admin.users.index')->with('error', 'Pengguna ini tidak boleh dipadam.');
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak boleh memadam akaun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berjaya dipadam.');
    }
}
