<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ProtocolReviewer;
use App\Models\Review;
use App\Models\Protocol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function dashboard()
    {
        $reviewerId = Auth::id();

        $pendingCount = ProtocolReviewer::where('reviewer_id', $reviewerId)
            ->where('status', 'pending')
            ->count();

        $doneCount = ProtocolReviewer::where('reviewer_id', $reviewerId)
            ->where('status', 'done')
            ->count();

        $nearDeadlineCount = ProtocolReviewer::where('reviewer_id', $reviewerId)
            ->where('status', 'pending')
            ->whereDate('deadline', '<=', now()->addDays(3)->toDateString())
            ->count();

        $latestAssignments = ProtocolReviewer::with(['protocol.user'])
            ->where('reviewer_id', $reviewerId)
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.reviewer', compact(
            'pendingCount',
            'doneCount',
            'nearDeadlineCount',
            'latestAssignments'
        ));
    }

    public function index()
    {
        $assignments = ProtocolReviewer::with(['protocol.user'])
            ->where('reviewer_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('reviewer.tugas.index', compact('assignments'));
    }

    public function show(ProtocolReviewer $assignment)
    {
        if ($assignment->reviewer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas review ini.');
        }

        $assignment->load([
            'protocol.user',
            'protocol.documents',
            'reviewer',
        ]);

        $existingReview = Review::where('protocol_reviewer_id', $assignment->id)
            ->where('reviewer_id', Auth::id())
            ->first();

        return view('reviewer.tugas.show', compact(
            'assignment',
            'existingReview'
        ));
    }

    public function submit(Request $request, ProtocolReviewer $assignment)
    {
        if ($assignment->reviewer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas review ini.');
        }

        if ($assignment->status === 'done') {
            return back()->with('error', 'Tugas review ini sudah pernah disubmit.');
        }

        $request->validate([
            'catatan' => 'required|string|min:10',
            'keputusan' => 'required|in:approved,approved_with_recommendation,minor_revision,major_revision,rejected',
        ], [
            'catatan.required' => 'Catatan review wajib diisi.',
            'catatan.min' => 'Catatan review minimal 10 karakter.',
            'keputusan.required' => 'Keputusan review wajib dipilih.',
            'keputusan.in' => 'Keputusan review tidak valid.',
        ]);

        DB::transaction(function () use ($request, $assignment) {
            Review::updateOrCreate(
                [
                    'protocol_reviewer_id' => $assignment->id,
                    'reviewer_id' => Auth::id(),
                ],
                [
                    'protocol_id' => $assignment->protocol_id,
                    'catatan' => $request->catatan,
                    'keputusan' => $request->keputusan,
                    'reviewed_at' => now(),
                ]
            );

            $assignment->update([
                'status' => 'done',
            ]);

            $masihAdaReviewerPending = ProtocolReviewer::where('protocol_id', $assignment->protocol_id)
                ->where('status', 'pending')
                ->exists();

            if (!$masihAdaReviewerPending) {
                $assignment->protocol->update([
                    'status' => 'waiting_secretary_decision',
                ]);

                DB::table('activity_logs')->insert([
                    'user_id' => $assignment->protocol->user_id,
                    'type' => 'review',
                    'action' => 'Seluruh reviewer telah menyelesaikan proses review. Pengajuan menunggu keputusan sekretariat.',
                    'subject_type' => Protocol::class,
                    'subject_id' => $assignment->protocol_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('reviewer.riwayat')
            ->with('success', 'Hasil review berhasil disubmit.');
    }

   public function history()
    {
        $assignments = ProtocolReviewer::with(['protocol.user', 'review'])
            ->where('reviewer_id', Auth::id())
            ->where('status', 'done')
            ->latest()
            ->paginate(10);

        return view('reviewer.riwayat.index', compact('assignments'));
    }

    public function editHistory(ProtocolReviewer $assignment)
    {
        if ($assignment->reviewer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke review ini.');
        }

        if ($assignment->status !== 'done') {
            return redirect()
                ->route('reviewer.riwayat')
                ->with('error', 'Review yang belum selesai tidak dapat diedit dari halaman riwayat.');
        }

        $assignment->load([
            'protocol.user',
            'reviewer',
        ]);

        $review = Review::where('protocol_reviewer_id', $assignment->id)
            ->where('reviewer_id', Auth::id())
            ->firstOrFail();

        return view('reviewer.riwayat.edit', compact(
            'assignment',
            'review'
        ));
    }

    public function updateHistory(Request $request, ProtocolReviewer $assignment)
    {
        if ($assignment->reviewer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke review ini.');
        }

        if ($assignment->status !== 'done') {
            return redirect()
                ->route('reviewer.riwayat')
                ->with('error', 'Review yang belum selesai tidak dapat diperbarui dari halaman riwayat.');
        }

        $request->validate([
            'catatan' => 'required|string|min:10',
            'keputusan' => 'required|in:approved,approved_with_recommendation,minor_revision,major_revision,rejected',
        ], [
            'catatan.required' => 'Catatan review wajib diisi.',
            'catatan.min' => 'Catatan review minimal 10 karakter.',
            'keputusan.required' => 'Keputusan review wajib dipilih.',
            'keputusan.in' => 'Keputusan review tidak valid.',
        ]);

        DB::transaction(function () use ($request, $assignment) {
            $review = Review::where('protocol_reviewer_id', $assignment->id)
                ->where('reviewer_id', Auth::id())
                ->firstOrFail();

            $review->update([
                'catatan' => $request->catatan,
                'keputusan' => $request->keputusan,
            ]);

            DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'type' => 'review',
                'action' => 'Reviewer memperbarui keputusan dan catatan review.',
                'subject_type' => Protocol::class,
                'subject_id' => $assignment->protocol_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('reviewer.riwayat')
            ->with('success', 'Keputusan dan catatan review berhasil diperbarui.');
    }

    public function previewDocument(Document $document)
    {
        $hasAccess = ProtocolReviewer::where('protocol_id', $document->protocol_id)
            ->where('reviewer_id', Auth::id())
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            $path = Storage::disk('public')->path($document->file_path);

            return response()->file($path);
        }

        if (Storage::exists($document->file_path)) {
            $path = Storage::path($document->file_path);

            return response()->file($path);
        }

        abort(404, 'File tidak ditemukan.');
    }
}