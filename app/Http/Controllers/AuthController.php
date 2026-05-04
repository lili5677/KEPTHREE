<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'address'  => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'email.email'  => 'Format email tidak valid.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'address'  => $request->address,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'status'   => 'aktif',
        ]);

        $user->assignRole('peneliti');

        return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('admin'))       return redirect('/admin/dashboard');
            if ($user->hasRole('peneliti'))    return redirect('/peneliti/dashboard');
            if ($user->hasRole('sekretariat')) return redirect('/sekretariat/dashboard');
            if ($user->hasRole('reviewer'))    return redirect('/reviewer/dashboard');
            if ($user->hasRole('ketua'))       return redirect('/ketua/dashboard');

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Email tidak terdaftar atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}