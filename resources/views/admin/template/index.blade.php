@extends('layouts.admin')

@section('title', 'Manajemen Template')

@section('content')

<style>
/* ====================================================
   LAYOUT & WRAPPER
   ==================================================== */
.tm-wrapper { padding: 0; }

.tm-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 860px) {
    .tm-grid { grid-template-columns: 1fr; }
}

.tm-col {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

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

.tm-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e1b4b;
    margin: 0;
    line-height: 1.2;
}

.tm-subtitle {
    font-size: 13px;
    color: #6b7280;
    margin: 2px 0 0;
}

/* ====================================================
   ALERTS
   ==================================================== */
.tm-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 12px;
    line-height: 1.5;
}
.tm-alert svg    { flex-shrink: 0; margin-top: 1px; }
.tm-alert strong { font-weight: 600; }

.tm-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.tm-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
.tm-alert--warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }

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

.tm-card-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.tm-card-desc {
    font-size: 12px;
    color: #9ca3af;
    margin: 3px 0 0;
}

/* ====================================================
   BADGES
   ==================================================== */
.tm-badge-active {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 500;
    padding: 4px 10px;
    border-radius: 20px;
    background: #dcfce7;
    color: #15803d;
}

.tm-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #22c55e;
    animation: tm-pulse 2s infinite;
}

@keyframes tm-pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: .4; }
}

.tm-badge-inactive {
    background: #f3f4f6;
    color: #9ca3af;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
}

/* ====================================================
   LIST ITEMS
   ==================================================== */
.tm-list { padding: 6px 0; }

