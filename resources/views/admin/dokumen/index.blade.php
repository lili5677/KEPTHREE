@extends('layouts.admin')

@section('title', 'Semua Dokumen')

@push('styles')
<style>
    /* ── Filter bar ── */
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
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .filter-group select,
    .filter-group .search-wrap input {
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

    .filter-group select:focus,
    .filter-group .search-wrap input:focus {
        border-color: var(--accent);
        background: #fff;
    }

    .filter-group.grow {
        flex: 1;
        min-width: 200px;
    }

    .search-wrap {
        position: relative;
    }

    .search-wrap svg {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 15px;
        height: 15px;
        stroke: var(--text-muted);
        stroke-width: 2;
        fill: none;
        pointer-events: none;
    }

    .search-wrap input {
        padding-left: 34px !important;
        width: 100%;
    }

    /* ── Table ── */
    .table-wrap {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    thead tr {
        background: #f8f9fc;
        border-bottom: 1px solid var(--border);
    }

    thead th {
        padding: 11px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        white-space: nowrap;
    }

    thead th:last-child {
        text-align: right;
    }

    tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.12s;
    }

    tbody tr:last-child {
        border-bottom: none;
    }

    tbody tr:hover {
        background: #f8f9fc;
    }

    tbody td {
        padding: 13px 16px;
        vertical-align: top;
        color: var(--text-primary);
    }

    tbody td:last-child {
        text-align: right;
        white-space: nowrap;
    }

    .td-kode {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .td-judul {
        font-weight: 600;
        line-height: 1.4;
    }

    .td-meta {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ── Badge status ── */
    .badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .badge-amber  { background: #fef3c7; color: #92400e; }
    .badge-blue   { background: #dbeafe; color: #1e40af; }
    .badge-indigo { background: #e0e7ff; color: #3730a3; }
    .badge-orange { background: #ffedd5; color: #9a3412; }
    .badge-green  { background: #dcfce7; color: #166534; }
    .badge-red    { background: #fee2e2; color: #991b1b; }
    .badge-slate  { background: #f1f5f9; color: #475569; }

    /* ── Aksi link ── */
    .link-detail {
        color: var(--accent);
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        transition: color 0.15s;
        margin-right: 12px;
    }

    .link-detail:hover { color: var(--accent-hover); }

    .link-download {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        transition: color 0.15s;
    }

    .link-download:hover { color: var(--text-primary); }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-top: 1px solid var(--border);
        font-size: 13px;
        color: var(--text-muted);
    }

    .pagination-pages {
        display: flex;
        gap: 4px;
    }

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
        cursor: pointer;
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

    /* ── Empty state ── */
    .empty-state {
        padding: 64px 32px;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-state svg {
        width: 44px;
        height: 44px;
        stroke: #d1d5db;
        stroke-width: 1.5;
        fill: none;
        margin: 0 auto 12px;
        display: block;
    }

    .empty-state p {
        font-size: 13.5px;
        font-weight: 600;
    }

    /* ── Info box ── */
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
        width: 16px;
        height: 16px;
        stroke: #3b82f6;
        stroke-width: 2;
        fill: none;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .filter-bar { flex-direction: column; }
        .filter-group.grow { width: 100%; }
        table { font-size: 12.5px; }
        thead th, tbody td { padding: 10px 12px; }
        .col-peneliti, .col-tanggal { display: none; }
    }
</style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="page-header">
        <h1 class="page-title">Semua Dokumen</h1>
        <p class="page-subtitle">Lihat dan kelola semua dokumen pengajuan di sistem.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('admin.dokumen.index') }}">
        <div class="filter-bar">

            {{-- Status --}}
            <div class="filter-group">
                <label>Filter Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Search --}}
            <div class="filter-group grow">
                <label>Cari Dokumen</label>
                <div class="search-wrap">
                    <svg viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari berdasarkan kode, judul, atau nama peneliti...">
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary" style="height:38px;padding:0 16px;">
                Cari
            </button>

            {{-- Reset --}}
            @if(request('status') || request('search'))
                <a href="{{ route('admin.dokumen.index') }}"
                   class="btn btn-secondary" style="height:38px;padding:0 14px;">
                    Reset
                </a>
            @endif

        </div>
    </form>

    {{-- Tabel --}}
    <div class="table-wrap">
        @if($protocols->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>Tidak ada dokumen ditemukan.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th class="col-peneliti">Peneliti</th>
                            <th>Status</th>
                            <th class="col-tanggal">Tanggal Ajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($protocols as $protocol)
                        @php
                            $colorMap = [
                                'new_proposal'         => 'amber',
                                'waiting_verification' => 'blue',
                                'under_review'         => 'indigo',
                                'revision_required'    => 'orange',
                                'approved'             => 'green',
                                'rejected'             => 'red',
                            ];
                            $color = $colorMap[$protocol->status] ?? 'slate';
                        @endphp
                        <tr>
                            <td>
                                <span class="td-kode">
                                    {{ $protocol->nomor_registrasi ?? 'PRO-'.$protocol->id }}
                                </span>
                            </td>
                            <td>
                                <div class="td-judul">{{ $protocol->title }}</div>
                                <div class="td-meta">Update terakhir: {{ $protocol->updated_at->format('Y-m-d') }}</div>
                            </td>
                            <td class="col-peneliti">{{ $protocol->user->name }}</td>
                            <td>
                                <span class="badge badge-{{ $color }}">
                                    {{ $protocol->statusLabel() }}
                                </span>
                            </td>
                            <td class="col-tanggal">
                                {{ $protocol->submitted_at?->format('Y-m-d') ?? '-' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.dokumen.show', $protocol->id) }}"
                                   class="link-detail">Lihat Detail</a>
                                <a href="{{ route('admin.dokumen.download', $protocol->id) }}"
                                   class="link-download">Download</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="pagination-wrap">
                <span>
                    Menampilkan {{ $protocols->firstItem() }}–{{ $protocols->lastItem() }}
                    dari {{ $protocols->total() }} dokumen
                </span>
                <div class="pagination-pages">
                    {{-- Previous --}}
                    @if($protocols->onFirstPage())
                        <span class="pg-btn disabled">Previous</span>
                    @else
                        <a href="{{ $protocols->previousPageUrl() }}" class="pg-btn">Previous</a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach($protocols->getUrlRange(max(1, $protocols->currentPage()-2), min($protocols->lastPage(), $protocols->currentPage()+2)) as $page => $url)
                        @if($page == $protocols->currentPage())
                            <span class="pg-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if($protocols->hasMorePages())
                        <a href="{{ $protocols->nextPageUrl() }}" class="pg-btn">Next</a>
                    @else
                        <span class="pg-btn disabled">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Info box --}}
    <div class="info-box">
        <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span>
            <strong>Informasi:</strong>
            Admin dapat melihat dan mengunduh semua dokumen, namun tidak dapat mengubah isinya.
            Untuk melakukan perubahan, hubungi sekretaris atau ketua terkait.
        </span>
    </div>

@endsection