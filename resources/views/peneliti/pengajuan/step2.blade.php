@extends('layouts.peneliti')
@section('title', 'Pengajuan Baru — Langkah 2')

@section('content')

<div class="page-header">
    <h1>Pengajuan Baru</h1>
    <p>Unggah dokumen yang diperlukan untuk pengajuan ethical clearance</p>
</div>

{{-- Step Indicator --}}
<div class="step-indicator mb-4">
    <div class="step-wrap">
        <div class="step-circle done"><i class="bi bi-check-lg"></i></div>
        <div class="step-label done">Informasi Dasar</div>
    </div>
    <div class="step-connector done"></div>
    <div class="step-wrap">
        <div class="step-circle active">2</div>
        <div class="step-label active">Dokumen</div>
    </div>
    <div class="step-connector"></div>
    <div class="step-wrap">
        <div class="step-circle">3</div>
        <div class="step-label">Konfirmasi</div>
    </div>
</div>

<div class="kep-card">
    <div class="kep-card-title">
        <i class="bi bi-folder2-open"></i>
        Upload Dokumen Pendukung
    </div>

    {{-- Info ketentuan — TIDAK auto-dismiss --}}
    <div class="kep-alert info mb-4">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            <strong>Ketentuan Unggah:</strong>
            Ukuran maksimal per file <strong>2 MB</strong>.
            Format yang diterima: <strong>PDF</strong> atau <strong>DOCX</strong>.
            Jika Anda kembali ke halaman ini, file yang sebelumnya sudah diunggah akan tetap tersimpan.
        </div>
    </div>

    @if($errors->any())
        <div class="kep-alert danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:.25rem;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Build lookup dari session --}}
    @php
        $sessionByField = [];
        foreach ($existingDocs as $doc) {
            $sessionByField[$doc['field']] = $doc;
        }
    @endphp

    <form action="{{ route('peneliti.pengajuan.step2.store') }}" method="POST"
          enctype="multipart/form-data" id="uploadForm">
        @csrf
        {{-- Hidden input untuk sync file yang dihapus via JS --}}
        <input type="hidden" name="removed_fields" id="removedFieldsInput" value="">

        {{-- ── Dokumen Wajib ─────────────────────────────── --}}
        <div style="margin-bottom:1.75rem;">
            <div style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--blue-accent);margin-bottom:.85rem;display:flex;align-items:center;gap:.4rem;">
                <i class="bi bi-asterisk" style="font-size:.65rem;"></i> Dokumen Wajib
            </div>

            @php
                $wajibFields = [
                    'formulir_pengajuan' => 'Formulir Pengajuan Telaah Etik Baru',
                    'formulir_ringkasan' => 'Formulir Ringkasan Protokol Penelitian',
                ];
            @endphp

            @foreach($wajibFields as $field => $label)
                @php
                    $existing  = $sessionByField[$field] ?? null;
                    $hasFile   = $existing !== null;
                    $ext       = $existing['extension'] ?? '';
                    $isPdf     = strtolower($ext) === 'pdf';
                    $zoneId    = 'zone-' . $field;
                    $pillId    = 'pill-' . $field;
                    $iconId    = 'icon-' . $field;
                @endphp

                <div class="mb-3">
                    {{-- Upload zone — shown only when no file yet --}}
                    <div class="upload-zone {{ $hasFile ? 'has-file' : '' }} @error($field) is-invalid @enderror"
                         id="{{ $zoneId }}"
                         ondragover="handleDragOver(event,'{{ $zoneId }}')"
                         ondragleave="handleDragLeave('{{ $zoneId }}')"
                         ondrop="handleDrop(event,'{{ $zoneId }}','{{ $field }}')"
                         onclick="document.getElementById('{{ $field }}').click()">
                        <div class="upload-icon" id="{{ $iconId }}">
                            <i class="bi {{ $hasFile ? 'bi-file-earmark-check' : 'bi-cloud-arrow-up' }}"></i>
                        </div>
                        <div class="upload-title">{{ $label }}</div>
                        <div class="upload-hint">
                            @if($hasFile)
                                File sudah diunggah — klik untuk mengganti
                            @else
                                Klik atau drag &amp; drop — PDF / DOCX, maks. 2 MB
                            @endif
                        </div>
                    </div>
                    <input type="file" id="{{ $field }}" name="{{ $field }}"
                           accept=".pdf,.docx" class="d-none"
                           onchange="handleFileChange(this,'{{ $zoneId }}','{{ $pillId }}','{{ $iconId }}')">

                    @error($field)
                        <div class="field-error mt-1">{{ $message }}</div>
                    @enderror

                    {{-- File Pill --}}
                    <div class="file-pill {{ $hasFile ? 'visible' : '' }}" id="{{ $pillId }}">
                        <div class="file-pill-icon" id="{{ $pillId }}-icon">
                            @if($hasFile)
                                <i class="bi {{ $isPdf ? 'bi-file-earmark-pdf' : 'bi-file-earmark-word' }}"></i>
                            @else
                                <i class="bi bi-file-earmark-pdf"></i>
                            @endif
                        </div>
                        <div class="file-pill-body">
                            <div class="file-pill-name" id="{{ $pillId }}-name">
                                {{ $hasFile ? $existing['name'] : '' }}
                            </div>
                            <div class="file-pill-meta">
                                <span class="file-pill-size" id="{{ $pillId }}-size">
                                    {{ $hasFile && isset($existing['size']) ? formatBytesPhp($existing['size']) : '' }}
                                </span>
                                <span class="file-pill-type" id="{{ $pillId }}-type">
                                    {{ $hasFile ? strtoupper($ext) : '' }}
                                </span>
                                @if($hasFile)
                                    <span class="session-badge">Tersimpan</span>
                                @endif
                            </div>
                        </div>
                        <div class="file-pill-check"><i class="bi bi-check"></i></div>
                        <button type="button" class="file-pill-remove" title="Hapus file"
                                onclick="removeFile('{{ $field }}','{{ $zoneId }}','{{ $pillId }}','{{ $iconId }}','{{ $label }}')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Dokumen Opsional ──────────────────────────── --}}
        <div style="margin-bottom:1.75rem;">
            <div style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:.85rem;">
                Dokumen Pendukung
                <span style="font-weight:400;text-transform:none;letter-spacing:0;">(Opsional)</span>
            </div>

            @php
                $opsional = [
                    'surat_pengantar'     => ['label' => 'Surat Pengantar',                   'icon' => 'bi-envelope-paper'],
                    'proposal_penelitian' => ['label' => 'Proposal Penelitian Lengkap',       'icon' => 'bi-file-earmark-text'],
                    'informed_consent'    => ['label' => 'Informed Consent Form (ICF)',        'icon' => 'bi-file-earmark-check'],
                    'kuesioner'           => ['label' => 'Kuesioner / Instrumen Penelitian',  'icon' => 'bi-list-check'],
                ];
            @endphp

            @foreach($opsional as $key => $meta)
                @php
                    $exOpt    = $sessionByField[$key] ?? null;
                    $hasOpt   = $exOpt !== null;
                    $optExt   = $exOpt['extension'] ?? '';
                    $optIsPdf = strtolower($optExt) === 'pdf';
                    $zoneId   = 'zone-opt-' . $key;
                    $pillId   = 'pill-opt-' . $key;
                    $iconId   = 'icon-opt-' . $key;
                @endphp

                <div class="mb-3">
                    {{-- Checkbox toggle --}}
                    <div style="margin-bottom:.6rem;">
                        <input type="checkbox" class="kep-check" id="chk-{{ $key }}"
                            {{ $hasOpt ? 'checked' : '' }}
                            onchange="toggleOptDoc('{{ $key }}', this.checked)">
                        <label for="chk-{{ $key }}" style="cursor:pointer;font-weight:600;font-size:.875rem;color:var(--navy-deep);margin-left:.4rem;">
                            <i class="bi {{ $meta['icon'] }}" style="color:var(--blue-accent);margin-right:.3rem;"></i>
                            {{ $meta['label'] }}
                        </label>
                        @if($hasOpt)
                            <span class="session-badge" style="margin-left:.5rem;">Tersimpan</span>
                        @endif
                    </div>

                    {{-- Upload zone — shown when checkbox checked --}}
                    <div id="upload-{{ $key }}" style="{{ $hasOpt ? '' : 'display:none;' }}">
                        <div class="upload-zone {{ $hasOpt ? 'has-file' : '' }}"
                            id="{{ $zoneId }}"
                            ondragover="handleOptDragOver(event,'{{ $zoneId }}')"
                            ondragleave="handleOptDragLeave('{{ $zoneId }}')"
                            ondrop="handleOptDrop(event,'{{ $zoneId }}','{{ $key }}')"
                            onclick="document.getElementById('file-{{ $key }}').click()">
                            <div class="upload-icon" id="{{ $iconId }}">
                                <i class="bi {{ $hasOpt ? 'bi-file-earmark-check' : 'bi-cloud-arrow-up' }}"></i>
                            </div>
                            <div class="upload-title">{{ $meta['label'] }}</div>
                            <div class="upload-hint" id="hint-{{ $key }}">
                                @if($hasOpt)
                                    File sudah diunggah — klik untuk mengganti
                                @else
                                    Klik atau drag &amp; drop — PDF / DOCX, maks. 2 MB
                                @endif
                            </div>
                        </div>
                        <input type="file" id="file-{{ $key }}" name="pendukung_{{ $key }}"
                            accept=".pdf,.docx" class="d-none"
                            onchange="handleOptFileChange(this,'{{ $zoneId }}','{{ $pillId }}','{{ $iconId }}','{{ $key }}')">

                        {{-- File Pill --}}
                        <div class="file-pill {{ $hasOpt ? 'visible' : '' }}" id="{{ $pillId }}">
                            <div class="file-pill-icon" id="{{ $pillId }}-icon">
                                @if($hasOpt)
                                    <i class="bi {{ $optIsPdf ? 'bi-file-earmark-pdf' : 'bi-file-earmark-word' }}"></i>
                                @else
                                    <i class="bi bi-file-earmark-pdf"></i>
                                @endif
                            </div>
                            <div class="file-pill-body">
                                <div class="file-pill-name" id="{{ $pillId }}-name">
                                    {{ $hasOpt ? $exOpt['name'] : '' }}
                                </div>
                                <div class="file-pill-meta">
                                    <span class="file-pill-size" id="{{ $pillId }}-size">
                                        {{ $hasOpt && isset($exOpt['size']) ? formatBytesPhp($exOpt['size']) : '' }}
                                    </span>
                                    <span class="file-pill-type" id="{{ $pillId }}-type">
                                        {{ $hasOpt ? strtoupper($optExt) : '' }}
                                    </span>
                                </div>
                            </div>
                            <div class="file-pill-check"><i class="bi bi-check"></i></div>
                            <button type="button" class="file-pill-remove" title="Hapus file"
                                    onclick="removeOptFile('{{ $key }}','{{ $zoneId }}','{{ $pillId }}','{{ $iconId }}', {{ $hasOpt ? 'true' : 'false' }})">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="d-flex justify-between" style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);">
            <a href="{{ route('peneliti.pengajuan.create') }}" class="btn-kep btn-outline">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn-kep btn-primary">
                Selanjutnya <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

