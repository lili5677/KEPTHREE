@extends('layouts.peneliti')
@section('title', 'Download Template')

@push('styles')
<style>
/* ════════════════════════════════════════
   TEMPLATE PAGE — additional styles
   (memperluas peneliti.css)
   ════════════════════════════════════════ */

/* ── Grid kartu template ── */
.template-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

/* ── Kartu template ── */
.template-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
    transition: box-shadow var(--transition), border-color var(--transition), transform var(--transition);
    position: relative;
    overflow: hidden;
}

.template-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--blue-accent), var(--navy-mid));
    opacity: 0;
    transition: opacity var(--transition);
}

.template-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--blue-light);
    transform: translateY(-2px);
}

.template-card:hover::before {
    opacity: 1;
}

/* ── Header kartu ── */
.template-card-header {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
}

.template-file-icon {
    width: 46px;
    height: 46px;
    border-radius: 11px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
    transition: background var(--transition), color var(--transition);
}

.template-card:hover .template-file-icon {
    background: var(--blue-accent);
    color: #fff;
}

.template-header-body {
    flex: 1;
    min-width: 0;
}

.template-name {
    font-size: .95rem;
    font-weight: 600;
    color: var(--navy-deep);
    line-height: 1.35;
    margin-bottom: .3rem;
}

/* ── Badge versi ── */
.template-versi-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    padding: .15rem .55rem;
    border-radius: 20px;
    background: #dbedf7;
    color: var(--navy-mid);
}

/* ── Deskripsi ── */
.template-desc {
    font-size: .82rem;
    color: var(--text-muted);
    line-height: 1.55;

    /* Clamp ke 3 baris */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Meta info (format, diperbarui) ── */
.template-meta {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem .9rem;
    font-size: .75rem;
    color: var(--text-muted);
}

.template-meta span {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
}

.template-meta i {
    font-size: .8rem;
    color: var(--blue-accent);
}

/* ── Tombol unduh ── */
.btn-download {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .65rem 1.25rem;
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    border: 1.5px solid var(--blue-accent);
    background: var(--blue-accent);
    color: #fff;
    text-decoration: none;
    transition: all var(--transition);
    width: 100%;
    margin-top: auto;
    box-shadow: 0 2px 6px rgba(74,127,167,.25);
}

.btn-download:hover {
    background: var(--navy-mid);
    border-color: var(--navy-mid);
    color: #fff;
    box-shadow: 0 4px 12px rgba(26,61,99,.28);
    transform: translateY(-1px);
}

.btn-download:disabled,
.btn-download[disabled] {
    opacity: .45;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
    background: var(--border);
    border-color: var(--border);
    color: var(--text-muted);
}

/* ── Divider di footer kartu ── */
.template-card-footer {
    padding-top: .85rem;
    border-top: 1px solid var(--border);
    margin-top: auto;
}

/* ── Panduan ── */
.usage-guide {
    background: var(--blue-pale);
    border: 1px solid var(--blue-light);
    border-radius: var(--radius);
    padding: 1.25rem 1.5rem;
}

.usage-guide h6 {
    font-size: .9rem;
    font-weight: 600;
    color: var(--navy-deep);
    margin-bottom: .75rem;
    display: flex;
    align-items: center;
    gap: .45rem;
}

.usage-guide ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: .45rem;
}

.usage-guide ul li {
    font-size: .845rem;
    color: var(--navy-mid);
    line-height: 1.5;
    padding-left: 1.3rem;
    position: relative;
}

.usage-guide ul li::before {
    content: '›';
    position: absolute;
    left: 0;
    color: var(--blue-accent);
    font-weight: 700;
    font-size: 1rem;
    line-height: 1.3;
}

/* ── Empty state (tidak ada template) ── */
.template-empty {
    text-align: center;
    padding: 3.5rem 2rem;
    color: var(--text-muted);
}

.template-empty .empty-icon {
    font-size: 2.8rem;
    color: var(--blue-light);
    margin-bottom: 1rem;
    display: block;
}

