<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\ProtocolReviewer;
use App\Models\Review;
use App\Models\Revision;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RevisiController extends Controller
{
    private const KEPUTUSAN_PERLU_REVIEW_ULANG = [
        'approved_with_recommendation',
        'minor_revision',
        'major_revision',
    ];

    private const STATUS_BISA_UPLOAD_REVISI = [
        'approved_with_recommendation',
        'revision_required',
    ];

    /**
     * Halaman upload revisi. Mendukung dua skenario:
     *
     * 1. Status 'revision_required': dokumen pengajuan awal
     *    dinyatakan tidak lengkap oleh Sekretaris, sebelum masuk tahap
     *    review. Menampilkan catatan dari proses Verifikasi.
     *
     * 2. Status 'approved_with_recommendation': Secretary Decision pasca-
     *    review meminta perbaikan. Menampilkan catatan Secretary Decision
     *    & Reviewer Feedback.
     */
    public function show(Protocol $protocol)
    {
        // Hanya pemilik pengajuan yang bisa mengakses
        if ($protocol->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($protocol->status, self::STATUS_BISA_UPLOAD_REVISI)) {
            return redirect()
                ->route('peneliti.riwayat.show', $protocol->id)
                ->with('error', 'Pengajuan ini tidak/belum membutuhkan revisi.');
        }

        $protocol->load(['documents', 'verification']);

        $isVerifikasiAwal = $protocol->status === 'revision_required';

        $sudahKirimRevisiMenungguSekretaris = $isVerifikasiAwal
            ? $protocol->sudah_kirim_revisi_menunggu_sekretaris
            : false;

        if ($sudahKirimRevisiMenungguSekretaris) {
            return redirect()
                ->route('peneliti.riwayat.show', $protocol->id)
                ->with('error', 'Revisi Anda sudah terkirim dan sedang menunggu diproses oleh Sekretariat.');
        }

        // Skenario 1: catatan dari proses Verifikasi awal (dokumen tidak lengkap)
        $verification = $isVerifikasiAwal ? $protocol->verification : null;

        // Skenario 2: catatan Secretary Decision babak terbaru
        $sekretariatDecision = $isVerifikasiAwal ? null : $protocol->latestSekretariatDecision;

        // Skenario 2: catatan Reviewer Feedback babak terakhir per reviewer
        $reviewerFeedback = collect();

        if (!$isVerifikasiAwal) {
            $latestAssignmentIdsPerReviewer = ProtocolReviewer::where('protocol_id', $protocol->id)
                ->selectRaw('MAX(id) as id')
                ->groupBy('reviewer_id')
                ->pluck('id');

            $reviewerFeedback = Review::whereIn('protocol_reviewer_id', $latestAssignmentIdsPerReviewer)
                ->whereNotNull('reviewed_at')
                ->with('reviewer')
                ->latest('reviewed_at')
                ->get();
        }

        // Histori revisi yang pernah diunggah sebelumnya untuk pengajuan ini
        $riwayatRevisi = Revision::where('protocol_id', $protocol->id)
            ->latest()
            ->get();

        return view('peneliti.revisi', compact(
            'protocol',
            'isVerifikasiAwal',
            'verification',
            'sekretariatDecision',
            'reviewerFeedback',
            'riwayatRevisi'
        ));
    }

    // Menyimpan dokumen revisi yang diunggah Peneliti
    public function store(Request $request, Protocol $protocol)
    {
        if ($protocol->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($protocol->status, self::STATUS_BISA_UPLOAD_REVISI)) {
            return redirect()
                ->route('peneliti.riwayat.show', $protocol->id)
                ->with('error', 'Pengajuan ini tidak/belum membutuhkan revisi.');
        }

        $isVerifikasiAwal = $protocol->status === 'revision_required';

        if ($isVerifikasiAwal && $protocol->sudah_kirim_revisi_menunggu_sekretaris) {
            return redirect()
                ->route('peneliti.riwayat.show', $protocol->id)
                ->with('error', 'Revisi Anda sudah terkirim dan sedang menunggu diproses oleh Sekretariat.');
        }

        $request->validate([
            'catatan_revisi' => 'required|string|min:10',
            'file_revisi'    => 'required|file|max:2048',
        ], [
            'catatan_revisi.required' => 'Catatan/penjelasan perbaikan wajib diisi.',
            'catatan_revisi.min'      => 'Catatan perbaikan minimal 10 karakter.',
            'file_revisi.required'    => 'File dokumen revisi wajib diunggah.',
            'file_revisi.max'         => 'Ukuran file maksimal 2 MB.',
        ]);

        $file = $request->file('file_revisi');
        $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'pdf');

        DB::transaction(function () use ($request, $protocol, $file, $ext, $isVerifikasiAwal) {

            $permanentPath = "protocols/{$protocol->id}/revisions/revisi_" . time() . ".{$ext}";

            Storage::disk('local')->put(
                $permanentPath,
                file_get_contents($file->getPathname())
            );

            Revision::create([
                'protocol_id'        => $protocol->id,
                'catatan_revisi'     => $request->catatan_revisi,
                'file_path'          => $permanentPath,
                'original_filename'  => $file->getClientOriginalName(),
                'submitted_at'       => now(),
            ]);

            if ($isVerifikasiAwal) {
                $this->prosesRevisiVerifikasiAwal($protocol);
            } else {
                $this->prosesRevisiPascaReview($protocol);
            }
        });

        $message = $isVerifikasiAwal
            ? 'Revisi dokumen berhasil diunggah. Sekretariat akan memverifikasi ulang kelengkapan dokumen Anda.'
            : 'Revisi berhasil diunggah dan diteruskan ke reviewer untuk ditelaah ulang. Status pengajuan Anda sekarang: On Review.';

        return redirect()
            ->route('peneliti.riwayat.show', $protocol->id)
            ->with('success', $message);
    }

    // Memproses revisi untuk Skenario 1: verifikasi awal
    private function prosesRevisiVerifikasiAwal(Protocol $protocol): void
    {
        DB::table('activity_logs')->insert([
            'user_id'      => Auth::id(),
            'type'         => 'revisi',
            'action'       => "Peneliti mengunggah dokumen tambahan/pelengkap untuk pengajuan '{$protocol->title}'. Menunggu verifikasi ulang Sekretariat.",
            'subject_type' => Protocol::class,
            'subject_id'   => $protocol->id,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        if ($protocol->sekretariat_id) {
            Notification::create([
                'user_id' => $protocol->sekretariat_id,
                'message' => "Peneliti telah mengunggah dokumen pelengkap untuk pengajuan '{$protocol->title}' ({$protocol->nomor_registrasi}). Mohon verifikasi ulang kelengkapan dokumennya.",
                'is_read' => false,
            ]);
        }
    }

    // Mengembalikan langsung ke reviewer yang ditugaskan
    private function prosesRevisiPascaReview(Protocol $protocol): void
    {
        $latestAssignmentIdsPerReviewer = ProtocolReviewer::where('protocol_id', $protocol->id)
            ->selectRaw('MAX(id) as id')
            ->groupBy('reviewer_id')
            ->pluck('id');

        $reviewerIdsPerluUlang = Review::whereIn('protocol_reviewer_id', $latestAssignmentIdsPerReviewer)
            ->whereNotNull('reviewed_at')
            ->whereIn('keputusan', self::KEPUTUSAN_PERLU_REVIEW_ULANG)
            ->pluck('reviewer_id')
            ->unique();

        // Babak assignment selanjutnya untuk protocol ini
        $nextRound = ProtocolReviewer::where('protocol_id', $protocol->id)->max('round') + 1;

        foreach ($reviewerIdsPerluUlang as $reviewerId) {
            ProtocolReviewer::create([
                'protocol_id' => $protocol->id,
                'reviewer_id' => $reviewerId,
                'deadline'    => now()->addDays(7)->toDateString(),
                'catatan'     => 'Telaah ulang dokumen revisi dari peneliti merespons rekomendasi sebelumnya.',
                'status'      => 'pending',
                'round'       => $nextRound,
            ]);
        }

        // Status protocol langsung masuk tahap review lagi (skip proses Sekretaris)
        $protocol->update([
            'status' => 'on_review',
        ]);

        DB::table('activity_logs')->insert([
            'user_id'      => Auth::id(),
            'type'         => 'revisi',
            'action'       => "Peneliti mengunggah dokumen revisi untuk pengajuan '{$protocol->title}'. "
                               . "Revisi diteruskan ke " . $reviewerIdsPerluUlang->count() . " reviewer untuk ditelaah ulang.",
            'subject_type' => Protocol::class,
            'subject_id'   => $protocol->id,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Notifikasi ke masing-masing reviewer yang mendapat tugas ulang
        foreach ($reviewerIdsPerluUlang as $reviewerId) {
            Notification::create([
                'user_id' => $reviewerId,
                'message' => "Peneliti telah mengunggah revisi untuk pengajuan '{$protocol->title}' ({$protocol->nomor_registrasi}). Mohon telaah ulang dokumen revisi tersebut.",
                'is_read' => false,
            ]);
        }
    }

    // Download dokumen revisi
    public function download(Revision $revision)
    {
        $protocol = $revision->protocol;

        $isOwner = $protocol->user_id === Auth::id();

        $isReviewerTerkait = ProtocolReviewer::where('protocol_id', $protocol->id)
            ->where('reviewer_id', Auth::id())
            ->exists();

        $isSekretarisTerkait = $protocol->sekretariat_id === Auth::id();

        if (!$isOwner && !$isReviewerTerkait && !$isSekretarisTerkait) {
            abort(403, 'Akses ditolak.');
        }

        if (!Storage::disk('local')->exists($revision->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $downloadName = $revision->original_filename ?: basename($revision->file_path);

        return Storage::disk('local')->download($revision->file_path, $downloadName);
    }
}