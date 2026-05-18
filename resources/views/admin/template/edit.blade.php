<style>
/* ====================================================
   LAYOUT
   ==================================================== */
.tm-wrapper { padding: 0; max-width: 620px; }

/* ====================================================
   HEADER
   ==================================================== */
.tm-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 22px;
}

.tm-header-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4f46e5;
    flex-shrink: 0;
}

.tm-title   { font-size: 18px; font-weight: 700; color: #1e1b4b; margin: 0; line-height: 1.2; }
.tm-subtitle{ font-size: 13px; color: #6b7280; margin: 2px 0 0; }

/* ====================================================
   CARD
   ==================================================== */
.tm-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
}

.tm-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}

.tm-card-title { font-size: 14px; font-weight: 600; color: #111827; margin: 0; }
.tm-card-desc  { font-size: 12px; color: #9ca3af; margin: 3px 0 0; }

/* ====================================================
   FORM
   ==================================================== */
.tm-form-wrap { padding: 20px; }

.tm-field { margin-bottom: 16px; }

.tm-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}
.tm-req { color: #ef4444; }

.tm-input {
    display: block;
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    padding: 10px 13px;
    font-size: 13px;
    color: #111827;
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    font-family: inherit;
    box-sizing: border-box;
}
.tm-input::placeholder { color: #c4c9d4; }
.tm-input:focus        { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.tm-input--err         { border-color: #f87171 !important; }

.tm-textarea { resize: vertical; line-height: 1.5; min-height: 80px; }

.tm-field-err { font-size: 11px; color: #ef4444; margin: 4px 0 0; display: block; }

/* ====================================================
   FILE GANTI (opsional)
   ==================================================== */
.tm-current-file {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    margin-bottom: 10px;
}

.tm-current-file-ic {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    flex-shrink: 0;
}

.tm-current-file-info { flex: 1; min-width: 0; }
.tm-current-file-name { font-size: 12px; font-weight: 600; color: #374151; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tm-current-file-hint { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }

.tm-toggle-replace {
    font-size: 12px;
    font-weight: 500;
    color: #6366f1;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    font-family: inherit;
    text-decoration: underline;
    white-space: nowrap;
}

.tm-dz-section { display: none; margin-top: 10px; }
.tm-dz-section.show { display: block; }

.tm-dz {
    border: 2px dashed #c7d2fe;
    border-radius: 12px;
    background: #fafbff;
    cursor: pointer;
    transition: border-color .2s, background .2s;
}
.tm-dz:hover { border-color: #6366f1; background: #f5f3ff; }
.tm-dz.over  { border-color: #4f46e5 !important; background: #ede9fe !important; }
.tm-dz.err   { border-color: #fca5a5 !important; background: #fff9f9 !important; }

.tm-dz-body {
    padding: 22px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 6px;
    pointer-events: none;
}

.tm-dz-ic {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    margin-bottom: 2px;
    pointer-events: none;
}

.tm-dz-main { font-size: 13px; font-weight: 600; color: #374151; margin: 0; pointer-events: none; }
.tm-dz-or   { font-size: 12px; color: #9ca3af; margin: 0; pointer-events: none; }
.tm-dz-hint { font-size: 11px; color: #c4c9d4; margin: 4px 0 0; pointer-events: none; }

.tm-btn-browse {
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
    pointer-events: auto;
}
.tm-btn-browse:hover { background: #4f46e5; }

.tm-dz-preview {
    display: none;
    padding: 14px 16px;
    align-items: center;
    gap: 12px;
}
.tm-dz-preview.show { display: flex; }

.tm-dz-file-ic {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    flex-shrink: 0;
}

.tm-dz-info { flex: 1; min-width: 0; }
.tm-dz-name { font-size: 13px; font-weight: 600; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tm-dz-size { font-size: 11px; color: #9ca3af; margin: 3px 0 0; }

.tm-dz-rm {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #fef2f2;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ef4444;
    flex-shrink: 0;
    transition: background .15s;
}
.tm-dz-rm:hover { background: #fee2e2; }

/* ====================================================
   WARNING BOX (ganti file)
   ==================================================== */
.tm-warn-box {
    display: none;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 9px;
    padding: 10px 14px;
    font-size: 12px;
    color: #92400e;
    line-height: 1.5;
    margin-top: 8px;
    gap: 8px;
    align-items: flex-start;
}
.tm-warn-box.show { display: flex; }
.tm-warn-box svg  { flex-shrink: 0; margin-top: 1px; }

/* ====================================================
   ACTION BUTTONS
   ==================================================== */
.tm-actions { display: flex; align-items: center; gap: 10px; margin-top: 4px; }

.tm-btn-cancel {
    display: inline-flex;
    align-items: center;
    padding: 9px 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    background: #f3f4f6;
    color: #4b5563;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
}
.tm-btn-cancel:hover { background: #e5e7eb; }

.tm-btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 20px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    background: #6366f1;
    color: #fff;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
    box-shadow: 0 2px 8px rgba(99,102,241,.25);
}
.tm-btn-submit:hover    { background: #4f46e5; }
.tm-btn-submit:disabled { opacity: .6; cursor: not-allowed; }
</style>

<div class="tm-wrapper">

    {{-- ================================================
         HEADER
         ================================================ --}}
    <div class="tm-header">
        <div class="tm-header-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </div>
        <div>
            <h1 class="tm-title">Edit Template</h1>
            <p class="tm-subtitle">Perbarui informasi atau file template</p>
        </div>
    </div>

    {{-- ================================================
         FORM EDIT
         ================================================ --}}
    <div class="tm-card">
        <div class="tm-card-header">
            <div>
                <p class="tm-card-title">{{ $template->name }}</p>
                <p class="tm-card-desc">v{{ $template->versi }} · Dibuat {{ $template->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <form id="tmEditForm" action="{{ route('admin.template.update', $template) }}" method="POST"
              enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            <div class="tm-form-wrap">

                {{-- Nama Template --}}
                <div class="tm-field">
                    <label class="tm-label" for="f_nama">
                        Nama Template <span class="tm-req">*</span>
                    </label>
                    <input id="f_nama" type="text" name="nama_template" required
                           value="{{ old('nama_template', $template->name) }}"
                           class="tm-input {{ $errors->has('nama_template') ? 'tm-input--err' : '' }}">
                    @error('nama_template')
                        <span class="tm-field-err">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="tm-field">
                    <label class="tm-label" for="f_desk">Deskripsi</label>
                    <textarea id="f_desk" name="deskripsi"
                              class="tm-input tm-textarea {{ $errors->has('deskripsi') ? 'tm-input--err' : '' }}">{{ old('deskripsi', $template->description) }}</textarea>
                    @error('deskripsi')
                        <span class="tm-field-err">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Versi --}}
                <div class="tm-field">
                    <label class="tm-label" for="f_versi">Versi</label>
                    <input id="f_versi" type="text" name="versi"
                           value="{{ old('versi', $template->versi) }}"
                           class="tm-input">
                </div>

                {{-- File (opsional ganti) --}}
                <div class="tm-field">
                    <label class="tm-label">File Template</label>

                    {{-- Info file saat ini --}}
                    <div class="tm-current-file">
                        <div class="tm-current-file-ic">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="tm-current-file-info">
                            <p class="tm-current-file-name">{{ basename($template->file_path) }}</p>
                            <p class="tm-current-file-hint">File aktif saat ini</p>
                        </div>
                        <button type="button" id="tmToggleReplace" class="tm-toggle-replace">Ganti file</button>
                    </div>

                    {{-- Drop zone ganti file (tersembunyi by default) --}}
                    <div id="tmDzSection" class="tm-dz-section">
                        <div class="tm-warn-box show">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                            <span>Versi lama akan otomatis disimpan ke <strong>Riwayat Template</strong> sebelum diganti.</span>
                        </div>

                        <input id="tmFileInput" type="file" name="file_template"
                               accept=".docx,.doc,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword"
                               style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;"
                               tabindex="-1" aria-hidden="true">

                        <div id="tmDropZone" class="tm-dz" style="margin-top:10px;">
                            <div id="tmDzIdle" class="tm-dz-body">
                                <div class="tm-dz-ic">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="16 16 12 12 8 16"/>
                                        <line x1="12" y1="12" x2="12" y2="21"/>
                                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                                    </svg>
                                </div>
                                <p class="tm-dz-main">Drag &amp; drop file baru</p>
                                <p class="tm-dz-or">atau</p>
                                <button type="button" id="tmBrowseBtn" class="tm-btn-browse">Pilih File</button>
                                <p class="tm-dz-hint">DOCX &nbsp;·&nbsp; Maks. 5 MB</p>
                            </div>
                            <div id="tmDzPreview" class="tm-dz-preview">
                                <div class="tm-dz-file-ic">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                </div>
                                <div class="tm-dz-info">
                                    <p id="tmDzName" class="tm-dz-name"></p>
                                    <p id="tmDzSize" class="tm-dz-size"></p>
                                </div>
                                <button type="button" id="tmDzRemove" class="tm-dz-rm" title="Hapus">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <span id="tmFileErr" class="tm-field-err" style="display:none;"></span>
                        @error('file_template')
                            <span class="tm-field-err">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="tm-actions">
                    <a href="{{ route('admin.template.index') }}" class="tm-btn-cancel">Batal</a>
                    <button type="button" id="tmSubmit" class="tm-btn-submit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var toggleBtn  = document.getElementById('tmToggleReplace');
    var dzSection  = document.getElementById('tmDzSection');
    var fileInput  = document.getElementById('tmFileInput');
    var dropZone   = document.getElementById('tmDropZone');
    var browseBtn  = document.getElementById('tmBrowseBtn');
    var dzIdle     = document.getElementById('tmDzIdle');
    var dzPreview  = document.getElementById('tmDzPreview');
    var dzRemove   = document.getElementById('tmDzRemove');
    var dzName     = document.getElementById('tmDzName');
    var dzSize     = document.getElementById('tmDzSize');
    var fileErr    = document.getElementById('tmFileErr');
    var submitBtn  = document.getElementById('tmSubmit');
    var form       = document.getElementById('tmEditForm');
    var namaNama   = document.getElementById('f_nama');

    /* Toggle drop zone */
    toggleBtn.addEventListener('click', function () {
        var isOpen = dzSection.classList.contains('show');
        if (isOpen) {
            dzSection.classList.remove('show');
            toggleBtn.textContent = 'Ganti file';
            // reset file
            fileInput.value = '';
            dzPreview.classList.remove('show');
            dzIdle.style.display = '';
            dropZone.classList.remove('err');
            fileErr.style.display = 'none';
        } else {
            dzSection.classList.add('show');
            toggleBtn.textContent = 'Batal ganti file';
        }
    });

    /* Buka picker */
    if (browseBtn) {
        browseBtn.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            fileInput.click();
        });
    }
    if (dropZone) {
        dropZone.addEventListener('click', function (e) {
            if (e.target.closest('#tmBrowseBtn') || e.target.closest('#tmDzRemove')) return;
            if (dzPreview.classList.contains('show')) return;
            fileInput.click();
        });

        ['dragenter','dragover'].forEach(function (evt) {
            dropZone.addEventListener(evt, function (e) {
                e.preventDefault(); e.stopPropagation();
                dropZone.classList.add('over');
            });
        });
        ['dragleave','dragend'].forEach(function (evt) {
            dropZone.addEventListener(evt, function (e) {
                e.preventDefault();
                dropZone.classList.remove('over');
            });
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault(); e.stopPropagation();
            dropZone.classList.remove('over');
            var files = e.dataTransfer && e.dataTransfer.files;
            if (files && files.length) processFile(files[0]);
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files.length) processFile(fileInput.files[0]);
        });
    }

    if (dzRemove) {
        dzRemove.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            fileInput.value = '';
            dzPreview.classList.remove('show');
            dzIdle.style.display = '';
            dropZone.classList.remove('err');
            fileErr.style.display = 'none';
        });
    }

    /* Submit */
    submitBtn.addEventListener('click', function () {
        var ok = true;

        if (!namaNama.value.trim()) {
            namaNama.classList.add('tm-input--err');
            ok = false;
        } else {
            namaNama.classList.remove('tm-input--err');
        }

        // Validasi file hanya jika drop zone terbuka
        var dzOpen = dzSection && dzSection.classList.contains('show');
        if (dzOpen && fileInput && (!fileInput.files || !fileInput.files.length)) {
            dropZone.classList.add('err');
            fileErr.textContent = 'Silakan pilih file baru atau tutup opsi ganti file.';
            fileErr.style.display = 'block';
            ok = false;
        }

        if (ok) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Menyimpan...';
            form.submit();
        }
    });

    function processFile(file) {
        var ext       = file.name.split('.').pop().toLowerCase();
        var validExt  = ['docx','doc'].indexOf(ext) !== -1;
        var validSize = file.size <= 5 * 1024 * 1024;
        if (!validExt)  { showFileErr('Format tidak didukung. Gunakan DOCX/DOC.'); return; }
        if (!validSize) { showFileErr('Ukuran melebihi 5 MB.'); return; }
        try { var dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files; } catch(e){}
        dzName.textContent = file.name;
        dzSize.textContent = fmtSize(file.size);
        dzIdle.style.display = 'none';
        dzPreview.classList.add('show');
        dropZone.classList.remove('err');
        fileErr.style.display = 'none';
    }

    function showFileErr(msg) {
        dropZone.classList.add('err');
        fileErr.textContent = msg;
        fileErr.style.display = 'block';
    }

    function fmtSize(bytes) {
        if (bytes < 1024)    return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    {{-- Buka otomatis jika ada error validasi file dari server --}}
    @if($errors->has('file_template'))
        dzSection && dzSection.classList.add('show');
        toggleBtn && (toggleBtn.textContent = 'Batal ganti file');
    @endif
})();
</script>
