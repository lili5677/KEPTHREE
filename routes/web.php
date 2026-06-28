<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Peneliti\PengajuanController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\AssignSekretarisController;
use App\Http\Controllers\Admin\DokumenController;
use App\Http\Controllers\Admin\EthicalClearanceController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Peneliti\RiwayatController;
use App\Http\Controllers\Peneliti\RevisiController;
use App\Http\Controllers\Peneliti\TemplateController as PenelitiTemplateController;
use App\Http\Controllers\Sekretariat\DashboardController;
use App\Http\Controllers\Sekretariat\VerifikasiController;
use App\Http\Controllers\Reviewer\ReviewController;
use App\Http\Controllers\Peneliti\SkeController;
use App\Http\Controllers\Ketua\DashboardController as KetuaDashboardController;
use App\Http\Controllers\Ketua\SkeController as KetuaSkeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =====================================================================
// DASHBOARD UMUM
// =====================================================================
Route::middleware('auth')->group(function () {
    Route::get('/peneliti/dashboard', fn() => view('dashboard.peneliti'))->name('peneliti.dashboard.view');
    Route::get('/sekretariat/dashboard', [DashboardController::class, 'index'])->name('sekretariat.dashboard');
    Route::get('/reviewer/dashboard', fn() => view('dashboard.reviewer'))->name('reviewer.dashboard.view');
});

// =====================================================================
// DOWNLOAD DOKUMEN GLOBAL / COMPATIBILITY ROUTE
// Untuk view lama yang masih memakai route('verifikasi.download')
// =====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/verifikasi/download/{document}', [VerifikasiController::class, 'download'])
        ->name('verifikasi.download');
});

// =====================================================================
// SEKRETARIAT
// =====================================================================
Route::middleware(['auth'])
    ->prefix('sekretariat')
    ->name('sekretariat.')
    ->group(function () {

        // Verifikasi
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/{protocol}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
        Route::post('/verifikasi/{protocol}/check', [VerifikasiController::class, 'check'])->name('verifikasi.check');
        Route::get('/verifikasi/download/{document}', [VerifikasiController::class, 'download'])->name('verifikasi.download');

        // Decision
        Route::get('/decision', [App\Http\Controllers\Sekretariat\DecisionController::class, 'index'])->name('decision.index');
        Route::get('/decision/{protocol}', [App\Http\Controllers\Sekretariat\DecisionController::class, 'show'])->name('decision.show');
        Route::post('/decision/{protocol}', [App\Http\Controllers\Sekretariat\DecisionController::class, 'store'])->name('decision.store');
    });

// =====================================================================
// ADMIN
// =====================================================================
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // User Management
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');

    Route::patch('/admin/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
        ->name('admin.users.reset-password');

    Route::patch('/admin/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('admin.users.deactivate');
    Route::patch('/admin/users/{user}/activate', [UserManagementController::class, 'activate'])->name('admin.users.activate');

    // Template
    Route::get('/admin/template', [TemplateController::class, 'index'])->name('admin.template.index');
    Route::post('/admin/template', [TemplateController::class, 'store'])->name('admin.template.store');
    Route::get('/admin/template/{template}/edit', [TemplateController::class, 'edit'])->name('admin.template.edit');
    Route::put('/admin/template/{template}', [TemplateController::class, 'update'])->name('admin.template.update');
    Route::delete('/admin/template/{template}', [TemplateController::class, 'destroy'])->name('admin.template.destroy');
    Route::get('/admin/template/{template}/download', [TemplateController::class, 'download'])->name('admin.template.download');

    // Assign Sekretaris
    Route::get('/admin/sekretaris', [AssignSekretarisController::class, 'index'])->name('admin.sekretaris.index');
    Route::post('/admin/sekretaris/{protocol}/assign', [AssignSekretarisController::class, 'assign'])->name('admin.sekretaris.assign');

    // Dokumen
    Route::get('/admin/dokumen', [DokumenController::class, 'index'])->name('admin.dokumen.index');
    Route::get('/admin/dokumen/{protocol}', [DokumenController::class, 'show'])->name('admin.dokumen.show');
    Route::get('/admin/dokumen/{protocol}/download', [DokumenController::class, 'download'])->name('admin.dokumen.download');

    // Ethical Clearance / SKE
    Route::get('/admin/ethical-clearance', [EthicalClearanceController::class, 'index'])->name('admin.ethical-clearance.index');
    Route::post('/admin/ethical-clearance/{protocol}/terbitkan', [EthicalClearanceController::class, 'terbitkanSke'])->name('admin.ethical-clearance.terbitkan');
    Route::post('/admin/ethical-clearance/ske/{ske}/kirim-ketua', [EthicalClearanceController::class, 'kirimKeKetua'])->name('admin.ethical-clearance.kirim-ketua');
    Route::post('/admin/ethical-clearance/ske/{ske}/proses-revisi', [EthicalClearanceController::class, 'prosesRevisi'])->name('admin.ethical-clearance.proses-revisi');
    Route::post('/admin/ethical-clearance/ske/{ske}/publish', [EthicalClearanceController::class, 'publish'])->name('admin.ethical-clearance.publish');
    Route::get('/admin/ethical-clearance/ske/{ske}/download', [EthicalClearanceController::class, 'downloadSke'])->name('admin.ethical-clearance.download');
    Route::get('/admin/ethical-clearance/ske/{ske}/preview', [EthicalClearanceController::class, 'previewSke'])->name('admin.ethical-clearance.preview');
    Route::post('/admin/ethical-clearance/save-setting', [EthicalClearanceController::class, 'saveSettingForm'])->name('admin.ethical-clearance.save-setting');
    Route::get('/admin/ethical-clearance/ske/{ske}/revisi', [EthicalClearanceController::class, 'editRevisi'])->name('admin.ethical-clearance.revisi.edit');
    Route::put('/admin/ethical-clearance/ske/{ske}/revisi', [EthicalClearanceController::class, 'updateRevisi'])->name('admin.ethical-clearance.revisi.update');

    
    // Log Aktivitas
    Route::get('/log', [ActivityLogController::class, 'index'])->name('log.index');
});

