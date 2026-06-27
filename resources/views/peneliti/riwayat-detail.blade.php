@extends('layouts.peneliti')
@section('title', 'Detail Pengajuan')

@section('content')

@php
    $statusMap = [
        'new_proposal'                 => ['class' => 'badge-new',      'label' => 'New Proposal'],
        'assigned_to_secretary'         => ['class' => 'badge-review',   'label' => 'Assigned to Secretary'],
        'waiting_secretary_decision'    => ['class' => 'badge-review',   'label' => 'Waiting Secretary Decision'],
        'ready_for_reviewer_assignment' => ['class' => 'badge-review',   'label' => 'Ready for Reviewer Assignment'],
        'on_review'                    => ['class' => 'badge-review',   'label' => 'On Review'],
        'revision_required'            => ['class' => 'badge-revision', 'label' => 'Revision Required'],
        'revised'                      => ['class' => 'badge-review',   'label' => 'Revised'],
        'approved'                     => ['class' => 'badge-approved', 'label' => 'Approved'],
        'approved_with_recommendation'  => ['class' => 'badge-approved', 'label' => 'Approved with Recommendation'],
        'disapproved'                  => ['class' => 'badge-rejected', 'label' => 'Disapproved'],
        'issued'                       => ['class' => 'badge-approved', 'label' => 'Issued'],
    ];

    $badge   = $statusMap[$protocol->status] ?? ['class' => 'badge-new', 'label' => $protocol->status];
    $tanggal = $protocol->submitted_at?->translatedFormat('d M Y, H:i')
               ?? $protocol->created_at->translatedFormat('d M Y, H:i');

    $afterSubmittedStatuses = [
        'assigned_to_secretary',
        'waiting_secretary_decision',
        'ready_for_reviewer_assignment',
        'on_review',
        'revision_required',
        'revised',
        'approved',
        'approved_with_recommendation',
        'disapproved',
        'issued',
    ];

    $reviewStatuses = [
        'ready_for_reviewer_assignment',
        'on_review',
    ];
    @endphp