.tm-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 20px;
    border-bottom: 1px solid #f9fafb;
}
.tm-item:last-child { border-bottom: none; }
.tm-item:hover      { background: #fafafa; }

.tm-item-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    flex-shrink: 0;
}
.tm-item-icon--gray { background: #f3f4f6; color: #9ca3af; }

.tm-item-info { flex: 1; min-width: 0; }

.tm-item-name {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.tm-item-name--muted { color: #6b7280; font-weight: 500; }

.tm-item-sub {
    font-size: 12px;
    color: #9ca3af;
    margin: 2px 0 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tm-item-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
}

.tm-tag {
    font-size: 11px;
    background: #eef2ff;
    color: #6366f1;
    padding: 2px 7px;
    border-radius: 5px;
    font-weight: 500;
}
.tm-tag--gray { background: #f3f4f6; color: #9ca3af; }

.tm-meta-txt { font-size: 11px; color: #9ca3af; }
.tm-sep      { color: #d1d5db; }

/* ====================================================
   ACTION BUTTON GROUP (per item)
   ==================================================== */
.tm-item-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.tm-btn-icon {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: background .15s;
    flex-shrink: 0;
}

.tm-btn-icon--dl   { background: #eef2ff; color: #6366f1; }
.tm-btn-icon--dl:hover { background: #e0e7ff; }

.tm-btn-icon--edit { background: #f0fdf4; color: #16a34a; }
.tm-btn-icon--edit:hover { background: #dcfce7; }

.tm-btn-icon--del  { background: #fef2f2; color: #ef4444; }
.tm-btn-icon--del:hover  { background: #fee2e2; }

/* ====================================================
   EMPTY STATE
   ==================================================== */
.tm-empty { padding: 36px 20px; text-align: center; color: #d1d5db; }
.tm-empty svg { margin: 0 auto 10px; display: block; }
.tm-empty p   { font-size: 13px; margin: 0; }

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
   DROP ZONE
   ==================================================== */
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
    padding: 28px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 6px;
    pointer-events: none;
}

.tm-dz-ic {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    margin-bottom: 4px;
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
    padding: 7px 18px;
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
    padding: 16px 18px;
    align-items: center;
    gap: 12px;
}
.tm-dz-preview.show { display: flex; }

.tm-dz-file-ic {
    width: 42px;
    height: 42px;
    border-radius: 10px;
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
   ACTION BUTTONS (form footer)
   ==================================================== */
.tm-actions { display: flex; align-items: center; gap: 10px; }

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

/* ====================================================
   MODAL (konfirmasi hapus)
   ==================================================== */
.tm-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 999;
    align-items: center;
    justify-content: center;
}
.tm-overlay.show { display: flex; }

.tm-modal {
    background: #fff;
    border-radius: 16px;
    padding: 28px 28px 22px;
    max-width: 380px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
}

.tm-modal-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #fef2f2;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ef4444;
    margin: 0 auto 16px;
}

.tm-modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #111827;
    text-align: center;
    margin: 0 0 8px;
}

.tm-modal-desc {
    font-size: 13px;
    color: #6b7280;
    text-align: center;
    line-height: 1.6;
    margin: 0 0 20px;
}

.tm-modal-actions {
    display: flex;
    gap: 10px;
}

.tm-btn-modal-cancel {
    flex: 1;
    padding: 10px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    background: #f3f4f6;
    color: #374151;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
}
.tm-btn-modal-cancel:hover { background: #e5e7eb; }

.tm-btn-modal-del {
    flex: 1;
    padding: 10px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    background: #ef4444;
    color: #fff;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
}
.tm-btn-modal-del:hover { background: #dc2626; }

/* ====================================================
   ADD BUTTON
   ==================================================== */
.tm-btn-add {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    background: #6366f1;
    color: #fff;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
    text-decoration: none;
}
.tm-btn-add:hover { background: #4f46e5; }
</style>

<div class="tm-wrapper">

    {{-- ================================================
         HEADER
         ================================================ --}}
    <div class="tm-header">
        <div class="tm-header-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <div>
            <h1 class="tm-title">Manajemen Template</h1>
            <p class="tm-subtitle">Kelola template formulir pengajuan</p>
        </div>
    </div>

    {{-- ================================================
         FLASH MESSAGES
         ================================================ --}}
    @if(session('success'))
        <div class="tm-alert tm-alert--success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="tm-alert tm-alert--error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ================================================
         MAIN GRID
         ================================================ --}}
    <div class="tm-grid">

        {{-- ============ KIRI: Daftar Template Aktif & Riwayat ============ --}}
        <div class="tm-col">

            {{-- Template Aktif --}}
            <div class="tm-card">
                <div class="tm-card-header">
                    <div>
                        <p class="tm-card-title">Template Aktif</p>
                        <p class="tm-card-desc">{{ $templates->count() }} template tersedia</p>
                    </div>
                    <span class="tm-badge-active">
                        <span class="tm-badge-dot"></span> Aktif
                    </span>
                </div>

                <div class="tm-list">
                    @forelse($templates as $template)
                        <div class="tm-item">
                            <div class="tm-item-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                            </div>
                            <div class="tm-item-info">
                                <p class="tm-item-name">{{ $template->name }}</p>
                                <p class="tm-item-sub">{{ $template->description }}</p>
                                <div class="tm-item-meta">
                                    <span class="tm-tag">v{{ $template->versi }}</span>
                                    <span class="tm-sep">·</span>
                                    <span class="tm-meta-txt">
                                        {{ $template->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                            <div class="tm-item-actions">
                                {{-- Download --}}
                                <a href="{{ route('admin.template.download', $template) }}"
                                   class="tm-btn-icon tm-btn-icon--dl" title="Download">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('admin.template.edit', $template) }}"
                                   class="tm-btn-icon tm-btn-icon--edit" title="Edit">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                {{-- Hapus --}}
                                <button type="button"
                                        class="tm-btn-icon tm-btn-icon--del"
                                        title="Hapus"
                                        onclick="tmConfirmDelete({{ $template->id }}, '{{ addslashes($template->name) }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="tm-empty">
                            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            <p>Belum ada template aktif.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Template --}}
            <div class="tm-card">
                <div class="tm-card-header">
                    <div>
                        <p class="tm-card-title">Riwayat Template</p>
                        <p class="tm-card-desc">Versi lama yang sudah digantikan</p>
                    </div>
                </div>

                <div class="tm-list">
                    @forelse($riwayat as $item)
                        <div class="tm-item">
                            <div class="tm-item-icon tm-item-icon--gray">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <div class="tm-item-info">
                                <p class="tm-item-name tm-item-name--muted">{{ $item->name }}</p>
                                <div class="tm-item-meta">
                                    <span class="tm-tag tm-tag--gray">v{{ $item->versi }}</span>
                                    <span class="tm-sep">·</span>
                                    <span class="tm-meta-txt">
                                        Diganti {{ $item->replaced_at?->format('d M Y') ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="tm-item-actions">
                                {{-- Download riwayat --}}
                                <a href="{{ route('admin.template.download', $item) }}"
                                   class="tm-btn-icon tm-btn-icon--dl" title="Download">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                </a>
                                {{-- Hapus riwayat --}}
                                <button type="button"
                                        class="tm-btn-icon tm-btn-icon--del"
                                        title="Hapus"
                                        onclick="tmConfirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </div>
                            <span class="tm-badge-inactive">Nonaktif</span>
                        </div>
                    @empty
                        <div class="tm-empty">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                            </svg>
                            <p>Belum ada riwayat template.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ============ KANAN: Form Tambah Template Baru ============ --}}
        <div class="tm-col">
            <div class="tm-card">
                <div class="tm-card-header">
                    <div>
                        <p class="tm-card-title">Tambah Template Baru</p>
                        <p class="tm-card-desc">Upload template DOCX baru ke daftar aktif</p>
                    </div>
                </div>

                <form id="tmForm" action="{{ route('admin.template.store') }}" method="POST"
                      enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="tm-form-wrap">

                        {{-- Nama Template --}}
                        <div class="tm-field">
                            <label class="tm-label" for="f_nama">
                                Nama Template <span class="tm-req">*</span>
                            </label>
                            <input id="f_nama" type="text" name="nama_template" required
                                   placeholder="Contoh: Formulir Pengajuan Telaah Etik"
                                   value="{{ old('nama_template') }}"
                                   class="tm-input {{ $errors->has('nama_template') ? 'tm-input--err' : '' }}">
                            @error('nama_template')
                                <span class="tm-field-err">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="tm-field">
                            <label class="tm-label" for="f_desk">Deskripsi</label>
                            <textarea id="f_desk" name="deskripsi"
                                      placeholder="Jelaskan tujuan dan penggunaan template ini"
                                      class="tm-input tm-textarea {{ $errors->has('deskripsi') ? 'tm-input--err' : '' }}">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <span class="tm-field-err">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Versi --}}
                        <div class="tm-field">
                            <label class="tm-label" for="f_versi">Versi</label>
                            <input id="f_versi" type="text" name="versi"
                                   placeholder="Contoh: 1.0"
                                   value="{{ old('versi') }}"
                                   class="tm-input">
                        </div>

                        {{-- File Template --}}
                        <div class="tm-field">
                            <label class="tm-label">
                                File Template <span class="tm-req">*</span>
                            </label>

                            <input id="tmFileInput" type="file" name="file_template"
                                   accept=".docx,.doc,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword"
                                   style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;"
                                   tabindex="-1" aria-hidden="true">

                            <div id="tmDropZone" class="tm-dz">
                                <div id="tmDzIdle" class="tm-dz-body">
                                    <div class="tm-dz-ic">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="16 16 12 12 8 16"/>
                                            <line x1="12" y1="12" x2="12" y2="21"/>
                                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                                        </svg>
                                    </div>
                                    <p class="tm-dz-main">Drag &amp; drop file di sini</p>
                                    <p class="tm-dz-or">atau</p>
                                    <button type="button" id="tmBrowseBtn" class="tm-btn-browse">Pilih File</button>
                                    <p class="tm-dz-hint">Format: DOCX &nbsp;·&nbsp; Maks. 5 MB</p>
                                </div>
                                <div id="tmDzPreview" class="tm-dz-preview">
                                    <div class="tm-dz-file-ic">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                    <div class="tm-dz-info">
                                        <p id="tmDzName" class="tm-dz-name"></p>
                                        <p id="tmDzSize" class="tm-dz-size"></p>
                                    </div>
                                    <button type="button" id="tmDzRemove" class="tm-dz-rm" title="Hapus file">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <span id="tmFileErr" class="tm-field-err" style="display:none;">
                                Silakan pilih file template (DOCX, maks 5 MB).
                            </span>
                            @error('file_template')
                                <span class="tm-field-err">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="tm-actions">
                            <button type="button" id="tmSubmit" class="tm-btn-submit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <polyline points="16 16 12 12 8 16"/>
                                    <line x1="12" y1="12" x2="12" y2="21"/>
                                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                                </svg>
                                Tambah Template
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- ================================================
     MODAL KONFIRMASI HAPUS
     ================================================ --}}
<div id="tmDeleteOverlay" class="tm-overlay">
    <div class="tm-modal">
        <div class="tm-modal-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6"/>
                <path d="M10 11v6M14 11v6"/>
                <path d="M9 6V4h6v2"/>
            </svg>
        </div>
        <p class="tm-modal-title">Hapus Template?</p>
        <p class="tm-modal-desc" id="tmDeleteDesc">
            File dan data template ini akan dihapus permanen dan tidak dapat dipulihkan.
        </p>
        <div class="tm-modal-actions">
            <button type="button" class="tm-btn-modal-cancel" onclick="tmCloseDelete()">Batal</button>
            <button type="button" class="tm-btn-modal-del"    onclick="tmDoDelete()">Hapus</button>
        </div>
    </div>
</div>

{{-- Form DELETE tersembunyi --}}
<form id="tmDeleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
/* ============================================================
   DROP ZONE
   ============================================================ */
(function () {
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
    var form       = document.getElementById('tmForm');
    var namaNama   = document.getElementById('f_nama');

    /* Buka picker */
    browseBtn.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        fileInput.click();
    });
    dropZone.addEventListener('click', function (e) {
        if (e.target.closest('#tmBrowseBtn') || e.target.closest('#tmDzRemove')) return;
        if (dzPreview.classList.contains('show')) return;
        fileInput.click();
    });

    /* Drag & Drop */
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

    /* Native picker */
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length) processFile(fileInput.files[0]);
    });

    /* Hapus file */
    dzRemove.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        fileInput.value = '';
        dzPreview.classList.remove('show');
        dzIdle.style.display = '';
        dropZone.classList.remove('err');
        fileErr.style.display = 'none';
    });

    /* Submit */
    submitBtn.addEventListener('click', function () {
        var ok = true;

        if (!fileInput.files || !fileInput.files.length) {
            dropZone.classList.add('err');
            fileErr.textContent = 'Silakan pilih file template terlebih dahulu.';
            fileErr.style.display = 'block';
            ok = false;
        } else {
            dropZone.classList.remove('err');
            fileErr.style.display = 'none';
        }

        if (!namaNama.value.trim()) {
            namaNama.classList.add('tm-input--err');
            ok = false;
        } else {
            namaNama.classList.remove('tm-input--err');
        }

        if (ok) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Mengupload...';
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
})();

/* ============================================================
   MODAL HAPUS
   ============================================================ */
var _deleteId = null;

function tmConfirmDelete(id, nama) {
    _deleteId = id;
    document.getElementById('tmDeleteDesc').textContent =
        'Template "' + nama + '" akan dihapus permanen dan tidak dapat dipulihkan.';
    document.getElementById('tmDeleteOverlay').classList.add('show');
}

function tmCloseDelete() {
    _deleteId = null;
    document.getElementById('tmDeleteOverlay').classList.remove('show');
}

function tmDoDelete() {
    if (!_deleteId) return;
    var form   = document.getElementById('tmDeleteForm');
    form.action = '/admin/template/' + _deleteId;
    form.submit();
}

/* Tutup modal jika klik di luar */
document.getElementById('tmDeleteOverlay').addEventListener('click', function (e) {
    if (e.target === this) tmCloseDelete();
});
</script>

@endsection