@php
/**
 * Helper PHP inline — hanya untuk Blade, tidak tersedia di JS.
 * JS punya formatBytes() sendiri.
 */
function formatBytesPhp(int $bytes): string {
    if ($bytes < 1024)        return $bytes . ' B';
    if ($bytes < 1048576)     return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 2) . ' MB';
}
@endphp

@endsection

@push('scripts')
<script>
const MAX = 2 * 1024 * 1024;
const removedFields = new Set();

function syncRemovedInput() {
    const input = document.getElementById('removedFieldsInput');
    if (input) input.value = [...removedFields].join(',');
}

/* ── AUTO-SAVE VIA AJAX ── */
function autoSaveFile(input, fieldName, isOptional = false) {
    const file = input.files[0];
    if (!file) return;
    
    const formData = new FormData();
    const actualFieldName = isOptional ? `pendukung_${fieldName}` : fieldName;
    
    formData.append(actualFieldName, file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

    fetch("{{ route('peneliti.pengajuan.step2.auto-save') }}", {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Auto-saved:', actualFieldName);
            const pillId = isOptional ? `pill-opt-${fieldName}` : `pill-${fieldName}`;
            const pill = document.getElementById(pillId);
            if (pill && !pill.querySelector('.session-badge')) {
                const meta = pill.querySelector('.file-pill-meta');
                if (meta) {
                    const badge = document.createElement('span');
                    badge.className = 'session-badge';
                    badge.textContent = 'Tersimpan';
                    meta.appendChild(badge);
                }
            }
        } else {
            console.error('❌ Auto-save failed:', data.error);
            alert('Gagal menyimpan file: ' + (data.error || 'Terjadi kesalahan'));
        }
    })
    .catch(err => {
        console.error('❌ Auto-save error:', err);
        alert('Gagal menghubungi server untuk menyimpan file.');
    });
}

