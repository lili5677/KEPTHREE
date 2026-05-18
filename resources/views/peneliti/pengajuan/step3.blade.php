@extends('layouts.peneliti')
@section('title', 'Pengajuan Baru — Langkah 3')

@section('content')

<div class="page-header">
    <h1>Pengajuan Baru</h1>
    <p>Periksa kembali seluruh data sebelum mengirimkan pengajuan</p>
</div>

{{-- Step Indicator --}}
<div class="step-indicator mb-4">
    <div class="step-wrap">
        <div class="step-circle done"><i class="bi bi-check-lg"></i></div>
        <div class="step-label done">Informasi Dasar</div>
    </div>
    <div class="step-connector done"></div>
    <div class="step-wrap">
        <div class="step-circle done"><i class="bi bi-check-lg"></i></div>
        <div class="step-label done">Dokumen</div>
    </div>
    <div class="step-connector done"></div>
    <div class="step-wrap">
        <div class="step-circle active">3</div>
        <div class="step-label active">Konfirmasi</div>
    </div>
</div>

<div class="kep-card">
    <div class="kep-card-title">
        <i class="bi bi-clipboard2-check"></i>
        Ringkasan Pengajuan
    </div>

    {{-- Informasi Dasar --}}
    <div class="confirm-section">
        <div class="confirm-section-title">
            <i class="bi bi-info-circle"></i> Informasi Dasar
        </div>
        <div class="confirm-row">
            <span class="confirm-key">Judul Penelitian</span>
            <span class="confirm-value">{{ $step1['title'] }}</span>
        </div>
        <div class="confirm-row">
            <span class="confirm-key">Program Studi</span>
            <span class="confirm-value">{{ $step1['program_studi'] }}</span>
        </div>
        <div class="confirm-row">
            <span class="confirm-key">Durasi Penelitian</span>
            <span class="confirm-value">{{ $step1['durasi_penelitian'] }} bulan</span>
        </div>
        <div class="confirm-row">
            <span class="confirm-key">Sumber Pendanaan</span>
            <span class="confirm-value">{{ $step1['sumber_pendanaan'] ?: '—' }}</span>
        </div>
        <div class="confirm-row" style="border-bottom:none;">
            <span class="confirm-key">Ringkasan Penelitian</span>
            <span class="confirm-value" style="line-height:1.55;">{{ $step1['ringkasan_penelitian'] }}</span>
        </div>
    </div>

    {{-- Dokumen --}}
    <div class="confirm-section">
        <div class="confirm-section-title">
            <i class="bi bi-folder2"></i> Dokumen yang Diunggah
        </div>

        @foreach($step2 as $index => $doc)
            @php
                $ext       = strtolower($doc['extension'] ?? 'pdf');
                $isPdf     = $ext === 'pdf';
                $isDocx    = in_array($ext, ['docx','doc']);
                $iconClass = $isPdf ? 'bi-file-earmark-pdf' : ($isDocx ? 'bi-file-earmark-word' : 'bi-file-earmark');
                $iconColor = $isPdf ? '#e74c3c' : ($isDocx ? '#2980b9' : 'var(--blue-accent)');
                $iconBg    = $isPdf ? '#fee2e2' : '#dbedf7';

                $sizeText = '';
                if (!empty($doc['size'])) {
                    $b = (int)$doc['size'];
                    if ($b < 1024)      $sizeText = $b . ' B';
                    elseif ($b < 1048576) $sizeText = round($b / 1024, 1) . ' KB';
                    else                $sizeText = round($b / 1048576, 2) . ' MB';
                }
            @endphp

            <div class="doc-confirm-item">
                <div class="doc-file-icon" style="background:{{ $iconBg }};color:{{ $iconColor }};">
                    <i class="bi {{ $iconClass }}"></i>
                </div>
                <div class="doc-confirm-info">
                    <div class="doc-confirm-name">{{ $doc['name'] }}</div>
                    @if($sizeText)
                        <div class="doc-confirm-sub">{{ strtoupper($ext) }} &bull; {{ $sizeText }}</div>
                    @endif
                </div>
                <div class="doc-confirm-actions">
                    @if($doc['wajib'])
                        <span class="doc-badge-wajib">Wajib</span>
                    @else
                        <span class="doc-badge-opt">Pendukung</span>
                    @endif

                    @if($isPdf)
                        {{-- PDF: buka preview modal via server route --}}
                        <button type="button" class="btn-preview-doc"
                                onclick="openPreview({{ $index }}, '{{ addslashes($doc['name']) }}', 'pdf')">
                            <i class="bi bi-eye"></i> Pratinjau
                        </button>
                    @else
                        {{-- DOCX: tidak bisa preview di browser --}}
                        <button type="button" class="btn-preview-doc btn-preview-docx"
                                onclick="showDocxInfo('{{ addslashes($doc['name']) }}')">
                            <i class="bi bi-eye"></i> Pratinjau
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Error --}}
    @if($errors->any())
        <div class="kep-alert danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <ul style="list-style:none;margin:0;padding:0;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Konfirmasi Form --}}
    <form action="{{ route('peneliti.pengajuan.submit') }}" method="POST" id="submitForm">
        @csrf

        <label class="kep-checkbox-label" style="margin-bottom:1.5rem;">
            <input type="checkbox" name="konfirmasi" value="1" id="konfirmasiCheck">
            <span>
                Saya menyatakan bahwa seluruh informasi dan dokumen yang saya masukkan adalah
                <strong>benar dan dapat dipertanggungjawabkan</strong>.
                Saya memahami bahwa pengajuan yang telah dikirimkan akan diproses oleh Komite Etik Penelitian
                dan tidak dapat diubah tanpa persetujuan dari komite.
            </span>
        </label>

        <div class="d-flex justify-between" style="padding-top:1.25rem;border-top:1px solid var(--border);">
            <a href="{{ route('peneliti.pengajuan.step2') }}" class="btn-kep btn-outline">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            <button type="submit" class="btn-kep btn-primary" id="submitBtn" disabled>
                <i class="bi bi-send"></i> Kirim Pengajuan
            </button>
        </div>
    </form>
