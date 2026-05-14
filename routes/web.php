<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Admin\TemplateController;

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // ===== DASHBOARD =====
    Route::get('/admin/dashboard', fn() => view('dashboard.admin'))->name('admin.dashboard');
    Route::get('/peneliti/dashboard', fn() => view('dashboard.peneliti'));
    Route::get('/sekretariat/dashboard', fn() => view('dashboard.sekretariat'));
    Route::get('/reviewer/dashboard', fn() => view('dashboard.reviewer'));
    Route::get('/ketua/dashboard', fn() => view('dashboard.ketua'));

    // ===== USER MANAGEMENT =====
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');

    // ===== TEMPLATE =====
    Route::get('/admin/template',                     [TemplateController::class, 'index'])   ->name('admin.template.index');
    Route::post('/admin/template',                    [TemplateController::class, 'store'])   ->name('admin.template.store');
    Route::get('/admin/template/{template}/edit',     [TemplateController::class, 'edit'])    ->name('admin.template.edit');
    Route::put('/admin/template/{template}',          [TemplateController::class, 'update'])  ->name('admin.template.update');
    Route::delete('/admin/template/{template}',       [TemplateController::class, 'destroy']) ->name('admin.template.destroy');
    Route::get('/admin/template/{template}/download', [TemplateController::class, 'download'])->name('admin.template.download');

    // ===== PLACEHOLDER (belum ada controller) =====
    Route::get('/admin/sekretaris', fn() => abort(404))->name('admin.sekretaris.index');
    Route::get('/admin/dokumen', fn() => abort(404))->name('admin.dokumen.index');
    Route::get('/admin/ethical-clearance', fn() => abort(404))->name('admin.ethical-clearance.index');
    Route::get('/admin/log', fn() => abort(404))->name('admin.log.index');

});