@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@push('styles')
<style>
    .filter-bar {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 18px 20px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        flex: 1;
        min-width: 160px;
    }
    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .filter-group select {
        height: 38px;
        padding: 0 12px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font);
        color: var(--text-primary);
        background: #fafafa;
        outline: none;
        transition: border-color 0.15s;
    }
    .filter-group select:focus {
        border-color: var(--accent);
        background: #fff;
    }

    .log-wrap {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .log-item {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        transition: box-shadow 0.12s;
    }
    .log-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .log-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .log-icon svg {
        width: 18px;
        height: 18px;
        stroke-width: 1.8;
        fill: none;
    }

    .icon-login      { background: #e0e7ff; } .icon-login svg      { stroke: #4338ca; }
    .icon-pengajuan  { background: #dbeafe; } .icon-pengajuan svg  { stroke: #1d4ed8; }
    .icon-upload     { background: #dbeafe; } .icon-upload svg     { stroke: #1d4ed8; }
    .icon-review     { background: #ffedd5; } .icon-review svg     { stroke: #c2410c; }
    .icon-verifikasi { background: #dcfce7; } .icon-verifikasi svg { stroke: #15803d; }
    .icon-penugasan  { background: #f3e8ff; } .icon-penugasan svg  { stroke: #7e22ce; }
    .icon-revisi     { background: #fef3c7; } .icon-revisi svg     { stroke: #b45309; }
    .icon-keputusan  { background: #dcfce7; } .icon-keputusan svg  { stroke: #15803d; }
    .icon-default    { background: #f1f5f9; } .icon-default svg    { stroke: #475569; }

    .log-body {
        flex: 1;
        min-width: 0;
    }
    .log-top-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }
    .log-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .log-time {
        font-size: 11.5px;
        color: var(--text-muted);
        white-space: nowrap;
        flex-shrink: 0;
    }
    .log-desc {
        font-size: 13px;
        color: #444;
        margin-top: 2px;
        line-height: 1.45;
    }
    .log-tags {
        display: flex;
        gap: 6px;
        margin-top: 6px;
        flex-wrap: wrap;
    }
    .log-tag {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 9px;
        border-radius: 20px;
        background: #f1f5f9;
        color: var(--text-primary);
    }
    .log-tag.role {
        background: #e0e7ff;
        color: #3730a3;
    }

    .pg-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 4px;
        font-size: 13px;
        color: var(--text-muted);
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 6px;
    }
    .pg-pages { display: flex; gap: 4px; }
    .pg-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border: 1.5px solid var(--border);
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        font-family: var(--font);
        text-decoration: none;
        color: var(--text-primary);
        background: #fff;
        transition: all 0.12s;
    }
    .pg-btn:hover { background: #f3f4f6; }
    .pg-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }
    .pg-btn.disabled {
        color: #c9cdd8;
        cursor: not-allowed;
        pointer-events: none;
    }

    .empty-state {
        padding: 64px 32px;
        text-align: center;
        color: var(--text-muted);
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
    }
    .empty-state svg {
        width: 44px; height: 44px;
        stroke: #d1d5db; stroke-width: 1.5; fill: none;
        margin: 0 auto 12px; display: block;
    }
    .empty-state p { font-size: 13.5px; font-weight: 600; }

    .info-box {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: var(--radius);
        padding: 14px 16px;
        margin-top: 16px;
        font-size: 13px;
        color: #1e40af;
    }
    .info-box svg {
        width: 16px; height: 16px;
        stroke: #3b82f6; stroke-width: 2; fill: none;
        flex-shrink: 0; margin-top: 1px;
    }

    @media (max-width: 768px) {
        .filter-bar { flex-direction: column; }
        .filter-group { width: 100%; }
        .log-top-row { flex-direction: column; align-items: flex-start; gap: 2px; }
    }
</style>
@endpush

@section('content')

    <div class="page-header">
        <h1 class="page-title">Log Aktivitas</h1>
        <p class="page-subtitle">Monitor semua aktivitas pengguna di sistem.</p>
    </div>

    {{--
        Daftar tipe aktivitas didefinisikan langsung di sini (bukan dari controller),
        supaya blade ini tidak bergantung pada variabel tambahan apapun selain
        $logs dan $users yang sudah pasti dikirim oleh ActivityLogController.
        Sesuaikan array ini jika ada tipe baru ditambahkan ke enum activity_logs.type.
    --}}
    @php
        $typeOptions = [
            'login'      => 'Login',
            'pengajuan'  => 'Pengajuan',
            'upload'     => 'Upload',
            'review'     => 'Review',
            'verifikasi' => 'Verifikasi',
            'penugasan'  => 'Penugasan',
            'revisi'     => 'Revisi',
            'keputusan'  => 'Keputusan',
        ];

        $iconMap = [
            'login'      => 'login',
            'pengajuan'  => 'pengajuan',
            'upload'     => 'upload',
            'review'     => 'review',
            'verifikasi' => 'verifikasi',
            'penugasan'  => 'penugasan',
            'revisi'     => 'revisi',
            'keputusan'  => 'keputusan',
        ];

        $titleMap = [
            'login'      => 'Login Sistem',
            'pengajuan'  => 'Submit Pengajuan Baru',
            'upload'     => 'Upload Dokumen',
            'review'     => 'Submit Review',
            'verifikasi' => 'Verifikasi Dokumen',
            'penugasan'  => 'Penugasan',
            'revisi'     => 'Permintaan Revisi',
            'keputusan'  => 'Keputusan Sekretaris',
        ];
    @endphp

    {{-- Filter --}}
    <form method="GET" action="{{ route('log.index') }}">
        <div class="filter-bar">

            <div class="filter-group">
                <label>Filter User</label>
                <select name="user_id" onchange="this.form.submit()">
                    <option value="">Semua User</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Filter Aktivitas</label>
                <select name="type" onchange="this.form.submit()">
                    <option value="">Semua Aktivitas</option>
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Periode</label>
                <select name="periode" onchange="this.form.submit()">
                    <option value="semua"      {{ $periode === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="hari_ini"   {{ $periode === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini" {{ $periode === 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan_ini"  {{ $periode === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </div>

        </div>
    </form>

    {{-- List log --}}
    @if($logs->isEmpty())
        <div class="empty-state">
            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <p>Tidak ada aktivitas pada periode/filter ini.</p>
        </div>
    @else
        <div class="log-wrap">
            @foreach($logs as $log)
                @php
                    $iconKey = $iconMap[$log->type] ?? 'default';
                    $title   = $titleMap[$log->type] ?? ucfirst(str_replace('_', ' ', $log->type));
                    $role    = $log->user?->roles?->first()?->name ?? '-';
                @endphp
            <div class="log-item">
                <div class="log-icon icon-{{ $iconKey }}">
                    @switch($iconKey)
                        @case('login')
                            <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            @break
                        @case('pengajuan')
                            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            @break
                        @case('upload')
                            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            @break
                        @case('review')
                            <svg viewBox="0 0 24 24"><path d="M21 12a9 9 0 11-9-9c2.52 0 4.93.99 6.74 2.74L21 8"/><polyline points="21 3 21 8 16 8"/></svg>
                            @break
                        @case('verifikasi')
                            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            @break
                        @case('penugasan')
                            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            @break
                        @case('revisi')
                            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4z"/></svg>
                            @break
                        @case('keputusan')
                            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    @endswitch
                </div>

                <div class="log-body">
                    <div class="log-top-row">
                        <span class="log-title">{{ $title }}</span>
                        <span class="log-time">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div class="log-desc">{{ $log->action }}</div>
                    <div class="log-tags">
                        <span class="log-tag">{{ $log->user?->name ?? 'Sistem' }}</span>
                        <span class="log-tag role">{{ ucfirst($role) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pg-wrap">
            <span>Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} aktivitas</span>
            <div class="pg-pages">
                @if($logs->onFirstPage())
                    <span class="pg-btn disabled">Previous</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="pg-btn">Previous</a>
                @endif

                @foreach($logs->getUrlRange(max(1, $logs->currentPage()-2), min($logs->lastPage(), $logs->currentPage()+2)) as $page => $url)
                    @if($page == $logs->currentPage())
                        <span class="pg-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="pg-btn">Next</a>
                @else
                    <span class="pg-btn disabled">Next</span>
                @endif
            </div>
        </div>
    @endif

    {{-- Info box --}}
    <div class="info-box">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>
            <strong>Informasi:</strong>
            Semua aktivitas dicatat secara otomatis dengan timestamp dan detail lengkap untuk audit trail sistem.
        </span>
    </div>

@endsection