<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['ketua', 'reviewer', 'sekretariat'])->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|in:ketua,reviewer,sekretariat',
        ], [
            'email.unique' => 'Email sudah digunakan.',
            'email.email'  => 'Format email tidak valid.',
            'role.in'      => 'Role tidak valid.',
        ]);

        $plainPassword = Str::random(10);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($plainPassword),
            'status'   => 'aktif',
        ]);

        $user->assignRole($request->role);

        return redirect('/admin/users')->with('success', "Akun berhasil dibuat! Password: {$plainPassword}");
    }
}