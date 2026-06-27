@extends('layouts.admin')
@section('title', 'Perbaiki SKE')

@section('content')

@php
    $protocol = $ske->protocol;
@endphp

<div class="revision-page">

    <div class="page-header revision-page-header">
        <div>
            <div class="revision-breadcrumb">
                <a href="{{ route('admin.ethical-clearance.index') }}">
                    Ethical Clearance
                </a>
                <i class="bi bi-chevron-right"></i>
                <span>Perbaiki SKE</span>
            </div>

            <h1>Perbaiki SKE</h1>
            <p>
                Perbaiki data sumber berdasarkan catatan peneliti, lalu generate ulang SKE dan kirim kembali ke peneliti.
            </p>
        </div>

        <a href="{{ route('admin.ethical-clearance.index') }}" class="btn-admin-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert-error-custom">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('error') }}</span>
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

    <div class="revision-hero-card">
        <div class="revision-hero-icon">
            <i class="bi bi-arrow-repeat"></i>
        </div>

        <div class="revision-hero-content">
            <strong>SKE perlu diperbaiki</strong>
            <p>
                Peneliti mengembalikan SKE karena ada data yang perlu diperbaiki. 
                Ubah data sumber di form ini agar sistem dapat membuat ulang dokumen SKE secara konsisten.
            </p>
        </div>

        <div class="revision-hero-status">
            {{ $ske->statusLabel() }}
        </div>
    </div>

    <div class="ske-edit-grid">

        {{-- KIRI --}}
        <div class="ske-edit-left">

            {{-- Catatan Peneliti --}}
            <div class="revision-panel panel-orange">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-icon orange">
                            <i class="bi bi-chat-left-text"></i>
                        </div>
                        <div>
                            <h2>Catatan Peneliti</h2>
                            <p>Alasan peneliti mengembalikan SKE.</p>
                        </div>
                    </div>
                </div>

                @if($ske->catatan_revisi)
                    <div class="revision-note-box">
                        {{ $ske->catatan_revisi }}
                    </div>
                @else
                    <div class="empty-note">
                        <i class="bi bi-info-circle"></i>
                        Tidak ada catatan revisi dari peneliti.
                    </div>
                @endif
            </div>

            {{-- Form Perbaikan --}}
            <div class="revision-panel panel-blue">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-icon blue">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div>
                            <h2>Form Perbaikan Data SKE</h2>
                            <p>Data berikut akan dipakai untuk generate ulang dokumen SKE.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.ethical-clearance.revisi.update', $ske->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-file-earmark-text"></i>
                            Data Surat
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="kep-label">Nomor Surat</label>
                                <input type="text"
                                       name="nomor_surat"
                                       class="kep-input"
                                       value="{{ old('nomor_surat', $ske->nomor_surat) }}"
                                       required>
                            </div>

                            <div class="form-group">
                                <label class="kep-label">Tanggal Terbit</label>
                                <input type="date"
                                       name="tanggal_terbit"
                                       class="kep-input"
                                       value="{{ old('tanggal_terbit', $ske->tanggal_terbit?->format('Y-m-d')) }}"
                                       required>
                            </div>

                            <div class="form-group form-full">
                                <label class="kep-label">Ketua Penandatangan</label>
                                <select name="ketua_id" class="kep-select" required>
                                    <option value="">Pilih Ketua</option>
                                    @foreach($ketuaList as $ketua)
                                        <option value="{{ $ketua->id }}"
                                            {{ old('ketua_id', $ske->ketua_id) == $ketua->id ? 'selected' : '' }}>
                                            {{ $ketua->name }} — NIP: {{ $ketua->nip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-journal-text"></i>
                            Data Protokol Penelitian
                        </div>

                        <div class="form-grid">
                            <div class="form-group form-full">
                                <label class="kep-label">Judul Penelitian</label>
                                <input type="text"
                                       name="title"
                                       class="kep-input"
                                       value="{{ old('title', $protocol->title) }}"
                                       required>
                            </div>

                            <div class="form-group">
                                <label class="kep-label">Program Studi</label>
                                <input type="text"
                                       name="program_studi"
                                       class="kep-input"
                                       value="{{ old('program_studi', $protocol->program_studi) }}">
                            </div>

                            <div class="form-group">
                                <label class="kep-label">Sumber Pendanaan</label>
                                <input type="text"
                                       name="sumber_pendanaan"
                                       class="kep-input"
                                       value="{{ old('sumber_pendanaan', $protocol->sumber_pendanaan) }}">
                            </div>

                            <div class="form-group">
                                <label class="kep-label">Durasi Penelitian</label>
                                <input type="number"
                                       name="durasi_penelitian"
                                       class="kep-input"
                                       value="{{ old('durasi_penelitian', $protocol->durasi_penelitian) }}"
                                       min="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.ethical-clearance.index') }}" class="btn-cancel-revision">
                            <i class="bi bi-x-circle"></i>
                            Batal
                        </a>

                        <button type="submit"
                                class="btn-submit-revision"
                                onclick="return confirm('Generate ulang SKE dan kirim kembali ke peneliti untuk konfirmasi?')">
                            <i class="bi bi-arrow-repeat"></i>
                            Generate Ulang & Kirim ke Peneliti
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- KANAN --}}
        <div class="ske-edit-right">

            <div class="revision-panel panel-slate">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-icon slate">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div>
                            <h2>Informasi SKE</h2>
                            <p>Ringkasan surat yang sedang diperbaiki.</p>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <span>Status</span>
                    <strong>
                        <span class="status-pill">
                            {{ $ske->statusLabel() }}
                        </span>
                    </strong>
                </div>

                <div class="info-row">
                    <span>Nomor Surat</span>
                    <strong>{{ $ske->nomor_surat ?? '—' }}</strong>
                </div>

                <div class="info-row">
                    <span>Peneliti</span>
                    <strong>{{ $protocol->user->name ?? '—' }}</strong>
                </div>

                <div class="info-row">
                    <span>Ketua</span>
                    <strong>{{ $ske->ketua->name ?? '—' }}</strong>
                </div>

                <div class="info-row">
                    <span>Diminta Revisi</span>
                    <strong>{{ $ske->direvisi_at?->translatedFormat('d M Y, H:i') ?? '—' }}</strong>
                </div>

                <div class="side-actions">
                    @if($ske->file_path)
                        <a href="{{ route('admin.ethical-clearance.preview', $ske->id) }}"
                           target="_blank"
                           class="btn-preview-ske">
                            <i class="bi bi-eye"></i>
                            Preview SKE Saat Ini
                        </a>
                    @endif
                </div>
            </div>

            <div class="warning-card">
                <div class="warning-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <strong>Jangan edit file manual</strong>
                    <p>
                        Perbaiki data di form, lalu sistem akan generate ulang SKE.
                        Setelah itu status akan kembali ke peneliti untuk konfirmasi ulang.
                    </p>
                </div>
            </div>

            <div class="flow-card">
                <div class="flow-title">
                    <i class="bi bi-diagram-3"></i>
                    Alur Setelah Diperbaiki
                </div>

                <div class="flow-step active">
                    <span>1</span>
                    <p>Admin perbaiki data</p>
                </div>

                <div class="flow-step">
                    <span>2</span>
                    <p>SKE dikirim ulang ke peneliti</p>
                </div>

                <div class="flow-step">
                    <span>3</span>
                    <p>Peneliti menyetujui / menolak</p>
                </div>

                <div class="flow-step">
                    <span>4</span>
                    <p>Jika setuju, diteruskan ke ketua</p>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