{{-- ══ PAGE HEADER ══════════════════════════════════════════════════ --}}
<div class="page-header" style="display:flex;align-items:flex-start;
            justify-content:space-between;flex-wrap:wrap;gap:1rem;">
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
            <h1>Detail Pengajuan</h1>
            <span class="kep-badge {{ $badge['class'] }}" style="font-size:.78rem;padding:.3rem .8rem;">
                {{ $badge['label'] }}
            </span>
        </div>

        <p>
            {{ $protocol->nomor_registrasi ?? 'Nomor belum ditetapkan' }}
            &nbsp;·&nbsp;
            Diajukan {{ $tanggal }} WIB
        </p>
    </div>

    {{-- Aksi header --}}
    <div style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;">
        @if(in_array($protocol->status, ['revision_required', 'approved_with_recommendation']) && !$protocol->sudah_kirim_revisi_menunggu_sekretaris)
            <a href="{{ route('peneliti.revisi.show', $protocol->id) }}" class="btn-kep btn-primary" style="font-size:.875rem;">
                <i class="bi bi-upload"></i> Upload Revisi
            </a>
        @elseif($protocol->sudah_kirim_revisi_menunggu_sekretaris)
            <span class="btn-kep" style="font-size:.875rem;background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;cursor:default;">
                <i class="bi bi-hourglass-split"></i> Revisi Terkirim, Menunggu Sekretariat
            </span>
        @endif

        <a href="{{ route('peneliti.riwayat') }}"
           class="btn-kep btn-outline" style="font-size:.875rem;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- ══ GRID UTAMA ═══════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;min-width:0;">

    {{-- ── KOLOM KIRI ─────────────────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;min-width:0;overflow:hidden;">

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
            <div class="confirm-row" style="border-bottom:none;align-items:flex-start;">
                <span class="confirm-key">Ringkasan Penelitian</span>
                <span class="confirm-value"
                      style="line-height:1.6;word-break:break-word;
                             overflow-wrap:break-word;min-width:0;flex:1;">
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
        <div class="kep-card detail-status-card">
            <div class="kep-card-title">
                <i class="bi bi-info-circle"></i> Status Pengajuan
            </div>

            <div class="confirm-row" style="padding:.5rem 0;border-bottom:1px solid var(--border);
                        margin-bottom:.85rem;align-items:center;">
                <span class="confirm-key" style="min-width:120px;">Status Saat Ini</span>
                <span class="kep-badge {{ $badge['class'] }}" style="font-size:.8rem;padding:.3rem .85rem;">
                    {{ $badge['label'] }}
                </span>
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

        {{-- Activity Log --}}
        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-clock-history"></i> Activity Log
            </div>

            <div class="activity-list">

                <div class="activity-entry">
                    <span class="activity-dot dot-done"></span>
                    <div>
                        <div class="activity-text">Pengajuan diterima</div>
                        <div class="activity-time">
                            {{ $protocol->submitted_at?->translatedFormat('d M Y, H:i') ?? $protocol->created_at->translatedFormat('d M Y, H:i') }} WIB
                        </div>
                    </div>
                </div>

                @if(in_array($protocol->status, $afterSubmittedStatuses))
                    <div class="activity-entry">
                        <span class="activity-dot dot-done"></span>
                        <div>
                            <div class="activity-text">Pengajuan diteruskan ke sekretariat</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'assigned_to_secretary')
                    <div class="activity-entry">
                        <span class="activity-dot dot-pending"></span>
                        <div>
                            <div class="activity-text">Menunggu verifikasi dokumen oleh sekretariat</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'waiting_secretary_decision')
                    <div class="activity-entry">
                        <span class="activity-dot dot-done"></span>
                        <div>
                            <div class="activity-text">Dokumen lengkap dan masuk kategori Exempted</div>
                        </div>
                    </div>
                    <div class="activity-entry">
                        <span class="activity-dot dot-pending"></span>
                        <div>
                            <div class="activity-text">Menunggu keputusan sekretariat</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'ready_for_reviewer_assignment')
                    <div class="activity-entry">
                        <span class="activity-dot dot-done"></span>
                        <div>
                            <div class="activity-text">Dokumen lengkap</div>
                        </div>
                    </div>
                    <div class="activity-entry">
                        <span class="activity-dot dot-pending"></span>
                        <div>
                            <div class="activity-text">Menunggu penugasan reviewer</div>
                        </div>
                    </div>
                @endif

                @if(in_array($protocol->status, $reviewStatuses))
                    <div class="activity-entry">
                        <span class="activity-dot dot-done"></span>
                        <div>
                            <div class="activity-text">Pengajuan masuk tahap review</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'on_review')
                    <div class="activity-entry">
                        <span class="activity-dot dot-pending"></span>
                        <div>
                            <div class="activity-text">Sedang direview oleh reviewer</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'revision_required')
                    <div class="activity-entry">
                        <span class="activity-dot dot-revision"></span>
                        <div>
                            <div class="activity-text" style="color:#9a3412;">Revisi diperlukan</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'revised')
                    <div class="activity-entry">
                        <span class="activity-dot dot-done"></span>
                        <div>
                            <div class="activity-text">Revisi telah dikirim</div>
                        </div>
                    </div>
                @endif

                @if(in_array($protocol->status, ['approved', 'issued']))
                    <div class="activity-entry">
                        <span class="activity-dot dot-approved"></span>
                        <div>
                            <div class="activity-text" style="color:#065f46;">Pengajuan disetujui</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'approved')
                    <div class="activity-entry">
                        <span class="activity-dot dot-pending"></span>
                        <div>
                            <div class="activity-text">SKE sedang dibuat</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'approved_with_recommendation')
                    <div class="activity-entry">
                        <span class="activity-dot dot-revision"></span>
                        <div>
                            <div class="activity-text" style="color:#9a3412;">Disetujui dengan rekomendasi - menunggu Anda mengunggah revisi</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'issued')
                    <div class="activity-entry">
                        <span class="activity-dot dot-approved"></span>
                        <div>
                            <div class="activity-text" style="color:#065f46;">Surat kelaikan etik telah diterbitkan</div>
                        </div>
                    </div>
                @endif

                @if($protocol->status === 'disapproved')
                    <div class="activity-entry">
                        <span class="activity-dot dot-rejected"></span>
                        <div>
                            <div class="activity-text" style="color:#991b1b;">Pengajuan tidak disetujui</div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
    @push('styles')
<style>
    @import url('https://fonts.bunny.net/css?family=instrument-sans:400,500,600');
    
    body {
        font-family: 'Instrument Sans', sans-serif;
    }

    @media (max-width: 900px) {
        div[style*="grid-template-columns:1fr 340px"] {
            grid-template-columns: 1fr !important;
        }
    }

    /* ... kode CSS lainnya tetap ... */
</style>
@endpush
@media (max-width: 900px) {
    div[style*="grid-template-columns:1fr 340px"] {
        grid-template-columns: 1fr !important;
    }
}

.confirm-value {
    word-break: break-word;
    overflow-wrap: break-word;
    min-width: 0;
}

.detail-status-card .confirm-row {
    flex-direction: row !important;
    align-items: center !important;
    gap: 1rem !important;
    padding: .6rem 0 !important;
}

.detail-status-card .confirm-key {
    min-width: 120px !important;
    font-size: .82rem !important;
    color: var(--text-muted) !important;
    font-weight: 500 !important;
}

.detail-status-card .confirm-value {
    font-size: .875rem !important;
    color: var(--navy-deep) !important;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: .25rem;
    padding-left: .25rem;
    border-left: 2px solid var(--blue-light);
}

.activity-entry {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    padding: .4rem 0 .4rem .5rem;
    font-size: .8rem;
}

.activity-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: .32rem;
}

.dot-done     { background: #10b981; }
.dot-revision { background: #f97316; }
.dot-approved { background: #059669; }
.dot-rejected { background: #dc3545; }
.dot-pending  { background: #d1d5db; border: 2px solid #9ca3af; }

.activity-text {
    color: var(--navy-deep);
    font-weight: 500;
    line-height: 1.35;
}

.activity-time {
    color: var(--text-muted);
    font-size: .72rem;
    margin-top: .1rem;
}
</style>
@endpush