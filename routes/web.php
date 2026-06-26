<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Peneliti\PengajuanController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\AssignSekretarisController;
use App\Http\Controllers\Admin\DokumenController;
use App\Http\Controllers\Admin\EthicalClearanceController;
use App\Http\Controllers\Peneliti\RiwayatController;
use App\Http\Controllers\Sekretariat\DashboardController;
use App\Http\Controllers\Sekretariat\VerifikasiController;
use App\Http\Controllers\Sekretariat\DecisionController;
use App\Http\Controllers\Peneliti\TemplateController as PenelitiTemplateController;
use App\Http\Controllers\Reviewer\ReviewController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
->prefix('admin')
->name('admin.')
->group(function () {

    Route::get('/dashboard', fn() => view('dashboard.admin'))->name('dashboard');

    // USER
    Route::resource('users', UserManagementController::class)->except(['show', 'destroy']);
    Route::patch('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');
    Route::patch('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('users.activate');

    // TEMPLATE
    Route::resource('template', TemplateController::class);
    Route::get('/template/{template}/download', [TemplateController::class, 'download'])->name('template.download');

    // SEKRETARIS
    Route::get('/sekretaris', [AssignSekretarisController::class, 'index'])->name('sekretaris.index');
    Route::post('/sekretaris/{protocol}/assign', [AssignSekretarisController::class, 'assign'])->name('sekretaris.assign');

    // DOKUMEN
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{protocol}', [DokumenController::class, 'show'])->name('dokumen.show');
    Route::get('/dokumen/{protocol}/download', [DokumenController::class, 'download'])->name('dokumen.download');

    // ETHICAL CLEARANCE
    Route::prefix('ethical-clearance')->name('ethical-clearance.')->group(function () {
        Route::get('/', [EthicalClearanceController::class, 'index'])->name('index');
        Route::post('{protocol}/terbitkan', [EthicalClearanceController::class, 'terbitkanSke'])->name('terbitkan');
        Route::post('ske/{ske}/kirim-ketua', [EthicalClearanceController::class, 'kirimKeKetua'])->name('kirim-ketua');
        Route::post('ske/{ske}/proses-revisi', [EthicalClearanceController::class, 'prosesRevisi'])->name('proses-revisi');
        Route::post('ske/{ske}/publish', [EthicalClearanceController::class, 'publish'])->name('publish');
        Route::get('ske/{ske}/download', [EthicalClearanceController::class, 'downloadSke'])->name('download');
        Route::get('ske/{ske}/preview', [EthicalClearanceController::class, 'previewSke'])->name('preview');
        Route::post('save-setting', [EthicalClearanceController::class, 'saveSettingForm'])->name('save-setting');
    });

});


/*
|--------------------------------------------------------------------------
| SEKRETARIAT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
->prefix('sekretariat')
->name('sekretariat.')
->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // VERIFIKASI
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{protocol}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
    Route::post('/verifikasi/{protocol}/check', [VerifikasiController::class, 'check'])->name('verifikasi.check');
    Route::get('/verifikasi/download/{document}', [VerifikasiController::class, 'download'])->name('verifikasi.download');

    // DECISION
    Route::get('/decision', [DecisionController::class, 'index'])->name('decision.index');
    Route::get('/decision/{protocol}', [DecisionController::class, 'show'])->name('decision.show');
    Route::post('/decision/{protocol}', [DecisionController::class, 'store'])->name('decision.store');

});


/*
|--------------------------------------------------------------------------
| PENELITI
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'peneliti'])
->prefix('peneliti')
->name('peneliti.')
->group(function () {

    Route::get('/dashboard', [PengajuanController::class, 'dashboard'])->name('dashboard');

    Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan/step1', [PengajuanController::class, 'storeStep1'])->name('pengajuan.step1');
    Route::get('/pengajuan/step2', [PengajuanController::class, 'step2'])->name('pengajuan.step2');
    Route::post('/pengajuan/step2', [PengajuanController::class, 'storeStep2'])->name('pengajuan.step2.store');
    Route::get('/pengajuan/step3', [PengajuanController::class, 'step3'])->name('pengajuan.step3');
    Route::post('/pengajuan/submit', [PengajuanController::class, 'submit'])->name('pengajuan.submit');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/{protocol}', [RiwayatController::class, 'show'])->name('riwayat.show');

    Route::get('/template', [PenelitiTemplateController::class, 'index'])->name('template');
    Route::get('/template/{template}/download', [PenelitiTemplateController::class, 'download'])->name('template.download');

});


/*
|--------------------------------------------------------------------------
| REVIEWER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'reviewer'])
->prefix('reviewer')
->name('reviewer.')
->group(function () {

    Route::get('/dashboard', [ReviewController::class, 'dashboard'])->name('dashboard');

    Route::get('/tugas-review', [ReviewController::class, 'index'])->name('tugas.index');
    Route::get('/tugas-review/{assignment}', [ReviewController::class, 'show'])->name('tugas.show');
    Route::post('/tugas-review/{assignment}/submit', [ReviewController::class, 'submit'])->name('tugas.submit');

    Route::get('/riwayat-review', [ReviewController::class, 'history'])->name('riwayat');

});