</div>

{{-- ═══ Preview Modal ═══ --}}
<div id="previewModal" aria-modal="true" role="dialog"
     style="display:none;position:fixed;inset:0;z-index:500;background:rgba(10,25,49,.6);
            backdrop-filter:blur(4px);padding:1.5rem;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;max-width:900px;width:100%;
                height:90vh;max-height:860px;display:flex;flex-direction:column;
                box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">

        {{-- Modal header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:1rem 1.5rem;border-bottom:1px solid var(--border);flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div id="modalFileIcon"
                     style="width:38px;height:38px;border-radius:8px;background:var(--blue-pale);
                            color:var(--blue-accent);display:flex;align-items:center;
                            justify-content:center;font-size:1.15rem;flex-shrink:0;">
                    <i class="bi bi-file-earmark-pdf"></i>
                </div>
                <div>
                    <div id="modalFileName"
                         style="font-size:.9rem;font-weight:600;color:var(--navy-deep);
                                max-width:580px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                    <div id="modalFileMeta"
                         style="font-size:.75rem;color:var(--text-muted);margin-top:.1rem;"></div>
                </div>
            </div>
            <button onclick="closePreview()"
                    style="background:none;border:none;font-size:1.25rem;color:var(--text-muted);
                           cursor:pointer;padding:.4rem;border-radius:8px;line-height:1;
                           transition:background .2s,color .2s;"
                    onmouseover="this.style.background='#f1f5f9';this.style.color='var(--navy-deep)'"
                    onmouseout="this.style.background='none';this.style.color='var(--text-muted)'">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Modal body --}}
        <div id="modalBody" style="flex:1;overflow:hidden;position:relative;background:#f8fafc;">

            {{-- Loading spinner --}}
            <div id="previewLoading"
                 style="position:absolute;inset:0;display:flex;align-items:center;
                        justify-content:center;flex-direction:column;gap:.85rem;
                        background:#f8fafc;z-index:10;">
                <div style="width:40px;height:40px;border:3px solid var(--blue-light);
                            border-top-color:var(--blue-accent);border-radius:50%;
                            animation:spin .8s linear infinite;"></div>
                <span style="font-size:.85rem;color:var(--text-muted);">Memuat pratinjau…</span>
            </div>

            {{-- PDF iframe --}}
            <iframe id="pdfViewer"
                    style="display:none;width:100%;height:100%;border:none;"
                    title="Pratinjau PDF"></iframe>

            {{-- DOCX / unsupported placeholder --}}
            <div id="docxPlaceholder"
                 style="display:none;height:100%;align-items:center;justify-content:center;
                        flex-direction:column;gap:1.25rem;color:var(--text-muted);padding:2rem;">
                <div style="width:80px;height:80px;border-radius:20px;background:#dbedf7;
                            color:var(--blue-accent);display:flex;align-items:center;
                            justify-content:center;font-size:2.2rem;">
                    <i class="bi bi-file-earmark-word"></i>
                </div>
                <div style="text-align:center;max-width:360px;">
                    <div style="font-size:1rem;font-weight:600;color:var(--navy-deep);margin-bottom:.4rem;">
                        Pratinjau tidak tersedia untuk DOCX
                    </div>
                    <div style="font-size:.84rem;line-height:1.55;">
                        File DOCX tidak dapat ditampilkan langsung di browser.
                        Dokumen Anda tetap tersimpan dan akan diproses setelah pengajuan dikirim.
                    </div>
                </div>
            </div>

            {{-- Error state --}}
            <div id="previewError"
                 style="display:none;height:100%;align-items:center;justify-content:center;
                        flex-direction:column;gap:1.25rem;color:var(--text-muted);padding:2rem;">
                <div style="width:80px;height:80px;border-radius:20px;background:#fee2e2;
                            color:#dc3545;display:flex;align-items:center;justify-content:center;font-size:2.2rem;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div style="text-align:center;max-width:360px;">
                    <div style="font-size:1rem;font-weight:600;color:var(--navy-deep);margin-bottom:.4rem;">
                        Gagal memuat pratinjau
                    </div>
                    <div style="font-size:.84rem;line-height:1.55;">
                        Terjadi kesalahan saat memuat file. Coba muat ulang halaman atau kirim pengajuan untuk menyimpan file.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }

