<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // ========== 1. STATISTIK KARTU ==========
        $menungguVerifikasi = Protocol::where('status', 'new_proposal')->count();
        $sedangOnReview = Protocol::where('status', 'on_review')->count();
        $perluKeputusan = Protocol::where('status', 'waiting_secretary_decision')->count();
        $selesaiBulanIni = Protocol::whereIn('status', ['approved', 'issued'])
            ->whereMonth('updated_at', now()->month)
            ->count();

        // ========== 2. PROPOSAL PRIORITAS ==========
        // Ambil proposal yang butuh aksi sekretaris, urut dari terbaru, batasi 3
        $prioritas = Protocol::whereIn('status', ['new_proposal', 'assigned_to_secretary', 'waiting_secretary_decision'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($protocol) {
                // Tentukan label aksi berdasarkan status
                if (in_array($protocol->status, ['new_proposal', 'assigned_to_secretary'])) {
                    $protocol->action_label = 'Verifikasi Dokumen';
                } elseif ($protocol->status == 'waiting_secretary_decision') {
                    $protocol->action_label = 'Secretary Decision';
                } else {
                    $protocol->action_label = 'Assign Reviewer';
                }
                
                // Hitung deadline terdekat (jika ada review)
                $protocol->deadline_display = null;
                if ($protocol->reviews && $protocol->reviews->isNotEmpty()) {
                    $nearestDeadline = $protocol->reviews->whereNull('submitted_at')->min('deadline');
                    if ($nearestDeadline) {
                        $protocol->deadline_display = \Carbon\Carbon::parse($nearestDeadline)->diffForHumans();
                    }
                }
                
                return $protocol;
            });

        // ========== 3. REVIEW PROGRESS ==========
        $reviewProgress = Protocol::where('status', 'on_review')
            ->with('reviews')
            ->take(3)
            ->get()
            ->map(function ($protocol) {
                $total = $protocol->reviews->count();
                $selesai = $protocol->reviews->whereNotNull('submitted_at')->count();
                $sisa = $total - $selesai;
                
                return (object) [
                    'id' => $protocol->id,
                    'judul' => $protocol->title,
                    'progress' => $total > 0 ? "$selesai/$total" : "0/0",
                    'status_text' => $sisa > 0 ? "$sisa reviewer belum selesai" : "Review lengkap - siap keputusan"
                ];
            });

        return view('dashboard.sekretariat', compact(
            'menungguVerifikasi',
            'sedangOnReview',
            'perluKeputusan',
            'selesaiBulanIni',
            'prioritas',
            'reviewProgress'
        ));
    }
}