@extends('layouts.ketua')
@section('title', 'Detail SKE')

@section('content')

<div class="page-header">
    <h1>Detail SKE</h1>
    <p>Periksa dokumen SKE sebelum mengunggah file yang sudah ditandatangani.</p>
</div>

<div class="ketua-detail-page">

    <div class="detail-grid">

        {{-- LEFT --}}
        <div class="left-stack">

            <div class="ketua-panel">
                <div class="panel-header">
                    <div>
                        <h2>Informasi SKE</h2>
                        <p>Data surat dan proposal penelitian.</p>
                    </div>

                    <span class="status-pill {{ $ske->status === 'menunggu_ttd' ? 'purple' : 'green' }}">
                        {{ $ske->statusLabel() }}
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span>Nomor Surat</span>
                        <strong>{{ $ske->nomor_surat }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Peneliti</span>
                        <strong>{{ $ske->protocol->user->name ?? '-' }}</strong>
                    </div>

                    <div class="info-item info-full">
                        <span>Judul Penelitian</span>
                        <strong>{{ $ske->protocol->title ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Program Studi</span>
                        <strong>{{ $ske->protocol->program_studi ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Dikirim ke Ketua</span>
                        <strong>{{ $ske->dikirim_ke_ketua_at?->format('d M Y, H:i') ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Ditandatangani</span>
                        <strong>{{ $ske->ditandatangani_at?->format('d M Y, H:i') ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <div class="ketua-panel">
                <div class="panel-header">
                    <div>
                        <h2>Preview Dokumen SKE</h2>
                        <p>Dokumen ini adalah file SKE yang perlu ditandatangani.</p>
                    </div>

                    @if($ske->file_path)
                        <a href="{{ route('ketua.ske.preview', $ske->id) }}" target="_blank" class="btn-outline-purple">
                            <i class="bi bi-box-arrow-up-right"></i>
                            Buka Tab Baru
                        </a>
                    @endif
                </div>

                @if($ske->file_path)
                    <div class="pdf-frame-wrap">
                        <iframe src="{{ route('ketua.ske.preview', $ske->id) }}" class="pdf-frame"></iframe>
                    </div>
                @else
                    <div class="empty-state-ketua">
                        <i class="bi bi-file-earmark-x"></i>
                        <p>Dokumen SKE tidak ditemukan.</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="right-stack">

            @if($ske->status === 'menunggu_ttd')
                <div class="ketua-panel action-panel">
                    <div class="action-icon">
                        <i class="bi bi-upload"></i>
                    </div>

                    <h2>Upload SKE Bertanda Tangan</h2>
                    <p>
                        Unggah file PDF SKE yang sudah ditandatangani. Setelah diunggah, admin akan menerbitkan SKE final ke peneliti.
                    </p>

                    <form method="POST"
                          action="{{ route('ketua.ske.upload', $ske->id) }}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="upload-box">
                            <label for="signed_file">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Pilih file PDF bertanda tangan</span>
                            </label>

                            <input type="file"
                                   id="signed_file"
                                   name="signed_file"
                                   accept="application/pdf"
                                   required>
                        </div>

                        @error('signed_file')
                            <div class="error-text">{{ $message }}</div>
                        @enderror

                        <button type="submit"
                                class="btn-purple-full"
                                onclick="return confirm('Upload SKE bertanda tangan dan ubah status menjadi Sudah TTD?')">
                            <i class="bi bi-check-circle"></i>
                            Upload & Tandai Sudah TTD
                        </button>
                    </form>
                </div>
            @else
                <div class="ketua-panel action-panel">
                    <div class="action-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <h2>SKE Sudah Ditandatangani</h2>
                    <p>
                        Dokumen ini sudah Anda tandatangani dan sudah masuk ke alur penerbitan admin.
                    </p>

                    @if($ske->signed_file_path)
                        <a href="{{ route('ketua.riwayat.download', $ske->id) }}" class="btn-purple-full">
                            <i class="bi bi-download"></i>
                            Download File TTD
                        </a>
                    @endif
                </div>
            @endif

            <div class="note-card">
                <div class="note-title">
                    <i class="bi bi-exclamation-triangle"></i>
                    Catatan
                </div>
                <p>
                    Pastikan file yang diunggah adalah PDF final yang sudah ditandatangani.
                    File ini akan digunakan admin untuk menerbitkan SKE kepada peneliti.
                </p>
            </div>

            <div class="timeline-card">
                <div class="timeline-title">
                    <i class="bi bi-clock-history"></i>
                    Status Proses
                </div>

                <div class="timeline-item done">
                    <span></span>
                    <p>SKE dikirim ke ketua</p>
                </div>

                <div class="timeline-item {{ $ske->ditandatangani_at ? 'done' : 'active' }}">
                    <span></span>
                    <p>Ketua menandatangani</p>
                </div>

                <div class="timeline-item {{ $ske->status === 'terbit' ? 'done' : '' }}">
                    <span></span>
                    <p>Admin menerbitkan ke peneliti</p>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection

@push('styles')
<style>
.ketua-detail-page,
.ketua-detail-page * {
    font-family: inherit;
}

.detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 350px;
    gap: 1.25rem;
    align-items: start;
}

.left-stack,
.right-stack {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    min-width: 0;
}

.ketua-panel,
.note-card,
.timeline-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    overflow: hidden;
}

.panel-header {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .8rem;
}

.panel-header h2 {
    margin: 0;
    color: var(--navy-deep);
    font-size: 1rem;
    font-weight: 900;
}

.panel-header p {
    margin: .2rem 0 0;
    color: var(--text-muted);
    font-size: .8rem;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: .28rem .7rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 900;
    white-space: nowrap;
}

.status-pill.purple {
    background: #ede9fe;
    color: #6d28d9;
}

.status-pill.green {
    background: #ecfdf5;
    color: #047857;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}

.info-item {
    padding: .9rem 1.15rem;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:nth-child(odd) {
    border-right: 1px solid #f1f5f9;
}

.info-full {
    grid-column: 1 / -1;
    border-right: none !important;
}

.info-item span {
    display: block;
    color: var(--text-muted);
    font-size: .75rem;
    font-weight: 800;
    margin-bottom: .25rem;
}

.info-item strong {
    display: block;
    color: var(--navy-deep);
    font-size: .88rem;
    font-weight: 900;
    line-height: 1.45;
}

.btn-outline-purple,
.btn-purple-full {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    border-radius: 12px;
    font-size: .82rem;
    font-weight: 900;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .2s ease;
}

.btn-outline-purple {
    padding: .5rem .75rem;
    background: #fff;
    color: #6d28d9;
    border-color: #c4b5fd;
}

.btn-outline-purple:hover {
    background: #f5f3ff;
    color: #5b21b6;
}

.btn-purple-full {
    width: 100%;
    padding: .7rem 1rem;
    background: #7c3aed;
    color: #fff;
    border-color: #7c3aed;
}

.btn-purple-full:hover {
    background: #6d28d9;
    color: #fff;
}

.pdf-frame-wrap {
    height: 620px;
    background: #f8fafc;
}

.pdf-frame {
    width: 100%;
    height: 100%;
    border: none;
}

.action-panel {
    padding: 1rem;
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: #ede9fe;
    color: #7c3aed;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    margin-bottom: .8rem;
}

.action-icon.green {
    background: #ecfdf5;
    color: #047857;
}

.action-panel h2 {
    margin: 0;
    color: var(--navy-deep);
    font-size: 1rem;
    font-weight: 900;
}

.action-panel p {
    margin: .35rem 0 1rem;
    color: var(--text-muted);
    font-size: .82rem;
    line-height: 1.55;
}

.upload-box {
    margin-bottom: .8rem;
}

.upload-box label {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .9rem;
    border-radius: 14px;
    border: 1px dashed #c4b5fd;
    background: #f5f3ff;
    color: #5b21b6;
    font-size: .84rem;
    font-weight: 900;
    margin-bottom: .65rem;
}

.upload-box label i {
    font-size: 1.25rem;
}

.upload-box input {
    width: 100%;
    font-size: .82rem;
}

.error-text {
    color: #be123c;
    font-size: .78rem;
    font-weight: 700;
    margin-bottom: .8rem;
}

.note-card,
.timeline-card {
    padding: 1rem;
}

.note-title,
.timeline-title {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: var(--navy-deep);
    font-size: .9rem;
    font-weight: 900;
    margin-bottom: .55rem;
}

.note-card p {
    margin: 0;
    color: var(--text-muted);
    font-size: .8rem;
    line-height: 1.55;
}

.timeline-item {
    display: flex;
    gap: .65rem;
    padding: .55rem 0;
    color: var(--text-muted);
    font-size: .8rem;
    font-weight: 700;
}

.timeline-item span {
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: #cbd5e1;
    margin-top: .2rem;
    flex-shrink: 0;
}

.timeline-item.done span {
    background: #7c3aed;
}

.timeline-item.active span {
    background: #f97316;
}

.timeline-item p {
    margin: 0;
}

.empty-state-ketua {
    padding: 2.8rem 1rem;
    text-align: center;
    color: var(--text-muted);
}

.empty-state-ketua i {
    display: block;
    font-size: 2.2rem;
    color: #cbd5e1;
    margin-bottom: .55rem;
}

.empty-state-ketua p {
    margin: 0;
    font-size: .86rem;
    font-weight: 700;
}

@media (max-width: 1000px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }

    .right-stack {
        order: -1;
    }
}

@media (max-width: 680px) {
    .panel-header {
        flex-direction: column;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .info-item {
        border-right: none !important;
    }

    .pdf-frame-wrap {
        height: 480px;
    }
}
</style>
@endpush