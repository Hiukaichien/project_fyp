<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        return view('admin.users.create');
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
            'superadmin' => ['required', 'in:yes,no'],
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'superadmin' => $request->superadmin,
            'can_be_deleted' => $request->superadmin === 'no', // Superadmins can't be deleted by default
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

        return view('admin.users.edit', compact('user'));
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
        ];

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

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
