@extends('layouts.peneliti')
@section('title', 'Detail Pengajuan')

@section('content')

@php
    $statusMap = [
        'new_proposal'         => ['class' => 'badge-new',      'label' => 'New Proposal'],
        'waiting_verification' => ['class' => 'badge-review',   'label' => 'Waiting Verification'],
        'under_review'         => ['class' => 'badge-review',   'label' => 'Under Review'],
        'revision_required'    => ['class' => 'badge-revision', 'label' => 'Revision Required'],
        'approved'             => ['class' => 'badge-approved', 'label' => 'Approved'],
        'rejected'             => ['class' => 'badge-rejected', 'label' => 'Rejected'],
    ];
    $badge   = $statusMap[$protocol->status] ?? ['class' => 'badge-new', 'label' => $protocol->status];
    $tanggal = $protocol->submitted_at?->translatedFormat('d M Y, H:i')
               ?? $protocol->created_at->translatedFormat('d M Y, H:i');
@endphp

{{-- ══ PAGE HEADER ══════════════════════════════════════════════════ --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;
            flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
    <div>
        {{-- Breadcrumb --}}
        <div style="display:flex;align-items:center;gap:.5rem;
                    font-size:.8rem;color:var(--text-muted);margin-bottom:.5rem;">
            <a href="{{ route('peneliti.riwayat') }}"
               style="color:var(--blue-accent);font-weight:500;text-decoration:none;">
                Riwayat Pengajuan
            </a>
            <i class="bi bi-chevron-right" style="font-size:.65rem;"></i>
            <span>Detail</span>
        </div>

        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <h1 style="font-size:2rem;font-weight:600;color:var(--navy-deep);
                       letter-spacing:-.02em;line-height:1.2;">
                Detail Pengajuan
            </h1>
            <span class="kep-badge {{ $badge['class'] }}" style="font-size:.78rem;padding:.3rem .8rem;">
                {{ $badge['label'] }}
            </span>
        </div>

        <p style="color:var(--text-muted);font-size:.9rem;margin-top:.3rem;">
            {{ $protocol->nomor_registrasi ?? 'Nomor belum ditetapkan' }}
            &nbsp;·&nbsp;
            Diajukan {{ $tanggal }} WIB
        </p>
    </div>

    {{-- Aksi header --}}
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;">
        @if($protocol->status === 'revision_required')
            <a href="#" class="btn-kep btn-primary" style="font-size:.875rem;">
                <i class="bi bi-upload"></i> Upload Revisi
            </a>
        @endif

        <a href="{{ route('peneliti.riwayat') }}"
           class="btn-kep btn-outline" style="font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- ══ GRID UTAMA ═══════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">

    {{-- ── KOLOM KIRI ─────────────────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Informasi Umum --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-file-text"></i> Informasi Umum
            </div>

            <div class="confirm-row">
                <span class="confirm-key">Judul Penelitian</span>
                <span class="confirm-value" style="font-size:.93rem;">{{ $protocol->title }}</span>
            </div>
            <div class="confirm-row">
                <span class="confirm-key">Program Studi</span>
                <span class="confirm-value">{{ $protocol->program_studi }}</span>
            </div>
            <div class="confirm-row">
                <span class="confirm-key">Sumber Pendanaan</span>
                <span class="confirm-value">{{ $protocol->sumber_pendanaan }}</span>
            </div>
            <div class="confirm-row">
                <span class="confirm-key">Durasi Penelitian</span>
                <span class="confirm-value">{{ $protocol->durasi_penelitian }} bulan</span>
            </div>
            <div class="confirm-row" style="border-bottom:none;">
                <span class="confirm-key">Ringkasan Penelitian</span>
                <span class="confirm-value" style="line-height:1.6;">
                    {{ $protocol->ringkasan_penelitian }}
                </span>
            </div>
        </div>

        {{-- Dokumen Pengajuan --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-paperclip"></i> Dokumen Pengajuan
            </div>

            @forelse($protocol->documents as $doc)
                <div class="doc-confirm-item">
                    <div class="doc-file-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <div class="doc-confirm-info">
                        <div class="doc-confirm-name">{{ $doc->name }}</div>
                        <div class="doc-confirm-sub">
                            <span class="{{ $doc->type === 'pendukung' ? 'doc-badge-opt' : 'doc-badge-wajib' }}">
                                {{ $doc->type === 'pendukung' ? 'Pendukung' : 'Wajib' }}
                            </span>
                            &nbsp;{{ $doc->label }}
                        </div>
                    </div>
                    <div class="doc-confirm-actions">
                        <a href="{{ route('peneliti.riwayat.download', $doc->id) }}"
                           class="btn-preview-doc">
                            <i class="bi bi-download"></i> Unduh
                        </a>
                    </div>
                </div>
            @empty
                <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.875rem;">
                    <i class="bi bi-folder2-open" style="font-size:1.6rem;margin-bottom:.5rem;
                       display:block;color:var(--blue-light);"></i>
                    Belum ada dokumen yang dilampirkan.
                </div>
            @endforelse
        </div>

    </div>

    {{-- ── KOLOM KANAN ─────────────────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Status Pengajuan --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-info-circle"></i> Status Pengajuan
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:.5rem 0;border-bottom:1px solid var(--border);margin-bottom:.85rem;">
                <span style="font-size:.82rem;color:var(--text-muted);font-weight:500;">Status saat ini</span>
                <span class="kep-badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
            </div>

            <div class="confirm-row" style="font-size:.85rem;">
                <span class="confirm-key" style="min-width:120px;">No. Registrasi</span>
                <span class="confirm-value" style="font-family:'Courier New',monospace;font-size:.82rem;">
                    {{ $protocol->nomor_registrasi ?? '—' }}
                </span>
            </div>
            <div class="confirm-row" style="font-size:.85rem;">
                <span class="confirm-key" style="min-width:120px;">Tanggal Ajuan</span>
                <span class="confirm-value">
                    {{ $protocol->submitted_at?->translatedFormat('d M Y') ?? $protocol->created_at->translatedFormat('d M Y') }}
                </span>
            </div>
            <div class="confirm-row" style="font-size:.85rem;border-bottom:none;">
                <span class="confirm-key" style="min-width:120px;">Peneliti</span>
                <span class="confirm-value">{{ $protocol->user->name ?? '—' }}</span>
            </div>
        </div>

        {{-- Activity Log — space placeholder ──────────────────────── --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-clock-history"></i> Activity Log
            </div>

            {{-- 
                ╔══════════════════════════════════════════════════════╗
                ║  PLACEHOLDER — Activity Log akan diisi di iterasi   ║
                ║  berikutnya setelah tabel activity_logs tersedia.    ║
                ╚══════════════════════════════════════════════════════╝
            --}}
            <div class="activity-placeholder">
                <div class="activity-placeholder-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <p class="activity-placeholder-text">
                    Riwayat aktivitas akan ditampilkan di sini.
                </p>
                <p class="activity-placeholder-sub">
                    Fitur ini sedang dalam pengembangan.
                </p>
            </div>

        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
@media (max-width: 900px) {
    div[style*="grid-template-columns:1fr 340px"] {
        grid-template-columns: 1fr !important;
    }
}

.activity-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.75rem 1rem;
    text-align: center;
}

.activity-placeholder-icon {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: var(--blue-pale);
    color: var(--blue-accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem;
    margin-bottom: .85rem;
    opacity: .7;
}

.activity-placeholder-text {
    font-size: .875rem;
    color: var(--text-muted);
    font-weight: 500;
    margin-bottom: .25rem;
}

.activity-placeholder-sub {
    font-size: .775rem;
    color: var(--text-muted);
    opacity: .7;
}
</style>
@endpush