.template-empty p {
    font-size: .9rem;
    max-width: 360px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ── Responsive ── */
@media (max-width: 640px) {
    .template-grid {
        grid-template-columns: 1fr;
    }

    .template-card {
        padding: 1.25rem;
    }
}
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="page-header">
    <h1>Download Template</h1>
    <p>Unduh dokumen template yang diperlukan untuk pengajuan ethical clearance</p>
</div>

{{-- ── Info Banner ── --}}
<div class="kep-alert info mb-4">
    <i class="bi bi-info-circle-fill"></i>
    <div>
        <strong>Informasi Penting:</strong>
        Template yang tersedia adalah versi terbaru sesuai standar Komite Etik Penelitian.
        Pastikan Anda selalu menggunakan template ini — hindari menggunakan versi lama.
    </div>
</div>

{{-- ── Daftar Template ── --}}
<div class="kep-card">
    <div class="kep-card-title">
        <i class="bi bi-files"></i> Daftar Template Dokumen
        @if($templates->isNotEmpty())
            <span class="ms-auto text-sm text-muted" style="margin-left:auto;font-weight:400;">
                {{ $templates->count() }} template tersedia
            </span>
        @endif
    </div>

    @if($templates->isEmpty())
        {{-- Empty state ── --}}
        <div class="template-empty">
            <i class="bi bi-folder2-open empty-icon"></i>
            <p>
                Belum ada template yang tersedia saat ini.<br>
                Silakan hubungi administrator untuk informasi lebih lanjut.
            </p>
        </div>
    @else
        <div class="template-grid">
            @foreach($templates as $tpl)
                <div class="template-card">

                    {{-- Header: ikon + nama + versi --}}
                    <div class="template-card-header">
                        <div class="template-file-icon">
                            <i class="bi bi-file-earmark-word"></i>
                        </div>
                        <div class="template-header-body">
                            <div class="template-name">{{ $tpl->name }}</div>
                            @if($tpl->versi)
                                <span class="template-versi-badge">
                                    <i class="bi bi-tag-fill" style="font-size:.6rem;"></i>
                                    v{{ $tpl->versi }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    @if($tpl->description)
                        <p class="template-desc">{{ $tpl->description }}</p>
                    @endif

                    {{-- Meta: format & tanggal --}}
                    <div class="template-meta">
                        <span>
                            <i class="bi bi-file-earmark"></i>
                            DOCX
                        </span>
                        <span>
                            <i class="bi bi-clock"></i>
                            Diperbarui: {{ $tpl->updated_at->translatedFormat('d M Y') }}
                        </span>
                        @if($tpl->uploader)
                            <span>
                                <i class="bi bi-person"></i>
                                {{ $tpl->uploader->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Tombol unduh --}}
                    <div class="template-card-footer">
                        @if(Storage::disk('public')->exists($tpl->file_path))
                            <a href="{{ route('peneliti.template.download', $tpl) }}"
                               class="btn-download">
                                <i class="bi bi-download"></i> Unduh Template
                            </a>
                        @else
                            <button class="btn-download" disabled
                                    title="File belum tersedia di server">
                                <i class="bi bi-exclamation-circle"></i> File Tidak Tersedia
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Panduan Penggunaan ── --}}
    <div class="usage-guide mt-4">
        <h6><i class="bi bi-lightbulb" style="color:var(--blue-accent);"></i> Panduan Penggunaan Template</h6>
        <ul>
            <li>Unduh template yang sesuai dengan jenis pengajuan Anda.</li>
            <li>Isi semua bagian yang ditandai tanda bintang (<strong>*</strong>) karena bersifat wajib.</li>
            <li>Pastikan informasi yang diisi akurat dan sesuai dengan protokol penelitian Anda.</li>
            <li>Simpan file dalam format yang sama (<strong>DOCX</strong>) saat mengunggah ke sistem.</li>
            <li>Maksimal ukuran file yang dapat diunggah adalah <strong>2 MB per dokumen</strong>.</li>
        </ul>
    </div>
</div>

@endsection