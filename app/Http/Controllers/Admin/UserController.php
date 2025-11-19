<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('roles');
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Filter by role
            if ($request->filled('role')) {
                $query->role($request->role);
            }
            
            $users = $query->latest()->paginate(15)->appends($request->only(['search', 'role']));
            $roles = Role::all();
            
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $request->ip()])
                ->log('Mengakses halaman user management');
            
            return view('admin.users.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            Log::error('User index error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data user');
        }
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                ],
                'role' => ['required', 'exists:roles,name'],
                'phone' => ['nullable', 'string', 'max:20'],
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.unique' => 'Email sudah terdaftar',
                'password.required' => 'Password wajib diisi',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
                'role.required' => 'Role wajib dipilih',
            ]);
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
            ]);
            
            // Assign role
            $user->assignRole($validated['role']);
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties(['ip' => $request->ip(), 'role' => $validated['role']])
                ->log('Membuat user baru: ' . $user->name);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('User store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first();
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['nullable', 'confirmed', Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                ],
                'role' => ['required', 'exists:roles,name'],
                'phone' => ['nullable', 'string', 'max:20'],
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.unique' => 'Email sudah digunakan user lain',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
                'role.required' => 'Role wajib dipilih',
            ]);
            
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);
            

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }
            
            // Sync role
            $user->syncRoles([$validated['role']]);
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties(['ip' => $request->ip(), 'role' => $validated['role']])
                ->log('Mengupdate user: ' . $user->name);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate');
        } catch (\Exception $e) {
            Log::error('User update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return back()->with('error', 'Anda tidak dapat menghapus akun sendiri');
            }
            
            $userName = $user->name;
            $user->delete();
            
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => request()->ip(), 'deleted_user' => $userName])
                ->log('Menghapus user: ' . $userName);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('User destroy error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
