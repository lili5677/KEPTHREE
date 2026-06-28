<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\SkeDocument;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik user
        $totalUsers = User::count();
        $totalAdmin = User::role('admin')->count();
        $totalPeneliti = User::role('peneliti')->count();
        $totalSekretariat = User::role('sekretariat')->count();
        $totalReviewer = User::role('reviewer')->count();
        $totalKetua = User::role('ketua')->count();

        // Statistik proposal
        $totalPengajuan = Protocol::count();

        $pengajuanAktif = Protocol::whereIn('status', [
            'new_proposal',
            'waiting_verification',
            'submitted',
            'menunggu_verifikasi',
            'pending_verification',
            'under_review',
            'in_review',
            'review',
        ])->count();

        $pengajuanRevisi = Protocol::whereIn('status', [
            'revision_required',
            'approved_with_recommendation',
        ])->count();

        $pengajuanDisetujui = Protocol::where('status', 'approved')->count();

        $pengajuanDitolak = Protocol::whereIn('status', [
            'rejected',
            'disapproved',
        ])->count();

        // Statistik SKE
        $totalSke = SkeDocument::count();

        $skeMenungguKonfirmasi = SkeDocument::where('status', 'menunggu_konfirmasi')->count();
        $skeRevisi = SkeDocument::where('status', 'revisi')->count();
        $skeMenungguTtd = SkeDocument::where('status', 'menunggu_ttd')->count();
        $skeSudahTtd = SkeDocument::where('status', 'sudah_ttd')->count();
        $skeTerbit = SkeDocument::where('status', 'terbit')->count();

        // Data terbaru
        $pengajuanTerbaru = Protocol::with('user')
            ->latest()
            ->take(5)
            ->get();

        $skeTerbaru = SkeDocument::with(['protocol.user', 'ketua'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalAdmin',
            'totalPeneliti',
            'totalSekretariat',
            'totalReviewer',
            'totalKetua',

            'totalPengajuan',
            'pengajuanAktif',
            'pengajuanRevisi',
            'pengajuanDisetujui',
            'pengajuanDitolak',

            'totalSke',
            'skeMenungguKonfirmasi',
            'skeRevisi',
            'skeMenungguTtd',
            'skeSudahTtd',
            'skeTerbit',

            'pengajuanTerbaru',
            'skeTerbaru'
        ));
    }
}