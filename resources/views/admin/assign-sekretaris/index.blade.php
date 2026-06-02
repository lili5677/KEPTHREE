@extends('layouts.admin')

@section('title', 'Assign Sekretaris')

@section('content')

<style>
/* ====================================================
   LAYOUT & WRAPPER
   ==================================================== */
.as-wrapper { padding: 0; }

.as-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    margin-top: 0;
}

@media (max-width: 860px) {
    .as-layout { flex-direction: column; }
    .as-left    { width: 100% !important; }
}

.as-left {
    width: 288px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.as-right {
    flex: 1;
    min-width: 0;
}

/* ====================================================
   HEADER
   ==================================================== */
.as-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 22px;
}

.as-header-icon {
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

.as-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e1b4b;
    margin: 0;
    line-height: 1.2;
}

.as-subtitle {
    font-size: 13px;
    color: #6b7280;
    margin: 2px 0 0;
}

/* ====================================================
   ALERTS
   ==================================================== */
.as-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 12px;
    line-height: 1.5;
}
.as-alert svg { flex-shrink: 0; margin-top: 1px; }

.as-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.as-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

/* ====================================================
   CARD
   ==================================================== */
.as-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
}

.as-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}

.as-card-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.as-card-desc {
    font-size: 12px;
    color: #9ca3af;
    margin: 3px 0 0;
}

/* ====================================================
   BADGES
   ==================================================== */
.as-badge-new {
    font-size: 10px;
    font-weight: 700;
    background: #fef3c7;
    color: #92400e;
    padding: 2px 7px;
    border-radius: 20px;
}

.as-badge-new-lg {
    font-size: 11px;
    font-weight: 700;
    background: #fef3c7;
    color: #92400e;
    padding: 3px 10px;
    border-radius: 20px;
}

/* ====================================================
   PROPOSAL LIST (kolom kiri)
   ==================================================== */
.as-proposal-item {
    padding: 13px 20px;
    cursor: pointer;
    border-left: 3px solid transparent;
    border-bottom: 1px solid #f9fafb;
    transition: background .15s, border-color .15s;
}
.as-proposal-item:last-child { border-bottom: none; }
.as-proposal-item:hover      { background: #fafafa; }
.as-proposal-item.active {
    background: #eef2ff;
    border-left-color: #6366f1;
}

.as-item-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
}

.as-item-num {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
}

.as-item-title {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 3px;
    line-height: 1.4;
}

.as-item-sub {
    font-size: 12px;
    color: #9ca3af;
    margin: 0;
    line-height: 1.5;
}

/* ====================================================
   WORKLOAD BARS (kolom kiri)
   ==================================================== */