// =====================================================================
// PENELITI
// =====================================================================
Route::middleware(['auth', 'peneliti'])->prefix('peneliti')->name('peneliti.')->group(function () {

    Route::get('/dashboard', [PengajuanController::class, 'dashboard'])->name('dashboard');

    Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan/step1', [PengajuanController::class, 'storeStep1'])->name('pengajuan.step1');
    Route::get('/pengajuan/step2', [PengajuanController::class, 'step2'])->name('pengajuan.step2');
    Route::post('/pengajuan/step2', [PengajuanController::class, 'storeStep2'])->name('pengajuan.step2.store');
    Route::get('/pengajuan/step3', [PengajuanController::class, 'step3'])->name('pengajuan.step3');
    Route::post('/pengajuan/submit', [PengajuanController::class, 'submit'])->name('pengajuan.submit');

    Route::get('/pengajuan/preview/{index}', [PengajuanController::class, 'previewDoc'])->name('pengajuan.preview')
         ->where('index', '[0-9]+');

    Route::post('/pengajuan/step2/auto-save', [PengajuanController::class, 'autoSaveStep2'])->name('pengajuan.step2.auto-save');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/{protocol}', [RiwayatController::class, 'show'])->name('riwayat.show');
    Route::get('/riwayat/dokumen/{document}/download', [RiwayatController::class, 'downloadDokumen'])->name('riwayat.download');

    // Revisi
    Route::get('/riwayat/{protocol}/revisi', [RevisiController::class, 'show'])->name('revisi.show');
    Route::post('/riwayat/{protocol}/revisi', [RevisiController::class, 'store'])->name('revisi.store');
    Route::get('/revisi/{revision}/download', [RevisiController::class, 'download'])->name('revisi.download');

    // SKE Peneliti
Route::get('/ske/{ske}', [SkeController::class, 'show'])->name('ske.show');
Route::get('/ske/{ske}/preview', [SkeController::class, 'preview'])->name('ske.preview');
Route::get('/ske/{ske}/download', [SkeController::class, 'download'])->name('ske.download');
Route::post('/ske/{ske}/approve', [SkeController::class, 'approve'])->name('ske.approve');
Route::post('/ske/{ske}/reject', [SkeController::class, 'reject'])->name('ske.reject');

    Route::get('/template', [PenelitiTemplateController::class, 'index'])->name('template');
    Route::get('/template/{template}/download', [PenelitiTemplateController::class, 'download'])->name('template.download');
});

// =====================================================================
// REVIEWER
// =====================================================================
Route::middleware(['auth', 'reviewer'])
    ->prefix('reviewer')
    ->name('reviewer.')
    ->group(function () {

        Route::get('/dashboard', [ReviewController::class, 'dashboard'])->name('dashboard');
        Route::get('/tugas-review', [ReviewController::class, 'index'])->name('tugas.index');
        Route::get('/tugas-review/{assignment}', [ReviewController::class, 'show'])->name('tugas.show');
        Route::post('/tugas-review/{assignment}/submit', [ReviewController::class, 'submit'])->name('tugas.submit');
        Route::get('/riwayat-review', [ReviewController::class, 'history'])->name('riwayat');

        Route::get('/dokumen/{document}/preview', [ReviewController::class, 'previewDocument'])->name('dokumen.preview');
        Route::get('/dokumen/{document}/download', [ReviewController::class, 'downloadDocument'])->name('dokumen.download');
        Route::get('/revisi/{revision}/download', [ReviewController::class, 'downloadRevision'])->name('revisi.download');

        Route::get('/riwayat-review/{assignment}/edit', [ReviewController::class, 'editHistory'])->name('riwayat.edit');
        Route::put('/riwayat-review/{assignment}', [ReviewController::class, 'updateHistory'])->name('riwayat.update');
    });


    // =====================================================================
// KETUA
// =====================================================================
Route::middleware(['auth', 'ketua'])
    ->prefix('ketua')
    ->name('ketua.')
    ->group(function () {

        Route::get('/dashboard', [KetuaDashboardController::class, 'index'])->name('dashboard');

        // Tanda Tangan SKE
        Route::get('/tanda-tangan-ske', [KetuaSkeController::class, 'index'])->name('ske.index');
        Route::get('/tanda-tangan-ske/{ske}', [KetuaSkeController::class, 'show'])->name('ske.show');
        Route::get('/tanda-tangan-ske/{ske}/preview', [KetuaSkeController::class, 'preview'])->name('ske.preview');
        Route::post('/tanda-tangan-ske/{ske}/upload', [KetuaSkeController::class, 'uploadSigned'])->name('ske.upload');

        // Riwayat TTD
        Route::get('/riwayat-ttd', [KetuaSkeController::class, 'history'])->name('riwayat');
        Route::get('/riwayat-ttd/{ske}', [KetuaSkeController::class, 'historyShow'])->name('riwayat.show');
        Route::get('/riwayat-ttd/{ske}/download', [KetuaSkeController::class, 'downloadSigned'])->name('riwayat.download');

        // NIP
        Route::patch('/profil/nip', [KetuaDashboardController::class, 'updateNip'])->name('profil.nip.update');
    });