/* ── Helpers ── */
function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(2) + ' MB';
}
function getExtension(filename) { return filename.split('.').pop().toUpperCase(); }
function getFileIcon(ext) {
    if (ext === 'PDF') return 'bi-file-earmark-pdf';
    if (ext === 'DOCX' || ext === 'DOC') return 'bi-file-earmark-word';
    return 'bi-file-earmark';
}

/* ── Wajib: populate pill ── */
function populatePill(pillId, file) {
    const pill = document.getElementById(pillId);
    const nameEl = document.getElementById(pillId + '-name');
    const sizeEl = document.getElementById(pillId + '-size');
    const typeEl = document.getElementById(pillId + '-type');
    const iconWrap = document.getElementById(pillId + '-icon');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatBytes(file.size);
    const ext = getExtension(file.name);
    if (typeEl) typeEl.textContent = ext;
    if (iconWrap) iconWrap.innerHTML = '<i class="bi ' + getFileIcon(ext) + '"></i>';

    // Hapus badge lama jika ada
    const oldBadge = pill?.querySelector('.session-badge');
    if (oldBadge) oldBadge.remove();

    if (pill) { pill.classList.add('visible'); pill.style.display = ''; }
}

// Untuk Dokumen Wajib
function handleFileChange(input, zoneId, pillId, iconId) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > MAX) {
        alert('Ukuran file melebihi 2 MB.');
        input.value = '';
        return;
    }
    
    autoSaveFile(input, input.id, false); 
    
    // Update UI
    const zone = document.getElementById(zoneId);
    zone.classList.add('has-file');
    const hint = zone.querySelector('.upload-hint');
    if (hint) hint.textContent = 'File dipilih — klik untuk mengganti';
    const iconEl = document.getElementById(iconId);
    if (iconEl) iconEl.innerHTML = '<i class="bi bi-file-earmark-check"></i>';
    populatePill(pillId, file);
}