.as-workload-body {
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.as-wl-top {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
}

.as-wl-name  { font-size: 13px; font-weight: 600; color: #111827; }
.as-wl-count { font-size: 12px; font-weight: 700; color: #6b7280; }

.as-wl-track {
    width: 100%;
    height: 6px;
    background: #f3f4f6;
    border-radius: 99px;
    overflow: hidden;
}

.as-wl-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .6s ease;
}

.as-wl-low  { background: #6366f1; }
.as-wl-mid  { background: #f59e0b; }
.as-wl-high { background: #ef4444; }

/* ====================================================
   EMPTY STATES
   ==================================================== */
.as-empty {
    padding: 36px 20px;
    text-align: center;
    color: #d1d5db;
}
.as-empty svg { margin: 0 auto 10px; display: block; }
.as-empty p   { font-size: 13px; margin: 0; }

.as-empty-page {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 64px 32px;
    text-align: center;
}
.as-empty-page svg { margin: 0 auto 14px; display: block; color: #d1d5db; }
.as-empty-page p   { font-size: 13px; font-weight: 600; color: #9ca3af; margin: 0; }

.as-placeholder {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 80px 32px;
    text-align: center;
}
.as-placeholder svg { margin: 0 auto 12px; display: block; color: #d1d5db; }
.as-placeholder p   { font-size: 13px; font-weight: 500; color: #9ca3af; margin: 0; }

/* ====================================================
   DETAIL PANEL (kolom kanan)
   ==================================================== */
.as-detail {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
}

.as-detail-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}

.as-detail-num {
    font-size: 12px;
    font-weight: 600;
    color: #9ca3af;
}

.as-detail-body { padding: 20px; }

.as-detail-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    letter-spacing: -.3px;
    margin: 0 0 20px;
    line-height: 1.35;
}

/* Section label */
.as-section-label {
    font-size: 10px;
    font-weight: 700;
    color: #9ca3af;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin: 0 0 12px;
}

/* Info grid */
.as-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 24px;
    margin-bottom: 22px;
}

.as-info-label { font-size: 11px; color: #9ca3af; margin: 0 0 2px; }
.as-info-value { font-size: 13px; font-weight: 600; color: #374151; margin: 0; }

/* Ringkasan */
.as-ringkasan {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.65;
    margin: 0 0 22px;
}

/* Dokumen grid */
.as-doc-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 8px;
}

.as-doc-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 10px;
}

.as-doc-item.uploaded { background: #f0fdf4; border: 1px solid #bbf7d0; }
.as-doc-item.missing  { background: #f9fafb; border: 1px solid #e5e7eb; }
.as-doc-item svg      { flex-shrink: 0; }

.as-doc-label { font-size: 12px; font-weight: 500; }
.as-doc-item.uploaded .as-doc-label { color: #374151; }
.as-doc-item.missing  .as-doc-label { color: #9ca3af; }

.as-extra-doc {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: #6b7280;
    padding: 2px 0;
}

/* ====================================================
   ASSIGN FORM (dalam detail panel)
   ==================================================== */
.as-assign-section {
    margin-top: 22px;
    padding-top: 20px;
    border-top: 1px solid #f3f4f6;
}

.as-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}
.as-req { color: #ef4444; }

.as-select {
    display: block;
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    padding: 10px 36px 10px 13px;
    font-size: 13px;
    color: #111827;
    background: #fff;
    outline: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    cursor: pointer;
    font-family: inherit;
    box-sizing: border-box;
    transition: border-color .15s, box-shadow .15s;
    margin-bottom: 6px;
}
.as-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.as-select.err   { border-color: #f87171 !important; box-shadow: 0 0 0 3px rgba(248,113,113,.1) !important; }

.as-hint {
    font-size: 11px;
    color: #9ca3af;
    margin: 0 0 16px;
}

.as-btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    gap: 7px;
    padding: 10px 20px;
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
.as-btn-submit:hover:not(:disabled) { background: #4f46e5; }
.as-btn-submit:disabled { opacity: .55; cursor: not-allowed; }

/* ====================================================
   TOAST
   ==================================================== */
.as-toast {
    display: none;
    position: fixed;
    bottom: 24px;
    right: 24px;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
    z-index: 9999;
    opacity: 0;
    transform: translateY(12px);
    transition: opacity .25s ease, transform .25s ease;
    pointer-events: none;
}
.as-toast.show {
    display: flex;
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}
.as-toast.success { background: #16a34a; }
.as-toast.error   { background: #dc2626; }
</style>

<div class="as-wrapper">

    {{-- ================================================
         HEADER
         ================================================ --}}
    <div class="as-header">
        <div class="as-header-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <h1 class="as-title">Assign Sekretaris</h1>
            <p class="as-subtitle">Lihat detail proposal dan tetapkan sekretaris penanggungjawab.</p>
        </div>
    </div>

    {{-- ================================================
         FLASH MESSAGES
         ================================================ --}}
    @if(session('success'))
        <div class="as-alert as-alert--success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="as-alert as-alert--error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if($protocols->isEmpty())

        {{-- ================================================
             EMPTY STATE
             ================================================ --}}
        <div class="as-empty-page">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>Tidak ada proposal baru yang menunggu assignment.</p>
        </div>

    @else

    {{-- ================================================
         MAIN LAYOUT — 2 KOLOM
         ================================================ --}}
    <div class="as-layout">

        {{-- ============================================
             KOLOM KIRI
             ============================================ --}}
        <div class="as-left">

            {{-- Daftar Proposal Baru --}}
            <div class="as-card">
                <div class="as-card-header">
                    <div>
                        <p class="as-card-title">Proposal Baru</p>
                        <p class="as-card-desc" id="as-counter">{{ $protocols->count() }} proposal menunggu</p>
                    </div>
                </div>

                <div id="proposal-list">
                    @foreach($protocols as $protocol)
                    <div id="item-{{ $protocol->id }}"
                         class="as-proposal-item {{ $loop->first ? 'active' : '' }}"
                         onclick="asShowDetail({{ $protocol->id }})">
                        <div class="as-item-meta">
                            <span class="as-item-num">{{ $protocol->nomor_registrasi ?? 'PRO-'.$protocol->id }}</span>
                            <span class="as-badge-new">New</span>
                        </div>
                        <p class="as-item-title">{{ Str::limit($protocol->title, 55) }}</p>
                        <p class="as-item-sub">{{ $protocol->user->name }}</p>
                        <p class="as-item-sub">Diajukan: {{ $protocol->submitted_at?->format('Y-m-d') ?? $protocol->created_at->format('Y-m-d') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Beban Kerja Sekretaris --}}
            <div class="as-card">
                <div class="as-card-header">
                    <div>
                        <p class="as-card-title">Beban Kerja Sekretaris</p>
                    </div>
                </div>

                <div class="as-workload-body">
                    @php $maxLoad = $sekretarisList->max('workload') ?: 1; @endphp
                    @forelse($sekretarisList as $sek)
                        @php
                            $pct = round(($sek->workload / $maxLoad) * 100);
                            $cls = $pct >= 80 ? 'as-wl-high' : ($pct >= 50 ? 'as-wl-mid' : 'as-wl-low');
                        @endphp
                        <div>
                            <div class="as-wl-top">
                                <span class="as-wl-name">{{ $sek->name }}</span>
                                <span class="as-wl-count">{{ $sek->workload }}</span>
                            </div>
                            <div class="as-wl-track">
                                <div class="as-wl-fill {{ $cls }}" style="width:{{ max($pct, 4) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="as-empty">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                            <p>Belum ada sekretaris terdaftar.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
        {{-- end kolom kiri --}}


        {{-- ============================================
             KOLOM KANAN
             ============================================ --}}
        <div class="as-right">

            {{-- Placeholder awal --}}
            <div id="as-placeholder" class="as-placeholder" style="display:none;">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>Pilih proposal untuk melihat detail</p>
            </div>

            {{-- Detail panels --}}
            @foreach($protocols as $protocol)
            <div id="detail-{{ $protocol->id }}"
                 class="as-detail"
                 style="{{ $loop->first ? '' : 'display:none;' }}">

                {{-- Header --}}
                <div class="as-detail-head">
                    <span class="as-detail-num">{{ $protocol->nomor_registrasi ?? 'PRO-'.$protocol->id }}</span>
                    <span class="as-badge-new-lg">New Proposal</span>
                </div>

                <div class="as-detail-body">

                    <h2 class="as-detail-title">{{ $protocol->title }}</h2>

                    {{-- Informasi Peneliti --}}
                    <p class="as-section-label">Informasi Peneliti</p>
                    <div class="as-info-grid">
                        <div>
                            <p class="as-info-label">Nama Peneliti</p>
                            <p class="as-info-value">{{ $protocol->user->name }}</p>
                        </div>
                        <div>
                            <p class="as-info-label">Program Studi</p>
                            <p class="as-info-value">{{ $protocol->program_studi }}</p>
                        </div>
                        <div>
                            <p class="as-info-label">Tanggal Pengajuan</p>
                            <p class="as-info-value">
                                {{ $protocol->submitted_at?->format('Y-m-d') ?? $protocol->created_at->format('Y-m-d') }}
                            </p>
                        </div>
                        <div>
                            <p class="as-info-label">Sumber Pendanaan</p>
                            <p class="as-info-value">{{ $protocol->sumber_pendanaan }}</p>
                        </div>
                    </div>

                    {{-- Ringkasan Penelitian --}}
                    <p class="as-section-label">Ringkasan Penelitian</p>
                    <p class="as-ringkasan">{{ $protocol->ringkasan_penelitian }}</p>

                    {{-- Dokumen yang Diunggah --}}
                    <p class="as-section-label">Dokumen yang Diunggah</p>
                    @php
                        $requiredDocs  = [
                            'formulir_pengajuan' => 'Formulir Pengajuan',
                            'formulir_ringkasan' => 'Ringkasan Protokol',
                            'pendukung'          => 'Dokumen Pendukung',
                        ];
                        $uploadedTypes = $protocol->documents->pluck('type')->toArray();
                    @endphp
                    <div class="as-doc-grid">
                        @foreach($requiredDocs as $type => $label)
                            @if(in_array($type, $uploadedTypes))
                                <div class="as-doc-item uploaded">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="as-doc-label">{{ $label }}</span>
                                </div>
                            @else
                                <div class="as-doc-item missing">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="as-doc-label">{{ $label }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Dokumen pendukung tambahan --}}
                    @php $extraDocs = $protocol->documents->where('type', 'pendukung'); @endphp
                    @if($extraDocs->isNotEmpty())
                        <div style="margin-top:8px; display:flex; flex-direction:column; gap:4px;">
                            @foreach($extraDocs as $doc)
                                <div class="as-extra-doc">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    {{ $doc->name }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Assign Sekretaris --}}
                    <div class="as-assign-section">
                        <p class="as-section-label">Assign Sekretaris</p>
                        <label class="as-label">
                            Pilih Sekretaris <span class="as-req">*</span>
                        </label>
                        <select id="select-{{ $protocol->id }}" class="as-select">
                            <option value="">-- Pilih Sekretaris --</option>
                            @foreach($sekretarisList as $sek)
                                <option value="{{ $sek->id }}">
                                    {{ $sek->name }} (Workload: {{ $sek->workload }} proposal)
                                </option>
                            @endforeach
                        </select>
                        <p class="as-hint">Sekretaris akan menerima notifikasi setelah ditugaskan.</p>
                        <button type="button"
                                id="btn-assign-{{ $protocol->id }}"
                                onclick="asHandleAssign({{ $protocol->id }}, '{{ route('admin.sekretaris.assign', $protocol->id) }}')"
                                class="as-btn-submit">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Assign Sekretaris
                        </button>
                    </div>

                </div>
            </div>
            @endforeach

        </div>
        {{-- end kolom kanan --}}

    </div>

    @endif

    {{-- ================================================
         TOAST NOTIFICATION
         ================================================ --}}
    <div id="as-toast" class="as-toast">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        <span id="as-toast-msg">Sekretaris berhasil ditugaskan.</span>
    </div>

</div>

<script>
/* ============================================================
   SHOW DETAIL
   ============================================================ */
function asShowDetail(id) {
    document.querySelectorAll('[id^="detail-"]').forEach(function (el) {
        el.style.display = 'none';
    });
    var ph = document.getElementById('as-placeholder');
    if (ph) ph.style.display = 'none';

    var detail = document.getElementById('detail-' + id);
    if (detail) detail.style.display = '';

    document.querySelectorAll('.as-proposal-item').forEach(function (el) {
        el.classList.remove('active');
    });
    var item = document.getElementById('item-' + id);
    if (item) item.classList.add('active');
}

/* ============================================================
   HANDLE ASSIGN (AJAX)
   ============================================================ */
async function asHandleAssign(protocolId, url) {
    var select = document.getElementById('select-' + protocolId);
    var btn    = document.getElementById('btn-assign-' + protocolId);

    if (!select.value) {
        select.classList.add('err');
        setTimeout(function () { select.classList.remove('err'); }, 2000);
        return;
    }

    btn.disabled    = true;
    btn.textContent = 'Memproses…';

    try {
        var res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ sekretaris_id: select.value }),
        });

        var data = await res.json();

        if (res.ok && data.success) {
            asShowToast(data.message || 'Sekretaris berhasil ditugaskan.', 'success');

            setTimeout(function () {
                var item   = document.getElementById('item-' + protocolId);
                var detail = document.getElementById('detail-' + protocolId);

                var remaining = Array.from(document.querySelectorAll('.as-proposal-item'))
                                     .filter(function (el) { return el.id !== 'item-' + protocolId; });

                if (remaining.length > 0) {
                    var nextId = remaining[0].id.replace('item-', '');
                    asShowDetail(nextId);
                } else {
                    if (detail) detail.style.display = 'none';
                    var ph = document.getElementById('as-placeholder');
                    if (ph) ph.style.display = '';
                }

                if (item)   item.remove();
                if (detail) detail.remove();

                var leftover = document.querySelectorAll('.as-proposal-item').length;
                var counter  = document.getElementById('as-counter');
                if (counter) counter.textContent = leftover + ' proposal menunggu';

                if (leftover === 0) setTimeout(function () { location.reload(); }, 600);
            }, 800);

        } else {
            asShowToast(data.message || 'Gagal mengassign sekretaris.', 'error');
            btn.disabled    = false;
            btn.textContent = 'Assign Sekretaris';
        }
    } catch (err) {
        asShowToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
        btn.disabled    = false;
        btn.textContent = 'Assign Sekretaris';
    }
}

/* ============================================================
   TOAST
   ============================================================ */
var _asToastTimer;

function asShowToast(msg, type) {
    var toast = document.getElementById('as-toast');
    document.getElementById('as-toast-msg').textContent = msg;
    toast.className = 'as-toast show ' + (type || 'success');

    clearTimeout(_asToastTimer);
    _asToastTimer = setTimeout(function () {
        toast.classList.remove('show');
    }, 3200);
}
</script>

@endsection