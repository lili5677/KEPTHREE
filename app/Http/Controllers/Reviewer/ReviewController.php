<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ProtocolReviewer;
use App\Models\Review;
use App\Models\Protocol;
use App\Models\Document;
use App\Models\Revision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function index(Request $request)
    {
        $reviewerId = Auth::id();

        $search = trim((string) $request->query('search', ''));
        $deadlineFilter = $request->query('deadline', 'all');

        $allowedPerPage = [5, 10, 15, 25];
        $perPage = (int) $request->query('per_page', 5);

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }

        $baseQuery = ProtocolReviewer::with(['protocol.user'])
            ->where('reviewer_id', $reviewerId)
            ->where('status', 'pending');

        if ($search !== '') {
            $baseQuery->whereHas('protocol', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('nomor_registrasi', 'like', '%' . $search . '%')
                    ->orWhere('program_studi', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($deadlineFilter === 'overdue') {
            $baseQuery->whereNotNull('deadline')
                ->whereDate('deadline', '<', now()->toDateString());
        } elseif ($deadlineFilter === 'near') {
            $baseQuery->whereNotNull('deadline')
                ->whereBetween('deadline', [
                    now()->startOfDay(),
                    now()->addDays(3)->endOfDay(),
                ]);
        } elseif ($deadlineFilter === 'normal') {
            $baseQuery->where(function ($query) {
                $query->whereNull('deadline')
                    ->orWhereDate('deadline', '>', now()->addDays(3)->toDateString());
            });
        }

        $initialAssignments = (clone $baseQuery)
            ->where(function ($query) {
                $query->whereNull('round')
                    ->orWhere('round', '<=', 1);
            })
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('deadline', 'asc')
            ->latest()
            ->paginate($perPage, ['*'], 'initial_page')
            ->withQueryString();

        $revisionAssignments = (clone $baseQuery)
            ->where('round', '>', 1)
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('deadline', 'asc')
            ->latest()
            ->paginate($perPage, ['*'], 'revision_page')
            ->withQueryString();

        $filters = [
            'search' => $search,
            'deadline' => $deadlineFilter,
            'per_page' => $perPage,
        ];

        return view('reviewer.tugas.index', compact(
            'initialAssignments',
            'revisionAssignments',
            'filters'
        ));
    }

    public function show(ProtocolReviewer $assignment)
    {
        if ($assignment->reviewer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas review ini.');
        }

        $assignment->load([
            'protocol.user',
            'protocol.documents',
            'protocol.revisions' => fn ($query) => $query->latest(),
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

        $deadline = $assignment->deadline
            ? \Carbon\Carbon::parse($assignment->deadline)
            : null;

        $isOverdue = $deadline && $deadline->isPast() && !$deadline->isToday();

        if ($isOverdue) {
            return redirect()
                ->route('reviewer.tugas.index')
                ->with('error', 'Deadline review sudah lewat. Anda tidak dapat melakukan aksi review.');
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

        return redirect()
            ->route('reviewer.riwayat')
            ->with('error', 'Review yang sudah disubmit tidak dapat diedit kembali.');
    }

    public function updateHistory(Request $request, ProtocolReviewer $assignment)
    {
        return redirect()
            ->route('reviewer.riwayat')
            ->with('error', 'Review yang sudah disubmit tidak dapat diedit kembali.');
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

    public function downloadDocument(Document $document)
    {
        $hasAccess = ProtocolReviewer::where('protocol_id', $document->protocol_id)
            ->where('reviewer_id', Auth::id())
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $fileName = $document->original_name
            ?? $document->name
            ?? basename($document->file_path);

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $fileName);
        }

        if (Storage::exists($document->file_path)) {
            return Storage::download($document->file_path, $fileName);
        }

        abort(404, 'File tidak ditemukan.');
    }

    public function downloadRevision(Revision $revision)
    {
        $hasAccess = ProtocolReviewer::where('protocol_id', $revision->protocol_id)
            ->where('reviewer_id', Auth::id())
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke dokumen revisi ini.');
        }

        $fileName = $revision->original_filename
            ?: basename($revision->file_path);

        if (Storage::disk('public')->exists($revision->file_path)) {
            return Storage::disk('public')->download($revision->file_path, $fileName);
        }

        if (Storage::exists($revision->file_path)) {
            return Storage::download($revision->file_path, $fileName);
        }

        abort(404, 'File revisi tidak ditemukan.');
    }
}
