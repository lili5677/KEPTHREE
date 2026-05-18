<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Peneliti\PengajuanController;
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

     // Tambahan PB-04
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::patch('/admin/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('admin.users.deactivate');
    Route::patch('/admin/users/{user}/activate', [UserManagementController::class, 'activate'])->name('admin.users.activate');
    
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

Route::middleware(['auth', 'peneliti'])->prefix('peneliti')->name('peneliti.')->group(function () {
 
    // Dashboard
    Route::get('/dashboard', [PengajuanController::class, 'dashboard'])
         ->name('dashboard');
 
    // Step Wizard Pengajuan Baru
    Route::get ('/pengajuan/baru',            [PengajuanController::class, 'create'])      ->name('pengajuan.create');
    Route::post('/pengajuan/step1',            [PengajuanController::class, 'storeStep1'])  ->name('pengajuan.step1');
    Route::get ('/pengajuan/step2',            [PengajuanController::class, 'step2'])       ->name('pengajuan.step2');
    Route::post('/pengajuan/step2',            [PengajuanController::class, 'storeStep2'])  ->name('pengajuan.step2.store');
    Route::get ('/pengajuan/step3',            [PengajuanController::class, 'step3'])       ->name('pengajuan.step3');
    Route::post('/pengajuan/submit',           [PengajuanController::class, 'submit'])      ->name('pengajuan.submit');

    // Preview dokumen dari session (PDF inline) — digunakan oleh step3
    Route::get('/pengajuan/preview/{index}',   [PengajuanController::class, 'previewDoc'])  ->name('pengajuan.preview')
         ->where('index', '[0-9]+');

    // Auto-save via AJAX
    Route::post('/pengajuan/step2/auto-save', [PengajuanController::class, 'autoSaveStep2'])
         ->name('pengajuan.step2.auto-save');
 
    // Riwayat — coming soon
    Route::get('/riwayat', [PengajuanController::class, 'riwayat'])
         ->name('riwayat');
 
    // Template — coming soon 
    Route::get('/template', [PengajuanController::class, 'template'])
         ->name('template');
});