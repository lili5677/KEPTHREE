<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\Document;
use App\Models\Verification;
use App\Models\User;
use App\Models\ProtocolReviewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
{
    public function index()
    {
        $protocols = Protocol::whereIn('status', [
            'new_proposal',
            'assigned_to_secretary',
            'revision_required',
        ])
            ->with(['user', 'latestRevision'])
            ->get();

        return view('sekretariat.verifikasi.index', compact('protocols'));
    }

    public function show(Protocol $protocol)
    {
        $documents = $protocol->documents;

        $reviewers = User::role('reviewer')->get();

        $riwayatRevisi = $protocol->revisions()->latest()->get();

        return view('sekretariat.verifikasi.detail', compact(
            'protocol',
            'documents',
            'reviewers',
            'riwayatRevisi'
        ));
    }

    public function check(Request $request, Protocol $protocol)
    {
        $request->validate([
            'action' => 'required|in:lengkap,tidak_lengkap',
            'review_type' => 'required_if:action,lengkap|nullable|in:exempted,expedited,full_board',
            'catatan_kelengkapan' => 'required_if:action,tidak_lengkap|nullable|string|min:5',
            'catatan_reviewer' => 'nullable|string|max:1000',
            'exempted_reason' => 'nullable|string',
            'review_deadline' => 'nullable|date|after_or_equal:today',
            'reviewer_ids' => 'nullable|array',
            'reviewer_ids.*' => 'nullable|exists:users,id',
        ], [
            'catatan_kelengkapan.required_if' => 'Catatan kekurangan dokumen wajib diisi agar Peneliti tahu apa yang perlu dilengkapi.',
            'catatan_kelengkapan.min' => 'Catatan kekurangan dokumen minimal 5 karakter.',
        ]);

        if ($request->action === 'lengkap') {
            $dokumenWajib = Document::where('protocol_id', $protocol->id)
                ->whereIn('type', ['formulir_pengajuan', 'formulir_ringkasan'])
                ->get();

            foreach ($dokumenWajib as $doc) {
                if (!$request->has('kelengkapan.' . $doc->id)) {
                    return back()
                        ->withErrors([
                            'kelengkapan' => 'Harap centang semua dokumen wajib.'
                        ])
                        ->withInput();
                }
            }

            if ($request->review_type === 'expedited') {
                $request->validate([
                    'reviewer_ids' => 'required|array|size:3',
                    'reviewer_ids.*' => 'required|distinct|exists:users,id',
                    'review_deadline' => 'required|date|after_or_equal:today',
                ], [
                    'reviewer_ids.size' => 'Expedited Review wajib memilih 3 reviewer.',
                    'reviewer_ids.*.distinct' => 'Reviewer tidak boleh dipilih double.',
                    'review_deadline.required' => 'Deadline reviewer wajib diisi.',
                    'review_deadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini.',
                ]);
            }

            if ($request->review_type === 'full_board') {
                $request->validate([
                    'reviewer_ids' => 'required|array|size:5',
                    'reviewer_ids.*' => 'required|distinct|exists:users,id',
                    'review_deadline' => 'required|date|after_or_equal:today',
                ], [
                    'reviewer_ids.size' => 'Full Board Review wajib memilih 5 reviewer.',
                    'reviewer_ids.*.distinct' => 'Reviewer tidak boleh dipilih double.',
                    'review_deadline.required' => 'Deadline reviewer wajib diisi.',
                    'review_deadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $protocol) {
            $verificationReviewType = $request->action === 'lengkap'
                ? $request->review_type
                : 'exempted';

            $verificationKeputusan = $request->action === 'lengkap'
                ? 'approved'
                : 'rejected';

            Verification::updateOrCreate(
                ['protocol_id' => $protocol->id],
                [
                    'sekretariat_id' => Auth::id(),
                    'verified_at' => now(),
                    'notes' => $request->catatan_kelengkapan,
                    'catatan' => $request->catatan_kelengkapan,
                    'status' => $request->action,
                    'review_type' => $verificationReviewType,
                    'keputusan' => $verificationKeputusan,
                ]
            );

            ProtocolReviewer::where('protocol_id', $protocol->id)->delete();

            if ($request->action === 'tidak_lengkap') {
                $protocol->update([
                    'status' => 'revision_required',
                ]);

                DB::table('activity_logs')->insert([
                    'user_id' => $protocol->user_id,
                    'type' => 'revisi',
                    'action' => 'Dokumen tidak lengkap. Pengajuan dikembalikan kepada peneliti untuk dilengkapi.',
                    'subject_type' => Protocol::class,
                    'subject_id' => $protocol->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return;
            }

            if ($request->review_type === 'exempted') {
                $protocol->update([
                    'status' => 'approved',
                ]);

                DB::table('activity_logs')->insert([
                    'user_id' => $protocol->user_id,
                    'type' => 'verifikasi',
                    'action' => 'Dokumen lengkap. Pengajuan masuk kategori Exempted dan disetujui. SKE sedang dibuat.',
                    'subject_type' => Protocol::class,
                    'subject_id' => $protocol->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return;
            }

            if (in_array($request->review_type, ['expedited', 'full_board'])) {
                foreach ($request->reviewer_ids as $reviewerId) {
                    ProtocolReviewer::create([
                        'protocol_id' => $protocol->id,
                        'reviewer_id' => $reviewerId,
                        'deadline' => $request->review_deadline,
                        'catatan' => $request->catatan_reviewer,
                        'status' => 'pending',
                    ]);
                }

                $protocol->update([
                    'status' => 'on_review',
                ]);

                $jenisReview = $request->review_type === 'expedited'
                    ? 'Expedited Review'
                    : 'Full Board Review';

                DB::table('activity_logs')->insert([
                    'user_id' => $protocol->user_id,
                    'type' => 'penugasan',
                    'action' => 'Dokumen lengkap. Pengajuan masuk tahap ' . $jenisReview . ' dan telah ditugaskan kepada reviewer dengan deadline ' . $request->review_deadline . '.',
                    'subject_type' => Protocol::class,
                    'subject_id' => $protocol->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('sekretariat.verifikasi.index')
            ->with('success', 'Verifikasi berhasil disimpan.');
    }

    public function download(Document $document)
    {
        $fileName = $document->original_name ?? $document->name ?? 'dokumen';

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $fileName);
        }

        if (Storage::exists($document->file_path)) {
            return Storage::download($document->file_path, $fileName);
        }

        abort(404, 'File tidak ditemukan. Path: ' . $document->file_path);
    }
}