.revision-page,
.revision-page * {
    font-family: inherit;
}

.revision-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.revision-breadcrumb {
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .8rem;
    color: var(--text-muted);
    margin-bottom: .5rem;
}

.revision-breadcrumb a {
    color: var(--blue-accent);
    font-weight: 700;
    text-decoration: none;
}

.revision-breadcrumb i {
    font-size: .65rem;
}

/* BUTTONS */
.btn-admin-secondary,
.btn-cancel-revision,
.btn-submit-revision,
.btn-preview-ske {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    border-radius: 12px;
    padding: .68rem 1rem;
    font-size: .86rem;
    font-weight: 700;
    line-height: 1;
    text-decoration: none;
    cursor: pointer;
    transition: all .2s ease;
    border: 1px solid transparent;
    white-space: nowrap;
}

.btn-admin-secondary,
.btn-cancel-revision {
    background: var(--white);
    color: var(--text-muted);
    border-color: var(--border);
}

.btn-admin-secondary:hover,
.btn-cancel-revision:hover {
    background: #f8fafc;
    color: var(--navy-deep);
    border-color: #cbd5e1;
    transform: translateY(-1px);
}

.btn-submit-revision {
    background: var(--blue-accent);
    color: #1d4ed8;;
    border-color: var(--blue-accent);
    box-shadow: 0 8px 18px rgba(37, 99, 235, .20);
}

.btn-submit-revision:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 12px 24px rgba(37, 99, 235, .26);
}

.btn-preview-ske {
    width: 100%;
    background: var(--blue-pale);
    color: var(--blue-accent);
    border-color: #bfdbfe;
}

.btn-preview-ske:hover {
    background: #dbeafe;
    color: #1d4ed8;
    border-color: #93c5fd;
    transform: translateY(-1px);
}

.alert-error-custom {
    display: flex;
    align-items: flex-start;
    gap: .7rem;
    padding: .9rem 1rem;
    border-radius: 14px;
    margin-bottom: 1rem;
    font-size: .86rem;
    font-weight: 600;
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
}

.revision-hero-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.15rem;
    margin-bottom: 1.35rem;
    border-radius: 18px;
    background: linear-gradient(135deg, #fff7ed 0%, #eff6ff 100%);
    border: 1px solid #fed7aa;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
}

.revision-hero-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: #ffedd5;
    color: #c2410c;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    flex-shrink: 0;
}

.revision-hero-content {
    flex: 1;
    min-width: 0;
}

.revision-hero-content strong {
    display: block;
    color: var(--navy-deep);
    font-size: 1rem;
    margin-bottom: .2rem;
}

.revision-hero-content p {
    margin: 0;
    color: var(--text-muted);
    font-size: .86rem;
    line-height: 1.55;
}

