@extends('layouts.peneliti')
@section('title', 'Cek SKE')

@section('content')

@php
    $protocol = $ske->protocol;

    $statusClass = match($ske->status) {
        'menunggu_konfirmasi' => 'ske-status-blue',
        'revisi'              => 'ske-status-orange',
        'menunggu_ttd'        => 'ske-status-indigo',
        'sudah_ttd'           => 'ske-status-green',
        'terbit'              => 'ske-status-emerald',
        default               => 'ske-status-slate',
    };
@endphp

<div class="page-header ske-page-header">
    <div>
        <div class="ske-breadcrumb">
            <a href="{{ route('peneliti.riwayat') }}">Riwayat Pengajuan</a>
            <i class="bi bi-chevron-right"></i>
            <span>Cek SKE</span>
        </div>

        <h1>Cek Surat Kelayakan Etik</h1>
        <p>
            Periksa dokumen SKE untuk memastikan data sudah benar sebelum dilanjutkan ke tahap berikutnya.
        </p>
    </div>

    <a href="{{ route('peneliti.riwayat') }}" class="btn-kep btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if(session('success'))
    <div class="alert-success-custom">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert-error-custom">
        <i class="bi bi-exclamation-triangle-fill"></i>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert-error-custom">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin:.35rem 0 0;padding-left:1.2rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="ske-grid">

    {{-- KOLOM KIRI --}}
    <div class="ske-left">

        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-file-earmark-check"></i> Informasi SKE
            </div>

            <div class="ske-info-row">
                <span>Nomor Surat</span>
                <strong>{{ $ske->nomor_surat ?? '—' }}</strong>
            </div>

            <div class="ske-info-row">
                <span>Status SKE</span>
                <strong>
                    <span class="ske-status-badge {{ $statusClass }}">
                        {{ $ske->statusLabel() }}
                    </span>
                </strong>
            </div>

            <div class="ske-info-row">
                <span>Judul Penelitian</span>
                <strong>{{ $protocol->title ?? '—' }}</strong>
            </div>

            <div class="ske-info-row">
                <span>Program Studi</span>
                <strong>{{ $protocol->program_studi ?? '—' }}</strong>
            </div>

            <div class="ske-info-row">
                <span>Peneliti</span>
                <strong>{{ $protocol->user->name ?? '—' }}</strong>
            </div>

            <div class="ske-info-row">
                <span>Ketua Penandatangan</span>
                <strong>{{ $ske->ketua->name ?? '—' }}</strong>
            </div>

            <div class="ske-info-row">
                <span>Tanggal Terbit</span>
                <strong>{{ $ske->tanggal_terbit?->translatedFormat('d M Y') ?? '—' }}</strong>
            </div>
        </div>

        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-file-pdf"></i> Preview SKE
            </div>

            <div class="ske-preview-box">
                <iframe src="{{ route('peneliti.ske.preview', $ske->id) }}" class="ske-preview-frame"></iframe>
            </div>

            <div class="ske-preview-actions">
                <a href="{{ route('peneliti.ske.preview', $ske->id) }}"
                   target="_blank"
                   class="btn-kep btn-outline">
                    <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                </a>

                @if($ske->status === 'terbit')
                    <a href="{{ route('peneliti.ske.download', $ske->id) }}"
                       class="btn-kep btn-primary">
                        <i class="bi bi-download"></i> Unduh SKE
                    </a>
                @endif
            </div>
        </div>

    </div>

    {{-- KOLOM KANAN --}}
    <div class="ske-right">

        @if($ske->status === 'menunggu_konfirmasi')
            <div class="kep-card action-card">
                <div class="action-icon action-blue">
                    <i class="bi bi-shield-check"></i>
                </div>

                <h3>Konfirmasi SKE</h3>
                <p>
                    Periksa kebenaran data pada SKE. Jika seluruh informasi sudah benar,
                    setujui agar SKE diteruskan ke Ketua untuk ditandatangani.
                </p>

                <form method="POST"
                      action="{{ route('peneliti.ske.approve', $ske->id) }}"
                      onsubmit="return confirm('Apakah Anda yakin data SKE sudah benar dan ingin meneruskan ke Ketua?')">
                    @csrf
                    <button type="submit" class="btn-kep btn-primary btn-full">
                        <i class="bi bi-check-circle"></i> Setujui SKE
                    </button>
                </form>
            </div>

            <div class="kep-card action-card">
                <div class="action-icon action-orange">
                    <i class="bi bi-pencil-square"></i>
                </div>

                <h3>Minta Perbaikan</h3>
                <p>
                    Jika ada data yang salah, tuliskan catatan perbaikan dengan jelas.
                    Catatan ini akan dikirim kembali ke Admin.
                </p>

                <form method="POST"
                      action="{{ route('peneliti.ske.reject', $ske->id) }}"
                      onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan SKE ke Admin untuk diperbaiki?')">
                    @csrf

                    <div class="form-group">
                        <label class="kep-label">Catatan Perbaikan</label>
                        <textarea name="catatan_revisi"
                                  class="kep-textarea"
                                  rows="5"
                                  placeholder="Contoh: Nama peneliti masih salah, judul penelitian kurang lengkap, atau data lain perlu diperbaiki..."
                                  required>{{ old('catatan_revisi') }}</textarea>
                    </div>

                    <button type="submit" class="btn-kep btn-warning-custom btn-full">
                        <i class="bi bi-arrow-counterclockwise"></i> Tolak / Minta Perbaikan
                    </button>
                </form>
            </div>
        @elseif($ske->status === 'revisi')
            <div class="kep-card action-card">
                <div class="action-icon action-orange">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </div>

                <h3>SKE Dikembalikan ke Admin</h3>
                <p>
                    Anda sudah meminta perbaikan SKE. Saat ini SKE sedang menunggu diproses ulang oleh Admin.
                </p>

                @if($ske->catatan_revisi)
                    <div class="note-box">
                        <strong>Catatan Anda:</strong>
                        <p>{{ $ske->catatan_revisi }}</p>
                    </div>
                @endif
            </div>
        @elseif($ske->status === 'menunggu_ttd')
            <div class="kep-card action-card">
                <div class="action-icon action-indigo">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                <h3>Menunggu Tanda Tangan Ketua</h3>
                <p>
                    SKE sudah Anda setujui dan sedang menunggu Ketua untuk menandatangani dokumen.
                </p>
            </div>
        @elseif($ske->status === 'sudah_ttd')
            <div class="kep-card action-card">
                <div class="action-icon action-green">
                    <i class="bi bi-pen"></i>
                </div>

                <h3>SKE Sudah Ditandatangani</h3>
                <p>
                    SKE sudah ditandatangani Ketua. Dokumen sedang menunggu Admin untuk diterbitkan secara final.
                </p>
            </div>
        @elseif($ske->status === 'terbit')
            <div class="kep-card action-card">
                <div class="action-icon action-green">
                    <i class="bi bi-patch-check"></i>
                </div>

                <h3>SKE Telah Terbit</h3>
                <p>
                    Surat Kelayakan Etik sudah resmi diterbitkan dan dapat Anda unduh.
                </p>

                <a href="{{ route('peneliti.ske.download', $ske->id) }}"
                   class="btn-kep btn-primary btn-full">
                    <i class="bi bi-download"></i> Unduh SKE
                </a>
            </div>
        @else
            <div class="kep-card action-card">
                <div class="action-icon action-slate">
                    <i class="bi bi-info-circle"></i>
                </div>

                <h3>Status SKE</h3>
                <p>
                    Status SKE saat ini: <strong>{{ $ske->statusLabel() }}</strong>.
                </p>
            </div>
        @endif

        <div class="kep-card">
            <div class="kep-card-title">
                <i class="bi bi-clock-history"></i> Alur SKE
            </div>

            <div class="ske-timeline">

                <div class="ske-timeline-item done">
                    <span></span>
                    <div>
                        <strong>SKE dibuat Admin</strong>
                        <p>{{ $ske->dikirim_ke_peneliti_at?->translatedFormat('d M Y, H:i') ?? '—' }}</p>
                    </div>
                </div>

                <div class="ske-timeline-item {{ in_array($ske->status, ['menunggu_ttd', 'sudah_ttd', 'terbit']) ? 'done' : ($ske->status === 'menunggu_konfirmasi' ? 'current' : '') }}">
                    <span></span>
                    <div>
                        <strong>Konfirmasi Peneliti</strong>
                        <p>
                            @if($ske->status === 'menunggu_konfirmasi')
                                Menunggu konfirmasi Anda
                            @elseif($ske->status === 'revisi')
                                Dikembalikan ke Admin
                            @elseif(in_array($ske->status, ['menunggu_ttd', 'sudah_ttd', 'terbit']))
                                Sudah disetujui
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>

                <div class="ske-timeline-item {{ in_array($ske->status, ['menunggu_ttd']) ? 'current' : (in_array($ske->status, ['sudah_ttd', 'terbit']) ? 'done' : '') }}">
                    <span></span>
                    <div>
                        <strong>Tanda Tangan Ketua</strong>
                        <p>
                            @if($ske->ditandatangani_at)
                                {{ $ske->ditandatangani_at->translatedFormat('d M Y, H:i') }}
                            @elseif($ske->status === 'menunggu_ttd')
                                Sedang menunggu tanda tangan
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>

                <div class="ske-timeline-item {{ $ske->status === 'terbit' ? 'done' : '' }}">
                    <span></span>
                    <div>
                        <strong>SKE Terbit</strong>
                        <p>{{ $ske->diterbitkan_at?->translatedFormat('d M Y, H:i') ?? '—' }}</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.ske-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.ske-breadcrumb {
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .8rem;
    color: var(--text-muted);
    margin-bottom: .5rem;
}

