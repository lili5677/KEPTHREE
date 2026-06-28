<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\ProtocolReviewer;
use App\Models\Review;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $sekretariatId = auth()->id();

        /*
         * Statistik utama dashboard sekretariat
         */
        $menungguVerifikasi = Protocol::where('sekretariat_id', $sekretariatId)
            ->whereIn('status', [
                'assigned_to_secretary',
                'revision_required',
            ])
            ->count();

        $sedangOnReview = Protocol::where('sekretariat_id', $sekretariatId)
            ->whereIn('status', [
                'on_review',
                'under_review',
                'in_review',
                'review',
            ])
            ->count();

        $perluKeputusan = $this->countPerluKeputusan($sekretariatId);

        $selesaiBulanIni = Protocol::where('sekretariat_id', $sekretariatId)
            ->whereIn('status', [
                'approved',
                'disapproved',
                'rejected',
            ])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        /*
         * Proposal Prioritas - Verifikasi
         */
        $prioritasVerifikasi = Protocol::with(['user', 'latestRevision'])
            ->where('sekretariat_id', $sekretariatId)
            ->whereIn('status', [
                'assigned_to_secretary',
                'revision_required',
            ])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($protocol) {
                $protocol->action_label = 'Verifikasi Dokumen';
                $protocol->deadline_display = null;
                $protocol->protocol_number = $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id;

                return $protocol;
            });

        /*
         * Proposal Prioritas - Keputusan
         */
        $prioritasKeputusan = $this->getPrioritasKeputusan($sekretariatId)
            ->take(5)
            ->map(function ($protocol) {
                $protocol->action_label = 'Secretary Decision';
                $protocol->deadline_display = null;
                $protocol->protocol_number = $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id;

                return $protocol;
            });

        $prioritas = $prioritasVerifikasi
            ->merge($prioritasKeputusan)
            ->sortByDesc('updated_at')
            ->take(6)
            ->values();

        /*
         * Review Progress
         */
        $reviewProgress = Protocol::with(['user'])
            ->where('sekretariat_id', $sekretariatId)
            ->whereIn('status', [
                'on_review',
                'under_review',
                'in_review',
                'review',
            ])
            ->latest('updated_at')
            ->take(6)
            ->get()
            ->map(function ($protocol) {
                $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)
                    ->distinct('reviewer_id')
                    ->count('reviewer_id');

                $completedReviews = $this->countCompletedUniqueReviewers($protocol->id);

                $protocol->judul = $protocol->title;
                $protocol->progress = $completedReviews . '/' . $totalReviewers;

                if ($totalReviewers <= 0) {
                    $protocol->status_text = 'Belum ada reviewer yang ditugaskan.';
                } elseif ($completedReviews >= $totalReviewers) {
                    $protocol->status_text = 'Semua reviewer telah menyelesaikan review.';
                } else {
                    $protocol->status_text = 'Menunggu ' . max($totalReviewers - $completedReviews, 0) . ' reviewer lagi.';
                }

                return $protocol;
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

    private function countPerluKeputusan(int $sekretariatId): int
    {
        return $this->getPrioritasKeputusan($sekretariatId)->count();
    }

    private function getPrioritasKeputusan(int $sekretariatId): Collection
    {
        $protocolIds = Review::whereNotNull('reviewed_at')
            ->pluck('protocol_id')
            ->unique();

        return Protocol::with(['user', 'verification', 'latestSekretariatDecision'])
            ->where('sekretariat_id', $sekretariatId)
            ->whereIn('id', $protocolIds)
            ->whereHas('verification', function ($query) {
                $query->whereIn('review_type', ['expedited', 'full_board']);
            })
            ->get()
            ->filter(function ($protocol) {
                $latestDecision = $protocol->latestSekretariatDecision;

                if ($latestDecision && in_array($latestDecision->keputusan, ['approved', 'rejected'])) {
                    return false;
                }

                if ($latestDecision && $latestDecision->keputusan === 'approved_with_recommendation') {
                    $adaAssignmentBaruSetelahKeputusan = ProtocolReviewer::where('protocol_id', $protocol->id)
                        ->where('created_at', '>', $latestDecision->created_at)
                        ->exists();

                    if (!$adaAssignmentBaruSetelahKeputusan) {
                        return false;
                    }
                }

                $totalReviewers = ProtocolReviewer::where('protocol_id', $protocol->id)
                    ->distinct('reviewer_id')
                    ->count('reviewer_id');

                $completedReviews = $this->countCompletedUniqueReviewers($protocol->id);

                return $totalReviewers > 0 && $completedReviews >= $totalReviewers;
            })
            ->sortByDesc('updated_at')
            ->values();
    }

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
}