<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\ProtocolReviewer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewerManagementController extends Controller
{
    public function index()
{
    $protocols = Protocol::with([
            'user',
            'verification',
        ])
        ->where('status', 'on_review')
        ->latest('updated_at')
        ->get()
        ->map(function ($protocol) {
            $assignments = ProtocolReviewer::with('reviewer')
                ->where('protocol_id', $protocol->id)
                ->orderBy('id')
                ->get();

            $deadline = $assignments->max('deadline');
            $deadlineDate = $deadline ? \Carbon\Carbon::parse($deadline)->endOfDay() : null;

            $totalReviewers = $assignments->count();
            $completedReviewers = $assignments->where('status', 'done')->count();

            $protocol->assigned_reviewers = $assignments;
            $protocol->review_deadline = $deadline;
            $protocol->review_progress = $completedReviewers . '/' . $totalReviewers;
            $protocol->deadline_passed = $deadlineDate ? now()->greaterThan($deadlineDate) : false;

            return $protocol;
        });

    return view('sekretariat.review.index', compact('protocols'));
}

    public function edit(Protocol $protocol)
    {
        $protocol->load(['user', 'verification']);

        $assignments = ProtocolReviewer::with('reviewer')
            ->where('protocol_id', $protocol->id)
            ->orderBy('id')
            ->get();

        if ($assignments->isEmpty()) {
            return redirect()
                ->route('sekretariat.review.index')
                ->with('error', 'Proposal ini belum memiliki reviewer.');
        }

        $reviewers = User::role('reviewer')
            ->orderBy('name')
            ->get();

        $deadline = $assignments->max('deadline');
        $deadlinePassed = $deadline ? now()->greaterThan($deadline) : false;

        /*
         * Rule:
         * - Deadline boleh diubah kapan saja.
         * - Reviewer hanya boleh diubah sebelum deadline lewat.
         * - Kalau sudah ada reviewer yang submit/done, reviewer tidak diubah agar histori review aman.
         */
        $hasSubmittedReview = $assignments->where('status', 'done')->count() > 0;

        $canEditReviewer = ! $deadlinePassed && ! $hasSubmittedReview;
        $canEditDeadline = true;

        $requiredReviewerCount = $protocol->verification?->review_type === 'full_board' ? 5 : 3;

        return view('sekretariat.review.edit', compact(
            'protocol',
            'assignments',
            'reviewers',
            'deadline',
            'deadlinePassed',
            'hasSubmittedReview',
            'canEditReviewer',
            'canEditDeadline',
            'requiredReviewerCount'
        ));
    }

    public function update(Request $request, Protocol $protocol)
    {
        $assignments = ProtocolReviewer::where('protocol_id', $protocol->id)
            ->orderBy('id')
            ->get();

        if ($assignments->isEmpty()) {
            return redirect()
                ->route('sekretariat.review.index')
                ->with('error', 'Proposal ini belum memiliki reviewer.');
        }

        $protocol->load('verification');

        $requiredReviewerCount = $protocol->verification?->review_type === 'full_board' ? 5 : 3;

        $deadline = $assignments->max('deadline');
        $deadlinePassed = $deadline ? now()->greaterThan($deadline) : false;
        $hasSubmittedReview = $assignments->where('status', 'done')->count() > 0;

        $request->validate([
            'review_deadline' => 'required|date',
            'reviewer_ids' => 'nullable|array',
            'reviewer_ids.*' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use (
            $request,
            $protocol,
            $assignments,
            $requiredReviewerCount,
            $deadlinePassed,
            $hasSubmittedReview
        ) {
            /*
             * Deadline selalu boleh diubah,
             * termasuk kalau deadline lama sudah lewat.
             */
            ProtocolReviewer::where('protocol_id', $protocol->id)
                ->update([
                    'deadline' => $request->review_deadline,
                ]);

            /*
             * Reviewer hanya boleh diubah sebelum deadline,
             * dan belum ada reviewer yang submit.
             */
            $requestReviewerIds = collect($request->reviewer_ids)
                ->filter()
                ->values();

            if ($requestReviewerIds->isNotEmpty()) {
                if ($deadlinePassed) {
                    abort(403, 'Reviewer tidak dapat diubah karena deadline sudah lewat. Deadline tetap dapat diperbarui.');
                }

                if ($hasSubmittedReview) {
                    abort(403, 'Reviewer tidak dapat diubah karena sudah ada reviewer yang mengirim review.');
                }

                if ($requestReviewerIds->count() !== $requiredReviewerCount) {
                    abort(422, 'Jumlah reviewer tidak sesuai dengan tipe review.');
                }

                if ($requestReviewerIds->unique()->count() !== $requestReviewerIds->count()) {
                    abort(422, 'Reviewer tidak boleh dipilih lebih dari satu kali.');
                }

                ProtocolReviewer::where('protocol_id', $protocol->id)->delete();

                foreach ($requestReviewerIds as $reviewerId) {
                    ProtocolReviewer::create([
                        'protocol_id' => $protocol->id,
                        'reviewer_id' => $reviewerId,
                        'deadline' => $request->review_deadline,
                        'catatan' => $assignments->first()->catatan,
                        'status' => 'pending',
                    ]);
                }
            }
        });

        return redirect()
            ->route('sekretariat.review.index')
            ->with('success', 'Manajemen reviewer berhasil diperbarui.');
    }
}