.ske-breadcrumb a {
    color: var(--blue-accent);
    font-weight: 600;
    text-decoration: none;
}

.ske-breadcrumb i {
    font-size: .65rem;
}

.alert-success-custom,
.alert-error-custom {
    display: flex;
    align-items: flex-start;
    gap: .7rem;
    padding: .85rem 1rem;
    border-radius: 14px;
    margin-bottom: 1rem;
    font-size: .86rem;
    font-weight: 600;
}

.alert-success-custom {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}

.alert-error-custom {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
}

.ske-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 1.5rem;
    align-items: start;
}

.ske-left,
.ske-right {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    min-width: 0;
}

.ske-info-row {
    display: grid;
    grid-template-columns: 150px minmax(0, 1fr);
    gap: 1rem;
    padding: .75rem 0;
    border-bottom: 1px solid var(--border);
    font-size: .86rem;
}

.ske-info-row:last-child {
    border-bottom: none;
}

.ske-info-row span {
    color: var(--text-muted);
    font-weight: 500;
}

.ske-info-row strong {
    color: var(--navy-deep);
    line-height: 1.45;
    word-break: break-word;
}

.ske-status-badge {
    display: inline-flex;
    align-items: center;
    padding: .28rem .7rem;
    border-radius: 999px;
    font-size: .76rem;
    font-weight: 700;
}

