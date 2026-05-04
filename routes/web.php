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
    Route::get('/admin/dashboard', fn() => view('dashboard.admin'));
    Route::get('/peneliti/dashboard', fn() => view('dashboard.peneliti'));
    Route::get('/sekretariat/dashboard', fn() => view('dashboard.sekretariat'));
    Route::get('/reviewer/dashboard', fn() => view('dashboard.reviewer'));
    Route::get('/ketua/dashboard', fn() => view('dashboard.ketua'));

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
});