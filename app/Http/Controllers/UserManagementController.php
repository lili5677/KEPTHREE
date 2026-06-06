<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    private array $roles = [
        'admin',
        'ketua',
        'reviewer',
        'sekretariat',
        'peneliti',
    ];

    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('role') && $request->role !== 'semua') {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::whereIn('name', $this->roles)->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', $this->roles)->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role'  => ['required', Rule::in($this->roles)],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan.',
            'role.required'  => 'Role wajib dipilih.',
            'role.in'        => 'Role tidak valid.',
        ]);

        $plainPassword = Str::random(10);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($plainPassword),
            'status'   => 'aktif',
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Akun berhasil dibuat. Password: {$plainPassword}");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role'  => ['required', Rule::in($this->roles)],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan user lain.',
            'role.required'  => 'Role wajib dipilih.',
            'role.in'        => 'Role tidak valid.',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function deactivate(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->status = 'nonaktif';
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun berhasil dinonaktifkan.');
    }

    public function activate(User $user)
    {
        $user->status = 'aktif';
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun berhasil diaktifkan kembali.');
    }
}