.ske-status-blue {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.ske-status-orange {
    background: #fff7ed;
    color: #9a3412;
    border: 1px solid #fed7aa;
}

.ske-status-indigo {
    background: #eef2ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
}

.ske-status-green,
.ske-status-emerald {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}

.ske-status-slate {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.ske-preview-box {
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    background: #f8fafc;
    min-height: 580px;
}

.ske-preview-frame {
    width: 100%;
    height: 580px;
    border: none;
    display: block;
}

.ske-preview-actions {
    display: flex;
    justify-content: flex-end;
    gap: .6rem;
    margin-top: .85rem;
    flex-wrap: wrap;
}

.action-card {
    text-align: left;
}

.action-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    margin-bottom: .9rem;
}

.action-blue {
    background: #eff6ff;
    color: #1d4ed8;
}

.action-orange {
    background: #fff7ed;
    color: #c2410c;
}

.action-indigo {
    background: #eef2ff;
    color: #3730a3;
}

.action-green {
    background: #ecfdf5;
    color: #047857;
}

.action-slate {
    background: #f8fafc;
    color: #475569;
}

.action-card h3 {
    margin: 0 0 .4rem;
    color: var(--navy-deep);
    font-size: 1rem;
    font-weight: 700;
}

.action-card p {
    margin: 0 0 1rem;
    color: var(--text-muted);
    font-size: .86rem;
    line-height: 1.55;
}

.btn-full {
    width: 100%;
    justify-content: center;
}

.btn-warning-custom {
    background: #f97316;
    color: #fff;
    border: 1px solid #f97316;
}

.btn-warning-custom:hover {
    background: #ea580c;
    border-color: #ea580c;
    color: #fff;
}

.form-group {
    margin-bottom: .85rem;
}

.kep-textarea {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: .75rem .85rem;
    font-size: .86rem;
    color: var(--navy-deep);
    resize: vertical;
    outline: none;
}

.kep-textarea:focus {
    border-color: var(--blue-accent);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
}

.note-box {
    padding: .75rem .85rem;
    border-radius: 12px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    font-size: .84rem;
    line-height: 1.5;
}

.note-box p {
    margin: .35rem 0 0;
    color: #9a3412;
}

.ske-timeline {
    display: flex;
    flex-direction: column;
    gap: .85rem;
    border-left: 2px solid #e5e7eb;
    padding-left: .85rem;
}

.ske-timeline-item {
    display: flex;
    gap: .7rem;
    position: relative;
}

.ske-timeline-item > span {
    width: 11px;
    height: 11px;
    border-radius: 999px;
    background: #d1d5db;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #d1d5db;
    margin-left: -1.18rem;
    margin-top: .25rem;
    flex-shrink: 0;
}

.ske-timeline-item.done > span {
    background: #10b981;
    box-shadow: 0 0 0 2px #10b981;
}

.ske-timeline-item.current > span {
    background: #2563eb;
    box-shadow: 0 0 0 2px #2563eb;
}

.ske-timeline-item strong {
    display: block;
    color: var(--navy-deep);
    font-size: .84rem;
    line-height: 1.35;
}

.ske-timeline-item p {
    margin: .15rem 0 0;
    color: var(--text-muted);
    font-size: .75rem;
    line-height: 1.35;
}

@media (max-width: 1000px) {
    .ske-grid {
        grid-template-columns: 1fr;
    }

    .ske-right {
        order: -1;
    }
}

@media (max-width: 640px) {
    .ske-info-row {
        grid-template-columns: 1fr;
        gap: .3rem;
    }

    .ske-preview-box,
    .ske-preview-frame {
        min-height: 420px;
        height: 420px;
    }

    .ske-preview-actions {
        justify-content: stretch;
    }

    .ske-preview-actions .btn-kep {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush