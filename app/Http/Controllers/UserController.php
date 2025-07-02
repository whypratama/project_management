<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with(['roles', 'organization'])->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::whereIn('name', ['Admin', 'User'])->pluck('name', 'name');
        $organizations = Organization::pluck('name', 'id');
        $jobTitles = JobTitle::pluck('name', 'id');
        return view('users.create', compact('roles', 'organizations', 'jobTitles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organization_id' => 'required|exists:organizations,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'role' => 'required|exists:roles,name',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'organization_id' => $request->organization_id,
            'job_title_id' => $request->job_title_id,
        ]);
        $user->assignRole($request->role);
        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::whereIn('name', ['Admin', 'User'])->pluck('name', 'name');
        $organizations = Organization::pluck('name', 'id');
        $jobTitles = JobTitle::pluck('name', 'id');

        // Ambil role user saat ini
        $userRole = $user->roles->pluck('name')->first();

        return view('users.edit', compact('user', 'roles', 'organizations', 'jobTitles', 'userRole'));
    }

    /**
     * Memperbarui data user di database.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'organization_id' => 'required|exists:organizations,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'organization_id' => $request->organization_id,
            'job_title_id' => $request->job_title_id,
        ]);

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Sinkronisasi role
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