function removeFile(inputId, zoneId, pillId, iconId) {
    const input = document.getElementById(inputId);
    if (input) input.value = '';
    
    // Tandai sebagai dihapus
    removedFields.add(inputId);
    syncRemovedInput();
    
    const zone = document.getElementById(zoneId);
    zone.classList.remove('has-file');
    const hint = zone.querySelector('.upload-hint');
    if (hint) hint.textContent = 'Klik atau drag & drop — PDF / DOCX, maks. 2 MB';
    const iconEl = document.getElementById(iconId);
    if (iconEl) iconEl.innerHTML = '<i class="bi bi-cloud-arrow-up"></i>';
    const pill = document.getElementById(pillId);
    if (pill) { pill.classList.remove('visible'); pill.style.display = 'none'; }
}

/* ── Drag & Drop wajib ── */
function handleDragOver(e, zoneId) { e.preventDefault(); document.getElementById(zoneId).classList.add('dragover'); }
function handleDragLeave(zoneId) { document.getElementById(zoneId).classList.remove('dragover'); }
function handleDrop(e, zoneId, inputId) {
    e.preventDefault();
    document.getElementById(zoneId).classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const input = document.getElementById(inputId);
    const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
    const map = {
        'zone-formulir_pengajuan': ['pill-formulir_pengajuan', 'icon-formulir_pengajuan'],
        'zone-formulir_ringkasan': ['pill-formulir_ringkasan', 'icon-formulir_ringkasan'],
    };
    const pair = map[zoneId];
    if (pair) handleFileChange(input, zoneId, pair[0], pair[1]);
}

