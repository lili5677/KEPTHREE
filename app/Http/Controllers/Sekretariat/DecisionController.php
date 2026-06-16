<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\Review;
use App\Models\ProtocolReviewer;
use App\Models\SekretariatDecision;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DecisionController extends Controller
{
    /**
     * Menampilkan daftar protocol yang menunggu keputusan
     */
    public function index()
    {
        $protocols = Protocol::where('status', 'waiting_secretary_decision')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($protocol) {
                $total = ProtocolReviewer::where('protocol_id', $protocol->id)->count();
                $selesai = Review::where('protocol_id', $protocol->id)
                    ->whereNotNull('submitted_at')
                    ->count();
                
                $protocol->review_progress = "$selesai/$total";
                $protocol->is_complete = $total > 0 && $total == $selesai;
                return $protocol;
            });

        return view('sekretariat.decision.index', compact('protocols'));
    }

    /**
     * Menampilkan detail protocol + rangkuman feedback reviewer
     */
    public function show(Protocol $protocol)
    {
        // Validasi: hanya protocol dengan status waiting_secretary_decision
        if ($protocol->status !== 'waiting_secretary_decision') {
            return redirect()->route('sekretariat.decision.index')
                ->with('error', 'Protocol ini tidak menunggu keputusan.');
        }

        // Validasi: hanya expedited atau full_board
        if (!in_array($protocol->review_type, ['expedited', 'full_board'])) {
            return redirect()->route('sekretariat.decision.index')
                ->with('error', 'Protocol ini tidak memerlukan Secretary Decision.');
        }

        // Ambil semua feedback reviewer yang sudah submit
        $reviews = Review::where('protocol_id', $protocol->id)
            ->whereNotNull('submitted_at')
            ->with('reviewer')
            ->get();

        $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)->count();
        $completedReviews = $reviews->count();
        $isComplete = $totalReviewers > 0 && $totalReviewers == $completedReviews;

        // Tentukan minimal reviewer berdasarkan jenis review
        $minReviewer = $protocol->review_type === 'expedited' ? 3 : 5;

        return view('sekretariat.decision.show', compact(
            'protocol',
            'reviews',
            'totalReviewers',
            'completedReviews',
            'isComplete',
            'minReviewer'
        ));
    }

    /**
     * Memproses keputusan Sekretaris
     */
    public function store(Request $request, Protocol $protocol)
    {
        // Validasi status
        if ($protocol->status !== 'waiting_secretary_decision') {
            return back()->with('error', 'Protocol ini tidak menunggu keputusan.');
        }

        // Validasi review type
        if (!in_array($protocol->review_type, ['expedited', 'full_board'])) {
            return back()->with('error', 'Protocol ini tidak memerlukan Secretary Decision.');
        }

        // Validasi input
        $request->validate([
            'keputusan' => 'required|in:approved,approved_with_recommendation,disapproved',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Validasi khusus: Disapproved hanya untuk Full Board
        if ($request->keputusan === 'disapproved' && $protocol->review_type === 'expedited') {
            return back()->with('error', 'Disapproved hanya tersedia untuk Full Board Review.');
        }

        $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)->count();
        $completedReviews = Review::where('protocol_id', $protocol->id)
            ->whereNotNull('submitted_at')
            ->count();

        if ($totalReviewers != $completedReviews) {
            return back()->with('error', 'Belum semua reviewer menyelesaikan feedback.');
        }

        $minReviewer = $protocol->review_type === 'expedited' ? 3 : 5;
        if ($totalReviewers < $minReviewer) {
            return back()->with('error', "Minimal $minReviewer reviewer diperlukan.");
        }

        DB::transaction(function () use ($request, $protocol) {
            // 1. Simpan ke tabel sekretariat_decisions
            SekretariatDecision::create([
                'protocol_id' => $protocol->id,
                'sekretariat_id' => Auth::id(),
                'keputusan' => $request->keputusan,
                'catatan' => $request->catatan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Update status protocol
            $statusMap = [
                'approved' => 'approved',
                'approved_with_recommendation' => 'approved_with_recommendation',
                'disapproved' => 'disapproved',
            ];
            $protocol->update(['status' => $statusMap[$request->keputusan]]);

            // 3. Buat activity log menggunakan DB::table (karena model ActivityLog belum ada)
            $keputusanLabel = [
                'approved' => 'Disetujui',
                'approved_with_recommendation' => 'Disetujui dengan Rekomendasi',
                'disapproved' => 'Ditolak',
            ];

            DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'type' => 'keputusan',
                'action' => "Menetapkan keputusan: {$keputusanLabel[$request->keputusan]} untuk protocol '{$protocol->title}'",
                'subject_type' => 'App\\Models\\Protocol',
                'subject_id' => $protocol->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Kirim notifikasi ke Peneliti
            $message = "Keputusan akhir untuk pengajuan '{$protocol->title}' adalah: {$keputusanLabel[$request->keputusan]}.";
            if ($request->catatan) {
                $message .= " Catatan: {$request->catatan}";
            }

            Notification::create([
                'user_id' => $protocol->user_id,
                'message' => $message,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('sekretariat.decision.index')
            ->with('success', 'Keputusan berhasil disimpan.');
    }
}