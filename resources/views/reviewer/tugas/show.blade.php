@extends('layouts.reviewer')
@section('title', 'Detail Tugas Review')

@section('content')

@php
    $protocol = $assignment->protocol;

    $deadline = $assignment->deadline
        ? \Carbon\Carbon::parse($assignment->deadline)
        : null;

    $isDone = $assignment->status === 'done';

    // Deadline hari ini masih boleh review.
    // Kalau sudah lewat dari hari deadline, form dikunci.
    $isOverdue = $deadline && $deadline->isPast() && !$deadline->isToday();
    $isLockedByDeadline = !$isDone && $isOverdue;

    $round = (int) ($assignment->round ?? 1);
    $isRevisionReview = $round > 1;

    $revisions = $protocol->revisions
        ? $protocol->revisions->sortByDesc(fn ($rev) => $rev->submitted_at ?? $rev->created_at)
        : collect();

    $pageTitle = $isRevisionReview
        ? 'Tinjauan Revisi Proposal'
        : 'Detail Tugas Review';

    $pageSubtitle = $isRevisionReview
        ? 'Periksa dokumen revisi dari peneliti, lalu kirim keputusan review revisi.'
        : 'Periksa dokumen, berikan catatan, lalu submit hasil review Anda.';

    $documentTitle = $isRevisionReview
        ? 'Dokumen Pengajuan Awal'
        : 'Dokumen Pengajuan';

    $revisionTitle = $isRevisionReview
        ? 'Dokumen Revisi yang Perlu Ditinjau'
        : 'Dokumen Revisi dari Peneliti';

    $formTitle = $isRevisionReview
        ? 'Form Keputusan Revisi'
        : 'Form Hasil Review';

    $submitLabel = $isRevisionReview
        ? 'Kirim Keputusan Revisi'
        : 'Submit Review';

    $doneTitle = $isRevisionReview
        ? 'Keputusan revisi sudah disubmit.'
        : 'Review sudah disubmit.';

    $doneText = $isRevisionReview
        ? 'Keputusan revisi Anda sudah masuk ke sistem dan menunggu telaah sekretariat.'
        : 'Hasil review Anda sudah masuk ke sistem.';

    $noteText = $isRevisionReview
        ? 'Setelah disubmit, keputusan revisi akan dikirim ke sekretariat untuk ditelaah kembali.'
        : 'Setelah disubmit, hasil review akan dikirim ke sekretariat dan tugas ini masuk ke Riwayat Review.';

    $decisionLabels = [
        'approved' => 'Layak',
        'approved_with_recommendation' => 'Layak dengan Rekomendasi',
        'minor_revision' => 'Revisi Minor',
        'major_revision' => 'Revisi Mayor',
        'rejected' => 'Tidak Layak',
    ];
@endphp

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
    <div>
        <h1>{{ $pageTitle }}</h1>
        <p>{{ $pageSubtitle }}</p>
    </div>

    @if($isDone)
        <a href="{{ route('reviewer.riwayat') }}" class="btn-kep btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    @else
        <a href="{{ route('reviewer.tugas.index') }}" class="btn-kep btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    @endif
</div>