.revision-hero-status {
    padding: .35rem .8rem;
    border-radius: 999px;
    background: var(--white);
    color: #9a3412;
    font-size: .78rem;
    font-weight: 800;
    white-space: nowrap;
}

.ske-edit-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 350px;
    gap: 1.5rem;
    align-items: start;
}

.ske-edit-left,
.ske-edit-right {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    min-width: 0;
}

.revision-panel {
    background: var(--white);
    border-radius: 18px;
    border: 1px solid var(--border);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    overflow: hidden;
}

.panel-orange {
    border-top: 5px solid #f97316;
}

.panel-blue {
    border-top: 5px solid var(--blue-accent);
}

.panel-slate {
    border-top: 5px solid #64748b;
}

.panel-header {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
}

.panel-title-wrap {
    display: flex;
    align-items: center;
    gap: .8rem;
}

.panel-icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.panel-icon.orange {
    background: #fff7ed;
    color: #c2410c;
}

.panel-icon.blue {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.panel-icon.slate {
    background: #f1f5f9;
    color: #475569;
}

.panel-header h2 {
    margin: 0;
    color: var(--navy-deep);
    font-size: .98rem;
    font-weight: 800;
}

.panel-header p {
    margin: .15rem 0 0;
    color: var(--text-muted);
    font-size: .8rem;
    line-height: 1.4;
}

.revision-note-box {
    margin: 1.15rem;
    padding: 1rem;
    border-radius: 14px;
    background: #fff7ed;
    color: #9a3412;
    border: 1px solid #fed7aa;
    font-size: .9rem;
    line-height: 1.65;
    font-weight: 600;
}

.empty-note {
    margin: 1.15rem;
    padding: 1rem;
    text-align: center;
    color: var(--text-muted);
    font-size: .875rem;
    background: #f8fafc;
    border-radius: 14px;
    border: 1px dashed var(--border);
}

.form-section {
    padding: 1.15rem;
    border-bottom: 1px solid var(--border);
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section-title {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .35rem .7rem;
    border-radius: 999px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    font-size: .78rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: .35rem;
}

.form-full {
    grid-column: 1 / -1;
}

.kep-input,
.kep-select {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: .72rem .85rem;
    font-size: .875rem;
    color: var(--navy-deep);
    outline: none;
    background: var(--white);
    transition: all .2s ease;
}

.kep-input:focus,
.kep-select:focus {
    border-color: var(--blue-accent);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    padding: 1.15rem;
    background: #f8fafc;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}

.info-row {
    display: grid;
    grid-template-columns: 120px minmax(0, 1fr);
    gap: .8rem;
    padding: .75rem 1.15rem;
    border-bottom: 1px solid var(--border);
    font-size: .85rem;
}

.info-row span {
    color: var(--text-muted);
    font-weight: 600;
}

.info-row strong {
    color: var(--navy-deep);
    line-height: 1.45;
    word-break: break-word;
}

.status-pill {
    display: inline-flex;
    padding: .28rem .7rem;
    border-radius: 999px;
    background: #fff7ed;
    color: #9a3412;
    border: 1px solid #fed7aa;
    font-size: .75rem;
    font-weight: 800;
}

.side-actions {
    padding: 1.15rem;
}

.warning-card {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    padding: 1rem;
    border-radius: 18px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    box-shadow: 0 10px 24px rgba(245, 158, 11, .08);
}

.warning-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: #fef3c7;
    color: #b45309;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.warning-card strong {
    color: #92400e;
    display: block;
    margin-bottom: .25rem;
}

.warning-card p {
    margin: 0;
    color: #92400e;
    font-size: .84rem;
    line-height: 1.5;
}

.flow-card {
    padding: 1rem;
    border-radius: 18px;
    background: var(--white);
    border: 1px solid var(--border);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
}

.flow-title {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: var(--navy-deep);
    font-size: .9rem;
    font-weight: 800;
    margin-bottom: .9rem;
}

.flow-step {
    display: flex;
    align-items: center;
    gap: .7rem;
    padding: .55rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.flow-step:last-child {
    border-bottom: none;
}

.flow-step span {
    width: 26px;
    height: 26px;
    border-radius: 999px;
    background: #e2e8f0;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    font-weight: 800;
    flex-shrink: 0;
}

.flow-step.active span {
    background: var(--blue-accent);
    color: #fff;
}

.flow-step p {
    margin: 0;
    color: var(--text-muted);
    font-size: .82rem;
    font-weight: 600;
    line-height: 1.35;
}

@media (max-width: 950px) {
    .ske-edit-grid {
        grid-template-columns: 1fr;
    }

    .ske-edit-right {
        order: -1;
    }
}

@media (max-width: 680px) {
    .revision-hero-card {
        align-items: flex-start;
        flex-direction: column;
    }

    .revision-hero-status {
        align-self: flex-start;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .info-row {
        grid-template-columns: 1fr;
        gap: .25rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-cancel-revision,
    .btn-submit-revision,
    .btn-preview-ske,
    .btn-admin-secondary {
        width: 100%;
    }
}
</style>
@endpush