/* ── Optional docs ── */
function toggleOptDoc(key, active) {
    const upload = document.getElementById('upload-' + key);
    upload.style.display = active ? 'block' : 'none';
    if (!active) {
        const input = document.getElementById('file-' + key);
        if (input) input.value = '';
        const zone = document.getElementById('zone-opt-' + key);
        if (zone) zone.classList.remove('has-file');
        const pill = document.getElementById('pill-opt-' + key);
        if (pill) { pill.classList.remove('visible'); pill.style.display = 'none'; }
    }
}

function handleOptDragOver(e, zoneId) { e.preventDefault(); document.getElementById(zoneId).classList.add('dragover'); }
function handleOptDragLeave(zoneId) { document.getElementById(zoneId).classList.remove('dragover'); }
function handleOptDrop(e, zoneId, key) {
    e.preventDefault();
    document.getElementById(zoneId).classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const input = document.getElementById('file-' + key);
    const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
    handleOptFileChange(input, zoneId, 'pill-opt-' + key, 'icon-opt-' + key, key);
}

// Untuk Dokumen Opsional
function handleOptFileChange(input, zoneId, pillId, iconId, key) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > MAX) {
        alert('Ukuran file melebihi 2 MB.');
        input.value = '';
        return;
    }
    
    autoSaveFile(input, key, true); 
    
    // Update UI
    const zone = document.getElementById(zoneId);
    zone.classList.add('has-file');
    const hint = document.getElementById('hint-' + key);
    if (hint) hint.textContent = 'File dipilih — klik untuk mengganti';
    const iconEl = document.getElementById(iconId);
    if (iconEl) iconEl.innerHTML = '<i class="bi bi-file-earmark-check"></i>';
    
    const pill = document.getElementById(pillId);
    const nameEl = document.getElementById(pillId + '-name');
    const sizeEl = document.getElementById(pillId + '-size');
    const typeEl = document.getElementById(pillId + '-type');
    const iconWrap = document.getElementById(pillId + '-icon');
    
    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatBytes(file.size);
    const ext = getExtension(file.name);
    if (typeEl) typeEl.textContent = ext;
    if (iconWrap) iconWrap.innerHTML = '<i class="bi ' + getFileIcon(ext) + '"></i>';
    
    if (pill) { pill.classList.add('visible'); pill.style.display = ''; }
}

function removeOptFile(key, zoneId, pillId, iconId, hadExisting) {
    const input = document.getElementById('file-' + key);
    if (input) input.value = '';
    removedFields.add(key);
    syncRemovedInput();
    
    const zone = document.getElementById(zoneId);
    zone.classList.remove('has-file');
    const hint = document.getElementById('hint-' + key);
    if (hint) hint.textContent = 'Klik atau drag & drop — PDF / DOCX, maks. 2 MB';
    const iconEl = document.getElementById(iconId);
    if (iconEl) iconEl.innerHTML = '<i class="bi bi-cloud-arrow-up"></i>';
    const pill = document.getElementById(pillId);
    if (pill) { pill.classList.remove('visible'); pill.style.display = 'none'; }
}

// Sync sebelum submit form
document.getElementById('uploadForm')?.addEventListener('submit', syncRemovedInput);
</script>
@endpush