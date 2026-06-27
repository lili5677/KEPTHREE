@extends('layouts.peneliti')
@section('title', 'Upload Revisi')

@section('content')

@php
    $decisionLabels = [
        'approved' => 'Layak',
        'approved_with_recommendation' => 'Layak dengan Rekomendasi',
        'minor_revision' => 'Revisi Minor',
        'major_revision' => 'Revisi Mayor',
        'rejected' => 'Tidak Layak',
    ];
@endphp

{{-- ══ PAGE HEADER ══════════════════════════════════════════════════ --}}
<div class="page-header" style="display:flex;align-items:flex-start;
            justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <div style="display:flex;align-items:center;gap:.5rem;
                    font-size:.8rem;color:var(--text-muted);margin-bottom:.5rem;">
            <a href="{{ route('peneliti.riwayat') }}"
               style="color:var(--blue-accent);font-weight:500;text-decoration:none;">
                Riwayat Pengajuan
            </a>
            <i class="bi bi-chevron-right" style="font-size:.65rem;"></i>
            <a href="{{ route('peneliti.riwayat.show', $protocol->id) }}"
               style="color:var(--blue-accent);font-weight:500;text-decoration:none;">
                Detail
            </a>
            <i class="bi bi-chevron-right" style="font-size:.65rem;"></i>
            <span>Upload Revisi</span>
        </div>

        <h1>{{ $isVerifikasiAwal ? 'Lengkapi Dokumen Pengajuan' : 'Upload Revisi' }}</h1>
        <p>{{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }} &nbsp;·&nbsp; {{ $protocol->title }}</p>
    </div>

    <a href="{{ route('peneliti.riwayat.show', $protocol->id) }}" class="btn-kep btn-outline" style="font-size:.875rem;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if(session('error'))
    <div class="alert-revisi alert-revisi-error">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="revisi-grid">

    {{-- ── KOLOM KIRI: Catatan yang harus direspons ─────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;min-width:0;">

        {{-- Skenario 1: Catatan dari Verifikasi Awal (dokumen tidak lengkap) --}}
        @if($isVerifikasiAwal)
            <div class="kep-card">
                <div class="kep-card-title">
                    <i class="bi bi-clipboard-check"></i> Catatan Sekretariat (Dokumen Tidak Lengkap)
                </div>
                <p class="feedback-text" style="margin:0;">
                    {{ $verification?->notes ?: 'Sekretariat menyatakan dokumen pengajuan Anda belum lengkap. Tidak ada catatan tambahan.' }}
                </p>
            </div>
        @endif

        {{-- Skenario 2: Catatan Reviewer Feedback --}}
        @if(!$isVerifikasiAwal)
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-chat-square-text"></i> Catatan Reviewer Feedback
            </div>

            @forelse($reviewerFeedback as $review)
                <div class="feedback-item">
                    <div class="feedback-head">
                        <span class="feedback-reviewer">{{ $review->reviewer->name ?? 'Reviewer' }}</span>
                        <span class="kep-badge
                            @if($review->keputusan === 'approved') badge-approved
                            @elseif($review->keputusan === 'approved_with_recommendation') badge-approved
                            @elseif(in_array($review->keputusan, ['minor_revision','major_revision'])) badge-revision
                            @else badge-rejected
                            @endif">
                            {{ $decisionLabels[$review->keputusan] ?? $review->keputusan }}
                        </span>
                    </div>
                    <p class="feedback-text">{{ $review->catatan }}</p>
                    <div class="feedback-time">
                        {{ $review->reviewed_at?->translatedFormat('d M Y, H:i') }} WIB
                    </div>
                </div>
            @empty
                <div class="empty-revisi-state">
                    <i class="bi bi-chat-square"></i>
                    <p>Belum ada catatan dari reviewer untuk pengajuan ini.</p>
                </div>
            @endforelse
        </div>
        @endif

        {{-- Skenario 2: Catatan Secretary Decision (Approved with Recommendation) --}}
        @if(!$isVerifikasiAwal && $sekretariatDecision)
            <div class="kep-card">
                <div class="kep-card-title">
                    <i class="bi bi-person-badge"></i> Catatan Sekretariat (Approved with Recommendation)
                </div>
                <p class="feedback-text" style="margin:0;">
                    {{ $sekretariatDecision->catatan ?: 'Sekretariat meminta perbaikan dokumen. Tidak ada catatan tambahan.' }}
                </p>
            </div>
        @endif

        {{-- Dokumen Pengajuan Asli (referensi) --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-paperclip"></i> Dokumen Pengajuan Sebelumnya
            </div>

            @forelse($protocol->documents as $doc)
                <div class="doc-confirm-item">
                    <div class="doc-file-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <div class="doc-confirm-info">
                        <div class="doc-confirm-name">{{ $doc->name }}</div>
                        <div class="doc-confirm-sub">{{ $doc->label }}</div>
                    </div>
                    <div class="doc-confirm-actions">
                        <a href="{{ route('peneliti.riwayat.download', $doc->id) }}" class="btn-preview-doc">
                            <i class="bi bi-download"></i> Unduh
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-muted" style="font-size:.85rem;">Tidak ada dokumen sebelumnya.</p>
            @endforelse
        </div>

        {{-- Riwayat Revisi yang Pernah Diunggah --}}
        @if($riwayatRevisi->isNotEmpty())
            <div class="kep-card">
                <div class="kep-card-title">
                    <i class="bi bi-clock-history"></i> Riwayat Revisi Sebelumnya
                </div>

                @foreach($riwayatRevisi as $rev)
                    <div class="doc-confirm-item">
                        <div class="doc-file-icon">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                        </div>
                        <div class="doc-confirm-info">
                            <div class="doc-confirm-name">
                                {{ $rev->original_filename ?: 'Dokumen Revisi' }}
                            </div>
                            <div class="doc-confirm-sub">
                                {{ $rev->submitted_at?->translatedFormat('d M Y, H:i') ?? $rev->created_at->translatedFormat('d M Y, H:i') }}
                                — {{ $rev->catatan_revisi }}
                            </div>
                        </div>
                        <div class="doc-confirm-actions">
                            <a href="{{ route('peneliti.revisi.download', $rev->id) }}" class="btn-preview-doc">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- ── KOLOM KANAN: Form Upload Revisi ──────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-upload"></i> Form Upload Revisi
            </div>

            <form method="POST" action="{{ route('peneliti.revisi.store', $protocol->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group-review">
                    <label class="kep-label">Catatan / Penjelasan Perbaikan</label>
                    <textarea name="catatan_revisi"
                              rows="6"
                              class="kep-textarea"
                              placeholder="Jelaskan perbaikan apa saja yang sudah dilakukan merespons catatan reviewer/sekretariat..."
                              required>{{ old('catatan_revisi') }}</textarea>
                    @error('catatan_revisi')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group-review">
                    <label class="kep-label">File Dokumen Revisi</label>
                    <input type="file" name="file_revisi" class="kep-input-file" required>
                    <p class="text-muted" style="font-size:.78rem;margin-top:.4rem;">
                        Maksimal ukuran file 2 MB.
                    </p>
                    @error('file_revisi')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="revisi-note-box">
                    @if($isVerifikasiAwal)
                        <strong>Catatan:</strong> Setelah disubmit, dokumen Anda akan diverifikasi ulang
                        oleh Sekretariat untuk dicek kelengkapannya.
                    @else
                        <strong>Catatan:</strong> Setelah disubmit, revisi Anda akan langsung diteruskan ke
                        reviewer yang sebelumnya memberi rekomendasi/revisi untuk ditelaah ulang.
                        Status pengajuan akan berubah menjadi <strong>On Review</strong>.
                    @endif
                </div>

                <button type="submit" class="btn-kep btn-primary" style="width:100%;justify-content:center;margin-top:1rem;">
                    <i class="bi bi-send"></i> Kirim Revisi
                </button>
            </form>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.revisi-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
    align-items: start;
}

.alert-revisi {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .8rem 1rem;
    border-radius: var(--radius-sm);
    font-size: .85rem;
    margin-bottom: 1.25rem;
}

.alert-revisi-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.feedback-item {
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .9rem;
    margin-bottom: .75rem;
}

.feedback-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    margin-bottom: .5rem;
}

.feedback-reviewer {
    font-weight: 600;
    color: var(--navy-deep);
    font-size: .88rem;
}

.feedback-text {
    color: var(--navy-deep);
    font-size: .85rem;
    line-height: 1.6;
}

.feedback-time {
    margin-top: .5rem;
    font-size: .72rem;
    color: var(--text-muted);
}

.empty-revisi-state {
    text-align: center;
    padding: 1.5rem 1rem;
    color: var(--text-muted);
    font-size: .875rem;
}

.empty-revisi-state i {
    display: block;
    font-size: 1.6rem;
    margin-bottom: .5rem;
    color: var(--blue-light);
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

.kep-input-file {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .6rem .7rem;
    font-size: .85rem;
    background: var(--white);
}

.error-text {
    margin-top: .35rem;
    color: #dc2626;
    font-size: .78rem;
    font-weight: 500;
}

.revisi-note-box {
    padding: .8rem;
    border-radius: var(--radius-sm);
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    font-size: .8rem;
    line-height: 1.5;
}

@media (max-width: 960px) {
    .revisi-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush