<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', fn() => view('dashboard.admin'))->name('admin.dashboard');
    Route::get('/peneliti/dashboard', fn() => view('dashboard.peneliti'));
    Route::get('/sekretariat/dashboard', fn() => view('dashboard.sekretariat'));
    Route::get('/reviewer/dashboard', fn() => view('dashboard.reviewer'));
    Route::get('/ketua/dashboard', fn() => view('dashboard.ketua'));

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');

    // Tambahan PB-04
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::patch('/admin/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('admin.users.deactivate');
    Route::patch('/admin/users/{user}/activate', [UserManagementController::class, 'activate'])->name('admin.users.activate');
});