/* Better doc confirm item layout */
.doc-confirm-info {
    flex: 1;
    min-width: 0;
}
.doc-confirm-sub {
    font-size: .75rem;
    color: var(--text-muted);
    margin-top: .15rem;
}

/* DOCX preview button muted style */
.btn-preview-docx {
    opacity: .7;
}
</style>

<script>
/* ── Preview Modal ── */
const previewBaseUrl = '{{ route("peneliti.pengajuan.preview", ["index" => "__IDX__"]) }}';

function openPreview(index, name, ext) {
    const modal     = document.getElementById('previewModal');
    const nameEl    = document.getElementById('modalFileName');
    const metaEl    = document.getElementById('modalFileMeta');
    const iconEl    = document.getElementById('modalFileIcon');
    const loader    = document.getElementById('previewLoading');
    const pdfView   = document.getElementById('pdfViewer');
    const docxPh    = document.getElementById('docxPlaceholder');
    const errorEl   = document.getElementById('previewError');

    // Reset states
    loader.style.display  = 'flex';
    pdfView.style.display = 'none';
    pdfView.src           = '';
    docxPh.style.display  = 'none';
    errorEl.style.display = 'none';

    // Populate header
    nameEl.textContent = name;
    const isPdf = (ext === 'pdf');
    iconEl.innerHTML   = isPdf
        ? '<i class="bi bi-file-earmark-pdf" style="color:#e74c3c;"></i>'
        : '<i class="bi bi-file-earmark-word" style="color:#2980b9;"></i>';
    iconEl.style.background = isPdf ? '#fee2e2' : '#dbedf7';
    metaEl.textContent = (ext === 'pdf' ? 'Dokumen PDF' : 'Dokumen DOCX');

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    if (!isPdf) {
        loader.style.display = 'none';
        docxPh.style.display = 'flex';
        return;
    }

    // Load PDF via server route — session content served as binary
    const url = previewBaseUrl.replace('__IDX__', index);

    pdfView.onload = function () {
        loader.style.display  = 'none';
        pdfView.style.display = 'block';
    };

    pdfView.onerror = function () {
        loader.style.display  = 'none';
        errorEl.style.display = 'flex';
    };

    pdfView.src = url;
}

function showDocxInfo(name) {
    const modal     = document.getElementById('previewModal');
    const nameEl    = document.getElementById('modalFileName');
    const metaEl    = document.getElementById('modalFileMeta');
    const iconEl    = document.getElementById('modalFileIcon');
    const loader    = document.getElementById('previewLoading');
    const pdfView   = document.getElementById('pdfViewer');
    const docxPh    = document.getElementById('docxPlaceholder');
    const errorEl   = document.getElementById('previewError');

    loader.style.display  = 'none';
    pdfView.style.display = 'none';
    pdfView.src           = '';
    errorEl.style.display = 'none';
    docxPh.style.display  = 'flex';

    nameEl.textContent = name;
    iconEl.innerHTML   = '<i class="bi bi-file-earmark-word" style="color:#2980b9;"></i>';
    iconEl.style.background = '#dbedf7';
    metaEl.textContent = 'Dokumen DOCX';

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    const modal   = document.getElementById('previewModal');
    const pdfView = document.getElementById('pdfViewer');

    modal.style.display = 'none';
    document.body.style.overflow = '';
    pdfView.src = '';
}

// Klik backdrop untuk tutup
document.getElementById('previewModal').addEventListener('click', function (e) {
    if (e.target === this) closePreview();
});

// ESC untuk tutup
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closePreview();
});

/* ── Checkbox enable submit button ── */
const check  = document.getElementById('konfirmasiCheck');
const submit = document.getElementById('submitBtn');

function syncSubmitBtn() {
    submit.disabled       = !check.checked;
    submit.style.opacity  = check.checked ? '1' : '.5';
    submit.style.cursor   = check.checked ? 'pointer' : 'not-allowed';
}

check.addEventListener('change', syncSubmitBtn);
syncSubmitBtn(); // init state
</script>

@endsection