<div class="review-detail-grid">

    {{-- KOLOM KIRI --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- INFORMASI PROTOKOL --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-file-text"></i> Informasi Protokol
            </div>

            <div class="detail-row">
                <span class="detail-key">Nomor Registrasi</span>
                <span class="detail-value">
                    {{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Judul Penelitian</span>
                <span class="detail-value">
                    {{ $protocol->title ?? '-' }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Peneliti</span>
                <span class="detail-value">
                    {{ $protocol->user->name ?? '-' }}
                    <br>
                    <small class="text-muted">{{ $protocol->user->email ?? '-' }}</small>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Program Studi</span>
                <span class="detail-value">
                    {{ $protocol->program_studi ?? '-' }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Sumber Pendanaan</span>
                <span class="detail-value">
                    {{ $protocol->sumber_pendanaan ?? '-' }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Durasi Penelitian</span>
                <span class="detail-value">
                    {{ $protocol->durasi_penelitian ?? '-' }} bulan
                </span>
            </div>

            <div class="detail-row" style="border-bottom:none;align-items:flex-start;">
                <span class="detail-key">Ringkasan Penelitian</span>
                <span class="detail-value" style="line-height:1.65;">
                    {{ $protocol->ringkasan_penelitian ?? '-' }}
                </span>
            </div>
        </div>

        {{-- DOKUMEN REVISI --}}
        @if($isRevisionReview)
            <div class="kep-card revision-highlight-card">
                <div class="kep-card-title">
                    <i class="bi bi-file-earmark-arrow-up"></i> {{ $revisionTitle }}
                </div>

                @if($revisions->isNotEmpty())
                    @foreach($revisions as $rev)
                        <div class="doc-review-item">
                            <div class="doc-icon revision-icon">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </div>

                            <div style="flex:1;min-width:0;">
                                <div class="doc-name">
                                    {{ $rev->original_filename ?: 'Dokumen Revisi' }}
                                </div>
                                <div class="doc-sub">
                                    {{ $rev->submitted_at?->translatedFormat('d M Y, H:i') ?? $rev->created_at?->translatedFormat('d M Y, H:i') }}
                                    @if($rev->catatan_revisi)
                                        — {{ $rev->catatan_revisi }}
                                    @endif
                                </div>
                            </div>

                            <div style="display:flex;gap:.45rem;flex-shrink:0;">
                                <a href="{{ route('reviewer.revisi.download', $rev->id) }}"
                                   class="btn-kep btn-primary"
                                   style="font-size:.8rem;padding:.42rem .7rem;">
                                    <i class="bi bi-download"></i> Unduh
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-reviewer-state">
                        <i class="bi bi-folder2-open"></i>
                        <p>Belum ada dokumen revisi yang dilampirkan.</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- DOKUMEN PENGAJUAN --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-paperclip"></i> {{ $documentTitle }}
            </div>

            @forelse($protocol->documents as $doc)
                <div class="doc-review-item">
                    <div class="doc-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>

                    <div style="flex:1;min-width:0;">
                        <div class="doc-name">
                            {{ $doc->name ?? $doc->original_name ?? 'Dokumen' }}
                        </div>
                        <div class="doc-sub">
                            {{ $doc->label ?? $doc->type ?? '-' }}
                        </div>
                    </div>

                    <div style="display:flex;gap:.45rem;flex-shrink:0;">
                        <a href="{{ route('reviewer.dokumen.preview', $doc->id) }}"
                           target="_blank"
                           class="btn-kep btn-outline"
                           style="font-size:.8rem;padding:.42rem .7rem;">
                            <i class="bi bi-eye"></i> Lihat
                        </a>

                        <a href="{{ route('verifikasi.download', $doc->id) }}"
                           class="btn-kep btn-primary"
                           style="font-size:.8rem;padding:.42rem .7rem;">
                            <i class="bi bi-download"></i> Unduh
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-reviewer-state">
                    <i class="bi bi-folder2-open"></i>
                    <p>Belum ada dokumen yang dilampirkan.</p>
                </div>
            @endforelse
        </div>

        {{-- DOKUMEN REVISI TAMBAHAN UNTUK REVIEW AWAL / RIWAYAT --}}
        @if(!$isRevisionReview && $revisions->isNotEmpty())
            <div class="kep-card">
                <div class="kep-card-title">
                    <i class="bi bi-file-earmark-arrow-up"></i> {{ $revisionTitle }}
                </div>

                @foreach($revisions as $rev)
                    <div class="doc-review-item">
                        <div class="doc-icon">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div class="doc-name">
                                {{ $rev->original_filename ?: 'Dokumen Revisi' }}
                            </div>
                            <div class="doc-sub">
                                {{ $rev->submitted_at?->translatedFormat('d M Y, H:i') ?? $rev->created_at?->translatedFormat('d M Y, H:i') }}
                                @if($rev->catatan_revisi)
                                    — {{ $rev->catatan_revisi }}
                                @endif
                            </div>
                        </div>

                        <div style="display:flex;gap:.45rem;flex-shrink:0;">
                            <a href="{{ route('reviewer.revisi.download', $rev->id) }}"
                               class="btn-kep btn-primary"
                               style="font-size:.8rem;padding:.42rem .7rem;">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- KOLOM KANAN --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- INFO TUGAS --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-info-circle"></i> Informasi Tugas
            </div>

            <div class="detail-row">
                <span class="detail-key">Tahap Review</span>
                <span class="detail-value">
                    <span class="kep-badge {{ $isRevisionReview ? 'badge-revision' : 'badge-review' }}">
                        {{ $isRevisionReview ? 'Review Revisi ke-' . $round : 'Review Awal' }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Status Review</span>
                <span class="detail-value">
                    @if($isDone)
                        <span class="kep-badge badge-approved">
                            {{ $isRevisionReview ? 'Keputusan Revisi Selesai' : 'Selesai' }}
                        </span>
                    @elseif($isLockedByDeadline)
                        <span class="kep-badge badge-rejected">
                            Deadline Berakhir
                        </span>
                    @else
                        <span class="kep-badge badge-review">
                            {{ $isRevisionReview ? 'Menunggu Tinjauan Revisi' : 'Pending' }}
                        </span>
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Deadline</span>
                <span class="detail-value">
                    @if($deadline)
                        <span class="{{ $isOverdue ? 'deadline-danger' : '' }}">
                            {{ $deadline->translatedFormat('d M Y') }}
                        </span>
                    @else
                        -
                    @endif
                </span>
            </div>

            <div class="detail-row" style="border-bottom:none;align-items:flex-start;">
                <span class="detail-key">Catatan Sekretariat</span>
                <span class="detail-value">
                    {{ $assignment->catatan ?? '-' }}
                </span>
            </div>
        </div>

        {{-- FORM REVIEW --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-pencil-square"></i> {{ $formTitle }}
            </div>

            @if($isLockedByDeadline)
                <div class="review-locked-box">
                    <i class="bi bi-lock-fill"></i>
                    <div>
                        <strong>Deadline review sudah berakhir.</strong>
                        <p>Anda tidak dapat mengirim keputusan atau catatan review karena batas waktu tugas ini sudah lewat.</p>
                    </div>
                </div>
            @elseif($isDone)
                <div class="review-done-box">
                    <i class="bi bi-check2-circle"></i>
                    <div>
                        <strong>{{ $doneTitle }}</strong>
                        <p>{{ $doneText }}</p>
                    </div>
                </div>

                @if($existingReview)
                    <div class="detail-row">
                        <span class="detail-key">Keputusan</span>
                        <span class="detail-value">
                            {{ $decisionLabels[$existingReview->keputusan] ?? $existingReview->keputusan }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-key">Tanggal Review</span>
                        <span class="detail-value">
                            {{ $existingReview->reviewed_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </span>
                    </div>

                    <div class="detail-row" style="border-bottom:none;align-items:flex-start;">
                        <span class="detail-key">Catatan</span>
                        <span class="detail-value" style="line-height:1.6;">
                            {{ $existingReview->catatan }}
                        </span>
                    </div>
                @endif
            @else
                <form method="POST" action="{{ route('reviewer.tugas.submit', $assignment->id) }}">
                    @csrf

                    <div class="form-group-review">
                        <label class="kep-label">Keputusan Review</label>
                        <select name="keputusan" class="kep-select" required>
                            <option value="">Pilih Keputusan</option>
                            <option value="approved" {{ old('keputusan') === 'approved' ? 'selected' : '' }}>
                                Layak
                            </option>
                            <option value="approved_with_recommendation" {{ old('keputusan') === 'approved_with_recommendation' ? 'selected' : '' }}>
                                Layak dengan Rekomendasi
                            </option>
                            <option value="minor_revision" {{ old('keputusan') === 'minor_revision' ? 'selected' : '' }}>
                                Revisi Minor
                            </option>
                            <option value="major_revision" {{ old('keputusan') === 'major_revision' ? 'selected' : '' }}>
                                Revisi Mayor
                            </option>
                            <option value="rejected" {{ old('keputusan') === 'rejected' ? 'selected' : '' }}>
                                Tidak Layak
                            </option>
                        </select>

                        @error('keputusan')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group-review">
                        <label class="kep-label">
                            {{ $isRevisionReview ? 'Catatan Keputusan Revisi' : 'Catatan Review' }}
                        </label>
                        <textarea name="catatan"
                                  rows="7"
                                  class="kep-textarea"
                                  placeholder="{{ $isRevisionReview ? 'Tuliskan penilaian terhadap hasil revisi peneliti...' : 'Tuliskan catatan, evaluasi, atau rekomendasi hasil review...' }}"
                                  required>{{ old('catatan') }}</textarea>

                        @error('catatan')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="review-note-box">
                        <strong>Catatan:</strong> {{ $noteText }}
                    </div>

                    <button type="submit" class="btn-kep btn-primary" style="width:100%;justify-content:center;margin-top:1rem;">
                        <i class="bi bi-send"></i> {{ $submitLabel }}
                    </button>
                </form>
            @endif
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.review-detail-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
    align-items: start;
}

.detail-row {
    display: flex;
    gap: 1rem;
    padding: .8rem 0;
    border-bottom: 1px solid var(--border);
    font-size: .88rem;
}

.detail-key {
    width: 145px;
    flex-shrink: 0;
    color: var(--text-muted);
    font-weight: 600;
}

.detail-value {
    color: var(--navy-deep);
    font-weight: 500;
    min-width: 0;
    word-break: break-word;
}

.doc-review-item {
    display: flex;
    align-items: center;
    gap: .85rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .85rem;
    margin-bottom: .75rem;
    background: var(--white);
}

.doc-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.revision-highlight-card {
    border-color: #fed7aa;
    background: #fffaf5;
}

.revision-icon {
    background: #ffedd5;
    color: #c2410c;
}

.doc-name {
    font-weight: 600;
    color: var(--navy-deep);
    line-height: 1.35;
}

.doc-sub {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--text-muted);
}

.form-group-review {
    margin-bottom: 1rem;
}

.kep-textarea {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .75rem .85rem;
    font-family: inherit;
    font-size: .9rem;
    resize: vertical;
    outline: none;
}

.kep-textarea:focus {
    border-color: var(--blue-accent);
    box-shadow: 0 0 0 3px rgba(74,127,167,.12);
}

.error-text {
    margin-top: .35rem;
    color: #dc2626;
    font-size: .78rem;
    font-weight: 500;
}

.review-note-box {
    padding: .8rem;
    border-radius: var(--radius-sm);
    background: #fefce8;
    border: 1px solid #fde68a;
    color: #854d0e;
    font-size: .8rem;
    line-height: 1.5;
}

.review-done-box {
    display: flex;
    gap: .75rem;
    padding: .9rem;
    border-radius: var(--radius-sm);
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    margin-bottom: 1rem;
    font-size: .85rem;
}

.review-done-box i {
    font-size: 1.35rem;
    flex-shrink: 0;
}

.review-done-box p {
    margin-top: .2rem;
    color: #047857;
}

.review-locked-box {
    display: flex;
    gap: .75rem;
    padding: .9rem;
    border-radius: var(--radius-sm);
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    margin-bottom: 1rem;
    font-size: .85rem;
}

.review-locked-box i {
    font-size: 1.35rem;
    flex-shrink: 0;
}

.review-locked-box p {
    margin-top: .2rem;
    color: #b91c1c;
}

.deadline-danger {
    color: #991b1b;
    font-weight: 700;
}

.empty-reviewer-state {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--text-muted);
}

.empty-reviewer-state i {
    display: block;
    font-size: 2rem;
    margin-bottom: .6rem;
    color: var(--blue-light);
}

@media (max-width: 960px) {
    .review-detail-grid {
        grid-template-columns: 1fr;
    }

    .detail-row {
        flex-direction: column;
        gap: .35rem;
    }

    .detail-key {
        width: auto;
    }

    .doc-review-item {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .doc-review-item > div:last-child {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>
@endpush