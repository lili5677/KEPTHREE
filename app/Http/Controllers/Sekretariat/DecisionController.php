<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\Review;
use App\Models\ProtocolReviewer;
use App\Models\SekretariatDecision;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DecisionController extends Controller
{
    /**
     * Menampilkan daftar protocol yang sudah memiliki review
     * dan perlu keputusan sekretariat.
     */
    public function index(Request $request)
    {
        $filterReviewType = $request->get('review_type');
        $filterDecision = $request->get('decision_status');
        $filterProgress = $request->get('progress_status');

        $protocolIds = Review::whereNotNull('reviewed_at')
            ->pluck('protocol_id')
            ->unique();

        $query = Protocol::where('sekretariat_id', Auth::id())
            ->whereIn('id', $protocolIds)
            ->with(['user', 'verification', 'latestSekretariatDecision']);

        if ($filterReviewType) {
            $query->whereHas('verification', function ($q) use ($filterReviewType) {
                $q->where('review_type', $filterReviewType);
            });
        }

        if ($filterDecision === 'decided') {
            $query->whereHas('sekretariatDecision');
        }

        if ($filterDecision === 'undecided') {
            $query->doesntHave('sekretariatDecision');
        }

        $protocols = $query
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($protocol) {
                $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)
                    ->distinct('reviewer_id')
                    ->count('reviewer_id');

                $completedReviews = $this->countCompletedUniqueReviewers($protocol->id);

                $protocol->review_progress = $completedReviews . '/' . $totalReviewers;
                $protocol->is_complete = $totalReviewers > 0 && $completedReviews == $totalReviewers;
                $protocol->review_type_label = $protocol->verification->review_type ?? 'unknown';
                $protocol->decision_status = $protocol->latestSekretariatDecision
                    ? 'Sudah Diputuskan'
                    : 'Belum Diputuskan';

                return $protocol;
            });

        if ($filterProgress === 'complete') {
            $protocols = $protocols->where('is_complete', true);
        }

        if ($filterProgress === 'incomplete') {
            $protocols = $protocols->where('is_complete', false);
        }

        return view('sekretariat.decision.index', compact(
            'protocols',
            'filterReviewType',
            'filterDecision',
            'filterProgress'
        ));
    }

    /**
     * Menampilkan detail protocol, feedback reviewer,
     * dan form keputusan / edit keputusan.
     */
    public function show(Protocol $protocol)
    {
        if ((int) $protocol->sekretariat_id !== (int) Auth::id()) {
            abort(403, 'Proposal ini bukan tugas sekretariat Anda.');
        }

        $protocol->load([
            'user',
            'verification',
            'documents',
            'revisions',
            'latestSekretariatDecision',
            'sekretariatDecisions.sekretariat'
        ]);

        $reviewType = $protocol->verification->review_type ?? null;

        // Ambil review dari assignment reviewer TERBARU untuk masing-masing reviewer
        $latestAssignmentIdsPerReviewer = ProtocolReviewer::where('protocol_id', $protocol->id)
            ->selectRaw('MAX(id) as id')
            ->groupBy('reviewer_id')
            ->pluck('id');

        $reviews = Review::whereIn('protocol_reviewer_id', $latestAssignmentIdsPerReviewer)
            ->whereNotNull('reviewed_at')
            ->with('reviewer', 'assignment')
            ->get();

        if ($reviews->isEmpty()) {
            return redirect()
                ->route('sekretariat.decision.index')
                ->with('error', 'Belum ada review dari reviewer.');
        }

        $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)
            ->distinct('reviewer_id')
            ->count('reviewer_id');

        $completedReviews = $this->countCompletedUniqueReviewers($protocol->id);

        $minReviewer = $reviewType === 'full_board' ? 5 : 3;

        // Histori keputusan sekretariat babak sebelumnya
        $decisionHistory = $protocol->sekretariatDecisions;
        $existingDecision = $decisionHistory->first();

        // Apakah protocol ini sedang dalam babak revisi
        $isRevisionRound = $decisionHistory
            ->where('keputusan', 'approved_with_recommendation')
            ->isNotEmpty();

        $menungguRevisiPeneliti = $this->isMenungguRevisiPeneliti($protocol, $existingDecision);

        $isComplete = $totalReviewers > 0
            && $completedReviews == $totalReviewers
            && $completedReviews >= $minReviewer
            && ! $menungguRevisiPeneliti;

        return view('sekretariat.decision.show', compact(
            'protocol',
            'reviews',
            'totalReviewers',
            'completedReviews',
            'isComplete',
            'minReviewer',
            'reviewType',
            'existingDecision',
            'decisionHistory',
            'isRevisionRound',
            'menungguRevisiPeneliti'
        ));
    }

    /**
     * Menyimpan atau mengubah keputusan sekretariat.
     */
    public function store(Request $request, Protocol $protocol)
    {
        if ((int) $protocol->sekretariat_id !== (int) Auth::id()) {
            abort(403, 'Proposal ini bukan tugas sekretariat Anda.');
        }

        $protocol->load(['user', 'verification']);

        $reviewType = $protocol->verification->review_type ?? null;

        if (! in_array($reviewType, ['expedited', 'full_board'])) {
            return back()->with('error', 'Protocol ini tidak memerlukan Secretary Decision.');
        }

        $request->validate([
            'keputusan' => 'required|in:approved,approved_with_recommendation,rejected',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $existingDecision = SekretariatDecision::where('protocol_id', $protocol->id)
            ->orderByDesc('round')
            ->first();

        if ($this->isMenungguRevisiPeneliti($protocol, $existingDecision)) {
            return back()->with('error', 'Pengajuan ini sedang menunggu Peneliti mengunggah revisi. Keputusan baru belum dapat ditetapkan.');
        }

        $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)
            ->distinct('reviewer_id')
            ->count('reviewer_id');

        $completedReviews = $this->countCompletedUniqueReviewers($protocol->id);

        $minReviewer = $reviewType === 'full_board' ? 5 : 3;

        if ($completedReviews < $totalReviewers) {
            return back()->with('error', 'Belum semua reviewer menyelesaikan feedback.');
        }

        if ($completedReviews < $minReviewer) {
            return back()->with('error', "Minimal $minReviewer reviewer diperlukan.");
        }

        DB::transaction(function () use ($request, $protocol) {

            // Babak keputusan sekretariat berikutnya
            $nextRound = SekretariatDecision::where('protocol_id', $protocol->id)->max('round') + 1;

            SekretariatDecision::create([
                'protocol_id' => $protocol->id,
                'sekretariat_id' => Auth::id(),
                'keputusan' => $request->keputusan,
                'catatan' => $request->catatan,
                'round' => $nextRound,
            ]);

            $statusMap = [
                'approved' => 'approved',
                'approved_with_recommendation' => 'approved_with_recommendation',
                'rejected' => 'disapproved',
            ];

            $protocol->update([
                'status' => $statusMap[$request->keputusan],
            ]);

            $keputusanLabel = [
                'approved' => 'Disetujui',
                'approved_with_recommendation' => 'Disetujui dengan Rekomendasi',
                'rejected' => 'Ditolak',
            ];

            DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'type' => 'keputusan',
                'action' => "Menetapkan keputusan (babak {$nextRound}): {$keputusanLabel[$request->keputusan]} untuk protocol '{$protocol->title}'",
                'subject_type' => Protocol::class,
                'subject_id' => $protocol->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->keputusan === 'approved_with_recommendation') {
                $message = "Sekretariat meminta perbaikan (Approved with Recommendation) untuk pengajuan '{$protocol->title}'. Mohon unggah revisi melalui halaman Riwayat Pengajuan Anda.";
            } else {
                $message = "Keputusan akhir untuk pengajuan '{$protocol->title}' adalah: {$keputusanLabel[$request->keputusan]}.";
            }

            if ($request->catatan) {
                $message .= " Catatan: {$request->catatan}";
            }

            Notification::create([
                'user_id' => $protocol->user_id,
                'message' => $message,
                'is_read' => false,
            ]);
        });

        return redirect()
            ->route('sekretariat.decision.index')
            ->with('success', 'Keputusan berhasil disimpan.');
    }

    /**
     * Hitung jumlah reviewer unik yang sudah menyelesaikan review untuk protocol tertentu.
     */
    private function countCompletedUniqueReviewers(int $protocolId): int
    {
        $latestAssignmentIdsPerReviewer = ProtocolReviewer::where('protocol_id', $protocolId)
            ->selectRaw('MAX(id) as id')
            ->groupBy('reviewer_id')
            ->pluck('id');

        return ProtocolReviewer::whereIn('id', $latestAssignmentIdsPerReviewer)
            ->where('status', 'done')
            ->count();
    }

    /**
     * Cek apakah protocol sedang menunggu Peneliti mengunggah revisi.
     */
    private function isMenungguRevisiPeneliti(Protocol $protocol, ?SekretariatDecision $existingDecision): bool
    {
        if (! $existingDecision || $existingDecision->keputusan !== 'approved_with_recommendation') {
            return false;
        }

        $adaAssignmentBaruSetelahKeputusan = ProtocolReviewer::where('protocol_id', $protocol->id)
            ->where('created_at', '>', $existingDecision->created_at)
            ->exists();

        return ! $adaAssignmentBaruSetelahKeputusan;
    }
}