@extends('layouts.admin')

@section('title', 'Ethical Clearance')

@push('styles')
<style>
    .ec-tabs {
        display: flex;
        gap: 4px;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--border);
        overflow-x: auto;
    }
    .ec-tab {
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.15s;
        background: none;
        border-top: none; border-left: none; border-right: none;
        font-family: var(--font);
        white-space: nowrap;
    }
    .ec-tab:hover { color: var(--accent); }
    .ec-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
    .ec-tab-count {
        background: var(--accent); color: #fff; font-size: 10px; font-weight: 700;
        padding: 1px 6px; border-radius: 20px; margin-left: 6px;
    }
    .ec-tab-panel { display: none; }
    .ec-tab-panel.active { display: block; }

    .ec-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .ec-card:last-child { margin-bottom: 0; }
    .ec-card-body { padding: 20px; }

    .proto-item {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px 18px;
        margin-bottom: 12px;
        transition: box-shadow 0.15s;
    }
    .proto-item:last-child { margin-bottom: 0; }
    .proto-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.07); }

    .proto-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap; }
    .proto-kode { font-size: 11px; font-weight: 700; color: var(--text-muted); }
    .proto-title { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .proto-info { font-size: 12px; color: var(--text-muted); display: flex; gap: 16px; flex-wrap: wrap; }

    .proto-form {
        margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);
        display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end;
    }
    @media (max-width: 768px) { .proto-form { grid-template-columns: 1fr; } }

    .badge {
        display: inline-flex; align-items: center; font-size: 10.5px; font-weight: 700;
        padding: 2px 8px; border-radius: 20px;
    }
    .badge-green   { background: #dcfce7; color: #166534; }
    .badge-indigo  { background: #e0e7ff; color: #3730a3; }
    .badge-amber   { background: #fef3c7; color: #92400e; }
    .badge-orange  { background: #ffedd5; color: #9a3412; }
    .badge-blue    { background: #dbeafe; color: #1e40af; }
    .badge-emerald { background: #d1fae5; color: #065f46; }
    .badge-slate   { background: #f1f5f9; color: #475569; }

    .empty-state { padding: 48px 24px; text-align: center; color: var(--text-muted); }
    .empty-state svg {
        width: 40px; height: 40px; stroke: #d1d5db; stroke-width: 1.5; fill: none;
        margin: 0 auto 10px; display: block;
    }
    .empty-state p { font-size: 13px; font-weight: 600; }

    .arsip-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .arsip-table thead tr { background: #f8f9fc; border-bottom: 1px solid var(--border); }
    .arsip-table thead th {
        padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-muted);
    }
    .arsip-table tbody tr { border-bottom: 1px solid var(--border); transition: background 0.12s; }
    .arsip-table tbody tr:last-child { border-bottom: none; }
    .arsip-table tbody tr:hover { background: #f8f9fc; }
    .arsip-table tbody td { padding: 12px 14px; vertical-align: middle; }

    .setting-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .setting-grid { grid-template-columns: 1fr; } }

    .preview-wrap {
        background: #f8f9fc; border: 1px solid var(--border); border-radius: 8px;
        padding: 16px; text-align: center; margin: 16px 0;
    }
    .preview-label { font-size: 11px; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; }
    .preview-nomor { font-size: 22px; font-weight: 700; color: var(--accent); font-family: monospace; }

    .pg-wrap {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 14px; border-top: 1px solid var(--border);
        font-size: 12.5px; color: var(--text-muted); flex-wrap: wrap; gap: 8px;
    }
    .pg-pages { display: flex; gap: 3px; }
    .pg-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 30px; height: 30px; padding: 0 7px;
        border: 1.5px solid var(--border); border-radius: 6px;
        font-size: 12.5px; font-weight: 600; font-family: var(--font);
        text-decoration: none; color: var(--text-primary); background: #fff;
        transition: all 0.12s;
    }
    .pg-btn:hover { background: #f3f4f6; }
    .pg-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }
    .pg-btn.disabled { color: #c9cdd8; pointer-events: none; }

    .info-box {
        display: flex; gap: 10px; background: #eff6ff; border: 1px solid #bfdbfe;
        border-radius: 8px; padding: 12px 14px; font-size: 12.5px; color: #1e40af; margin-bottom: 16px;
    }
    .info-box svg { width: 15px; height: 15px; stroke: #3b82f6; stroke-width: 2; fill: none; flex-shrink: 0; margin-top: 1px; }

    .form-hint-plain { font-size: 11.5px; color: var(--text-muted); margin-top: 4px; }

    .revisi-note {
        background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px;
        padding: 10px 12px; font-size: 12.5px; color: #9a3412; margin-top: 10px;
    }
    .revisi-note strong { display: block; margin-bottom: 2px; }

    .action-row {
        display: flex; gap: 8px; margin-top: 14px; padding-top: 14px;
        border-top: 1px solid var(--border); flex-wrap: wrap;
    }

    .btn-sm {
        padding: 7px 14px; font-size: 12.5px;
    }
</style>
@endpush

@section('content')

    <div class="page-header">
        <h1 class="page-title">Ethical Clearance</h1>
        <p class="page-subtitle">Kelola penerbitan, revisi, dan penandatanganan SKE.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:16px;">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom:16px;">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="ec-tabs">
        <button class="ec-tab active" onclick="switchTab('terbitkan', this)">
            Perlu Diterbitkan
            @if($perluDiterbitkan->count() > 0)
                <span class="ec-tab-count">{{ $perluDiterbitkan->count() }}</span>
            @endif
        </button>
        <button class="ec-tab" onclick="switchTab('proses', this)">
            Sedang Diproses
            @if($sedangProses->count() > 0)
                <span class="ec-tab-count">{{ $sedangProses->count() }}</span>
            @endif
        </button>
        <button class="ec-tab" onclick="switchTab('arsip', this)">Arsip SKE</button>
        <button class="ec-tab" onclick="switchTab('setting', this)">Setting Format</button>
    </div>

    {{-- ============================================================
         TAB 1 — PERLU DITERBITKAN
    ============================================================ --}}
    <div id="tab-terbitkan" class="ec-tab-panel active">

        <div class="info-box">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>
                Protokol berikut telah <strong>disetujui (Approved)</strong> oleh Sekretaris dan siap diterbitkan SKE.
                Isi nomor surat, pilih ketua penandatangan, lalu klik <strong>Terbitkan SKE</strong>.
                Sistem akan membuat dokumen otomatis dan mengirimkannya ke peneliti untuk dikonfirmasi.
            </span>
        </div>

        @if($perluDiterbitkan->isEmpty())
            <div class="ec-card">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p>Tidak ada protokol yang menunggu penerbitan SKE.</p>
                </div>
            </div>
        @else
            @foreach($perluDiterbitkan as $protocol)
            <div class="ec-card">
                <div class="ec-card-body">
                    <div class="proto-meta">
                        <span class="proto-kode">ID-{{ $protocol->id }}</span>
                        <span class="badge badge-green">Approved</span>
                    </div>
                    <div class="proto-title">{{ $protocol->title }}</div>
                    <div class="proto-info">
                        <span><i class="bi bi-person"></i> {{ $protocol->user->name }}</span>
                        <span><i class="bi bi-calendar"></i> {{ $protocol->submitted_at?->format('d M Y') ?? '-' }}</span>
                        @if($protocol->sekretariat)
                            <span><i class="bi bi-person-badge"></i> Sekretariat: {{ $protocol->sekretariat->name }}</span>
                        @endif
                    </div>

                    <form method="POST"
                          action="{{ route('admin.ethical-clearance.terbitkan', $protocol->id) }}"
                          class="proto-form">
                        @csrf
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nomor Surat <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $suggestNomor) }}"
                                   class="form-control" placeholder="Contoh: KEP/2026/001" required>
                            <div class="form-hint-plain">Format: KODE/TAHUN/NOMOR-URUT</div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Ketua Penandatangan <span style="color:#ef4444;">*</span></label>
                            <select name="ketua_id" class="form-control" required {{ $ketuaList->isEmpty() ? 'disabled' : '' }}>
                                <option value="">-- Pilih Ketua --</option>
                                @foreach($ketuaList as $ketua)
                                    <option value="{{ $ketua->id }}"
                                        {{ ($setting['ketua_default_id'] ?? null) == $ketua->id ? 'selected' : '' }}>
                                        {{ $ketua->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($ketuaList->isEmpty())
                                <div class="form-hint-plain" style="color:#ef4444;">
                                    Belum ada ketua yang bisa ditugaskan — semua ketua belum mengisi NIP.
                                </div>
                            @endif
                        </div>
                        <div style="padding-top:20px;">
                            <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
                                <svg style="width:14px;height:14px;stroke:white;stroke-width:2.5;fill:none;flex-shrink:0;" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
                                </svg>
                                Terbitkan SKE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- ============================================================
         TAB 2 — SEDANG DIPROSES
    ============================================================ --}}
    <div id="tab-proses" class="ec-tab-panel">

        @if($sedangProses->isEmpty())
            <div class="ec-card">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p>Tidak ada SKE yang sedang diproses.</p>
                </div>
            </div>
        @else
            @foreach($sedangProses as $ske)
            @php
                $statusColorMap = [
                    'menunggu_konfirmasi' => 'blue',
                    'revisi'              => 'orange',
                    'menunggu_ttd'        => 'indigo',
                    'sudah_ttd'           => 'green',
                ];
                $sColor = $statusColorMap[$ske->status] ?? 'slate';
            @endphp
            <div class="ec-card">
                <div class="ec-card-body">
                    <div class="proto-meta">
                        <span style="font-family:monospace;font-size:12px;font-weight:700;color:var(--accent);">
                            {{ $ske->nomor_surat }}
                        </span>
                        <span class="badge badge-{{ $sColor }}">{{ $ske->statusLabel() }}</span>
                    </div>
                    <div class="proto-title">{{ $ske->protocol->title }}</div>
                    <div class="proto-info">
                        <span><i class="bi bi-person"></i> {{ $ske->protocol->user->name }}</span>
                        <span><i class="bi bi-person-badge"></i> Ketua: {{ $ske->ketua->name }}</span>
                        <span><i class="bi bi-clock-history"></i> Update: {{ $ske->updated_at->format('d M Y, H:i') }}</span>
                    </div>

                    @if($ske->status === 'revisi' && $ske->catatan_revisi)
                        <div class="revisi-note">
                            <strong>Catatan Revisi dari Peneliti:</strong>
                            {{ $ske->catatan_revisi }}
                        </div>
                    @endif

                    <div class="action-row">
                        {{-- Preview PDF --}}
                        @if($ske->file_path)
                        <a href="{{ route('admin.ethical-clearance.preview', $ske->id) }}"
                           target="_blank" class="btn btn-secondary btn-sm">
                            Lihat Dokumen
                        </a>
                        @endif

                        {{-- Menunggu konfirmasi peneliti: bisa langsung diteruskan jika tak ada revisi --}}
                        @if($ske->status === 'menunggu_konfirmasi')
                            <form method="POST" action="{{ route('admin.ethical-clearance.kirim-ketua', $ske->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Teruskan ke Ketua (Tidak Ada Revisi)
                                </button>
                            </form>
                        @endif

                        {{-- Revisi: admin proses ulang & kirim ke ketua --}}
                        @if($ske->status === 'revisi')
                            <form method="POST" action="{{ route('admin.ethical-clearance.proses-revisi', $ske->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Proses Revisi & Kirim ke Ketua
                                </button>
                            </form>
                        @endif

                        {{-- Menunggu TTD: tinggal menunggu ketua, tidak ada aksi admin --}}
                        @if($ske->status === 'menunggu_ttd')
                            <span style="font-size:12.5px;color:var(--text-muted);align-self:center;">
                                Menunggu tanda tangan dari {{ $ske->ketua->name }}...
                            </span>
                        @endif

                        {{-- Sudah TTD: admin bisa terbitkan final --}}
                        @if($ske->status === 'sudah_ttd')
                            <form method="POST" action="{{ route('admin.ethical-clearance.publish', $ske->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Terbitkan ke Peneliti
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- ============================================================
         TAB 3 — ARSIP SKE
    ============================================================ --}}
    <div id="tab-arsip" class="ec-tab-panel">
        <div class="ec-card">
            @if($arsip->isEmpty())
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p>Belum ada SKE yang terbit.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table class="arsip-table">
                        <thead>
                            <tr>
                                <th>Nomor SKE</th>
                                <th>Judul</th>
                                <th>Peneliti</th>
                                <th>Ketua</th>
                                <th>Tgl Terbit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arsip as $ske)
                            <tr>
                                <td>
                                    <span style="font-family:monospace;font-size:13px;font-weight:700;color:var(--accent);">
                                        {{ $ske->nomor_surat }}
                                    </span>
                                </td>
                                <td style="font-weight:600;font-size:13px;">{{ Str::limit($ske->protocol->title, 45) }}</td>
                                <td style="font-size:13px;">{{ $ske->protocol->user->name }}</td>
                                <td style="font-size:13px;">{{ $ske->ketua->name }}</td>
                                <td style="font-size:13px;">{{ $ske->diterbitkan_at?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.ethical-clearance.download', $ske->id) }}"
                                       style="font-size:12.5px;font-weight:600;color:var(--accent);text-decoration:none;">
                                        Download
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($arsip->hasPages())
                <div class="pg-wrap">
                    <span>{{ $arsip->firstItem() }}–{{ $arsip->lastItem() }} dari {{ $arsip->total() }} SKE</span>
                    <div class="pg-pages">
                        @if($arsip->onFirstPage())
                            <span class="pg-btn disabled">‹</span>
                        @else
                            <a href="{{ $arsip->previousPageUrl() }}" class="pg-btn">‹</a>
                        @endif
                        @foreach($arsip->getUrlRange(1, $arsip->lastPage()) as $page => $url)
                            @if($page == $arsip->currentPage())
                                <span class="pg-btn active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($arsip->hasMorePages())
                            <a href="{{ $arsip->nextPageUrl() }}" class="pg-btn">›</a>
                        @else
                            <span class="pg-btn disabled">›</span>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ============================================================
         TAB 4 — SETTING FORMAT
    ============================================================ --}}
    <div id="tab-setting" class="ec-tab-panel">
        <form method="POST" action="{{ route('admin.ethical-clearance.save-setting') }}">
            @csrf
            <div class="setting-grid">

                <div class="ec-card">
                    <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
                        <div style="font-size:14px;font-weight:700;">Format Nomor Surat</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:1px;">
                            Digunakan sebagai template nomor otomatis saat menerbitkan SKE
                        </div>
                    </div>
                    <div class="ec-card-body">
                        <div class="form-group">
                            <label class="form-label">Kode Institusi <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="kode_institusi" id="kode_institusi"
                                   value="{{ old('kode_institusi', $setting['kode_institusi'] ?? 'KEP') }}"
                                   class="form-control" placeholder="KEP" maxlength="20"
                                   oninput="updatePreview()" style="text-transform:uppercase;">
                            <div class="form-hint">Contoh: KEP, EC, KEPPK</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" id="tahun"
                                   value="{{ old('tahun', $setting['tahun'] ?? date('Y')) }}"
                                   class="form-control" min="2020" max="2099" oninput="updatePreview()">
                            <div class="form-hint-plain">Tahun otomatis berubah setiap tahun baru.</div>
                        </div>
                        <div class="preview-wrap">
                            <div class="preview-label">Preview Nomor Berikutnya:</div>
                            <div class="preview-nomor" id="preview-nomor">{{ $suggestNomor }}</div>
                        </div>
                    </div>
                </div>

                <div class="ec-card">
                    <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
                        <div style="font-size:14px;font-weight:700;">Ketua Default</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:1px;">
                            Pre-select saat menerbitkan SKE baru
                        </div>
                    </div>
                    <div class="ec-card-body">
                        <div class="form-group">
                            <label class="form-label">Pilih Ketua Default</label>
                            <select name="ketua_default_id" class="form-control" {{ $ketuaList->isEmpty() ? 'disabled' : '' }}>
                                <option value="">-- Tidak ada default --</option>
                                @foreach($ketuaList as $ketua)
                                    <option value="{{ $ketua->id }}"
                                        {{ ($setting['ketua_default_id'] ?? null) == $ketua->id ? 'selected' : '' }}>
                                        {{ $ketua->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint-plain">
                                Hanya ketua yang sudah mengisi NIP yang muncul di pilihan ini.
                            </div>
                        </div>

                        @if($semuaKetua->isNotEmpty())
                            <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-top:8px;">
                                @foreach($semuaKetua as $ketua)
                                    @php $punyaNip = !empty($ketua->nip); @endphp
                                    <div style="padding:11px 14px;border-bottom:1px solid var(--border);
                                                display:flex;align-items:center;justify-content:space-between;gap:10px;
                                                {{ $loop->last ? 'border-bottom:none;' : '' }}
                                                {{ !$punyaNip ? 'opacity:0.65;' : '' }}">
                                        <div>
                                            <div style="font-size:13.5px;font-weight:600;color:var(--text-primary);">
                                                {{ $ketua->name }}
                                            </div>
                                            <div style="font-size:11.5px;color:var(--text-muted);">
                                                {{ $ketua->email }} &nbsp;·&nbsp; NIP: {{ $ketua->nip ?? 'belum diisi' }}
                                            </div>
                                        </div>
                                        @if($punyaNip)
                                            <span class="badge badge-green" style="flex-shrink:0;">Siap TTD</span>
                                        @else
                                            <span class="badge badge-slate" style="flex-shrink:0;">Belum Bisa TTD</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if($semuaKetua->where('nip', null)->count() > 0 || $semuaKetua->where('nip', '')->count() > 0)
                                <div class="form-hint-plain" style="margin-top:8px;">
                                    Ketua dengan status <strong>"Belum Bisa TTD"</strong> perlu mengisi NIP terlebih dahulu
                                    melalui akunnya masing-masing sebelum bisa ditugaskan menandatangani SKE.
                                </div>
                            @endif
                        @else
                            <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;
                                        border:1px solid var(--border);border-radius:8px;">
                                Belum ada user dengan role ketua.
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div style="text-align:right;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Simpan Setting</button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    function switchTab(name, el) {
        document.querySelectorAll('.ec-tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.ec-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        el.classList.add('active');
    }

    function updatePreview() {
        const kode  = (document.getElementById('kode_institusi').value || 'KEP').toUpperCase();
        const tahun = document.getElementById('tahun').value || '{{ date("Y") }}';
        const urut  = '{{ str_pad(1, 3, "0", STR_PAD_LEFT) }}';
        document.getElementById('preview-nomor').textContent = kode + '/' + tahun + '/' + urut;
        document.getElementById('kode_institusi').value = kode;
    }
</script>
@endpush