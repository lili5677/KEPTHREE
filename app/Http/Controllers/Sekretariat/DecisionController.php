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

        $query = Protocol::whereIn('id', $protocolIds)
            ->with(['user', 'verification', 'sekretariatDecision']);

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
                $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)->count();

                $completedReviews = Review::where('protocol_id', $protocol->id)
                    ->whereNotNull('reviewed_at')
                    ->count();

                $protocol->review_progress = $completedReviews . '/' . $totalReviewers;
                $protocol->is_complete = $totalReviewers > 0 && $completedReviews == $totalReviewers;
                $protocol->review_type_label = $protocol->verification->review_type ?? 'unknown';
                $protocol->decision_status = $protocol->sekretariatDecision
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
        $protocol->load(['user', 'verification', 'sekretariatDecision']);

        $reviewType = $protocol->verification->review_type ?? null;

        $reviews = Review::where('protocol_id', $protocol->id)
            ->whereNotNull('reviewed_at')
            ->with('reviewer')
            ->get();

        if ($reviews->isEmpty()) {
            return redirect()
                ->route('sekretariat.decision.index')
                ->with('error', 'Belum ada review dari reviewer.');
        }

        $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)->count();
        $completedReviews = $reviews->count();

        $minReviewer = $reviewType === 'full_board' ? 5 : 3;

        $isComplete = $totalReviewers > 0
            && $completedReviews == $totalReviewers
            && $completedReviews >= $minReviewer;

        $existingDecision = SekretariatDecision::where('protocol_id', $protocol->id)->first();

        return view('sekretariat.decision.show', compact(
            'protocol',
            'reviews',
            'totalReviewers',
            'completedReviews',
            'isComplete',
            'minReviewer',
            'reviewType',
            'existingDecision'
        ));
    }

    /**
     * Menyimpan atau mengubah keputusan sekretariat.
     */
    public function store(Request $request, Protocol $protocol)
    {
        $protocol->load(['user', 'verification']);

        $reviewType = $protocol->verification->review_type ?? null;

        if (!in_array($reviewType, ['expedited', 'full_board'])) {
            return back()->with('error', 'Protocol ini tidak memerlukan Secretary Decision.');
        }

        $request->validate([
            'keputusan' => 'required|in:approved,approved_with_recommendation,rejected',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)->count();

        $completedReviews = Review::where('protocol_id', $protocol->id)
            ->whereNotNull('reviewed_at')
            ->count();

        $minReviewer = $reviewType === 'full_board' ? 5 : 3;

        if ($completedReviews < $totalReviewers) {
            return back()->with('error', 'Belum semua reviewer menyelesaikan feedback.');
        }

        if ($completedReviews < $minReviewer) {
            return back()->with('error', "Minimal $minReviewer reviewer diperlukan.");
        }

        DB::transaction(function () use ($request, $protocol) {
            SekretariatDecision::updateOrCreate(
                [
                    'protocol_id' => $protocol->id,
                ],
                [
                    'sekretariat_id' => Auth::id(),
                    'keputusan' => $request->keputusan,
                    'catatan' => $request->catatan,
                ]
            );

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
                'action' => "Menetapkan keputusan: {$keputusanLabel[$request->keputusan]} untuk protocol '{$protocol->title}'",
                'subject_type' => Protocol::class,
                'subject_id' => $protocol->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $message = "Keputusan akhir untuk pengajuan '{$protocol->title}' adalah: {$keputusanLabel[$request->keputusan]}.";

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
}