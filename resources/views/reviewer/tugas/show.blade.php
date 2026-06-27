@extends('layouts.reviewer')
@section('title', 'Detail Tugas Review')

@section('content')

@php
    $protocol = $assignment->protocol;

    $deadline = $assignment->deadline
        ? \Carbon\Carbon::parse($assignment->deadline)
        : null;

    $isDone = $assignment->status === 'done';

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
        <h1>Detail Tugas Review</h1>
        <p>Periksa dokumen, berikan catatan, lalu submit hasil review Anda</p>
    </div>

    <a href="{{ route('reviewer.tugas.index') }}" class="btn-kep btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
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
                <span class="detail-value">{{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Judul Penelitian</span>
                <span class="detail-value">{{ $protocol->title ?? '-' }}</span>
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
                <span class="detail-value">{{ $protocol->program_studi ?? '-' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Sumber Pendanaan</span>
                <span class="detail-value">{{ $protocol->sumber_pendanaan ?? '-' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Durasi Penelitian</span>
                <span class="detail-value">{{ $protocol->durasi_penelitian ?? '-' }} bulan</span>
            </div>

            <div class="detail-row" style="border-bottom:none;align-items:flex-start;">
                <span class="detail-key">Ringkasan Penelitian</span>
                <span class="detail-value" style="line-height:1.65;">
                    {{ $protocol->ringkasan_penelitian ?? '-' }}
                </span>
            </div>
        </div>

        {{-- DOKUMEN --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-paperclip"></i> Dokumen Pengajuan
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

        @if($protocol->revisions->isNotEmpty())
            <div class="kep-card">
                <div class="kep-card-title">
                    <i class="bi bi-file-earmark-arrow-up"></i> Dokumen Revisi dari Peneliti
                </div>

                @foreach($protocol->revisions as $rev)
                    <div class="doc-review-item">
                        <div class="doc-icon">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div class="doc-name">
                                {{ $rev->original_filename ?: 'Dokumen Revisi' }}
                            </div>
                            <div class="doc-sub">
                                {{ $rev->submitted_at?->translatedFormat('d M Y, H:i') ?? $rev->created_at->translatedFormat('d M Y, H:i') }}
                                — {{ $rev->catatan_revisi }}
                            </div>
                        </div>

                        <div style="display:flex;gap:.45rem;flex-shrink:0;">
                            <a href="{{ route('peneliti.revisi.download', $rev->id) }}"
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
                <span class="detail-key">Status Review</span>
                <span class="detail-value">
                    @if($isDone)
                        <span class="kep-badge badge-approved">Selesai</span>
                    @else
                        <span class="kep-badge badge-review">Pending</span>
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-key">Deadline</span>
                <span class="detail-value">
                    {{ $deadline ? $deadline->translatedFormat('d M Y') : '-' }}
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
                <i class="bi bi-pencil-square"></i> Form Hasil Review
            </div>

            @if($isDone)
                <div class="review-done-box">
                    <i class="bi bi-check2-circle"></i>
                    <div>
                        <strong>Review sudah disubmit.</strong>
                        <p>Hasil review Anda sudah masuk ke sistem.</p>
                    </div>
                </div>

                @if($existingReview)
                    <div class="detail-row">
                        <span class="detail-key">Keputusan</span>
                        <span class="detail-value">
                            {{ $decisionLabels[$existingReview->keputusan] ?? $existingReview->keputusan }}
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
                        <label class="kep-label">Catatan Review</label>
                        <textarea name="catatan"
                                  rows="7"
                                  class="kep-textarea"
                                  placeholder="Tuliskan catatan, evaluasi, atau rekomendasi hasil review..."
                                  required>{{ old('catatan') }}</textarea>

                        @error('catatan')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="review-note-box">
                        <strong>Catatan:</strong> Setelah disubmit, hasil review akan dikirim ke sekretariat dan tugas ini masuk ke Riwayat Review.
                    </div>

                    <button type="submit" class="btn-kep btn-primary" style="width:100%;justify-content:center;margin-top:1rem;">
                        <i class="bi bi-send"></i> Submit Review
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
}
</style>
@endpush