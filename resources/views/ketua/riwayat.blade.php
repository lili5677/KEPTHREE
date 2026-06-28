@extends('layouts.ketua')
@section('title', 'Riwayat TTD')

@section('content')

<div class="page-header">
    <h1>Riwayat TTD</h1>
    <p>Daftar SKE yang sudah Anda tandatangani.</p>
</div>

<div class="ketua-page">

    <div class="ketua-panel">
        <div class="panel-header">
            <div>
                <h2>Riwayat Tanda Tangan SKE</h2>
                <p>Total {{ $history->total() }} SKE sudah ditangani.</p>
            </div>
        </div>

        @if($history->isEmpty())
            <div class="empty-state-ketua">
                <i class="bi bi-clock-history"></i>
                <p>Belum ada riwayat tanda tangan SKE.</p>
            </div>
        @else
            <div class="history-table-wrap">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Nomor SKE</th>
                            <th>Judul Proposal</th>
                            <th>Peneliti</th>
                            <th>Status</th>
                            <th>Tanggal TTD</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($history as $ske)
                            <tr>
                                <td>
                                    <span class="ske-number">{{ $ske->nomor_surat }}</span>
                                </td>

                                <td>
                                    <strong>{{ \Illuminate\Support\Str::limit($ske->protocol->title ?? '-', 50) }}</strong>
                                </td>

                                <td>{{ $ske->protocol->user->name ?? '-' }}</td>

                                <td>
                                    <span class="status-pill {{ $ske->status === 'terbit' ? 'green' : 'purple' }}">
                                        {{ $ske->statusLabel() }}
                                    </span>
                                </td>

                                <td>{{ $ske->ditandatangani_at?->format('d M Y, H:i') ?? '-' }}</td>

                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('ketua.riwayat.show', $ske->id) }}" class="btn-table-outline">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($history->hasPages())
                <div class="pagination-wrap">
                    {{ $history->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

@endsection

@push('styles')
<style>
.ketua-page,
.ketua-page * {
    font-family: inherit;
}

.ketua-panel {
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

.history-table-wrap {
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .84rem;
}

.history-table thead {
    background: #f8fafc;
}

.history-table th {
    padding: .8rem .9rem;
    text-align: left;
    color: var(--text-muted);
    font-size: .72rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-bottom: 1px solid var(--border);
}

.history-table td {
    padding: .85rem .9rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text-muted);
    vertical-align: middle;
}

.history-table tr:last-child td {
    border-bottom: none;
}

.history-table tbody tr:hover {
    background: #f8fafc;
}

.history-table td strong {
    color: var(--navy-deep);
    font-weight: 900;
    line-height: 1.4;
}

.ske-number {
    color: #7c3aed;
    font-family: monospace;
    font-size: .78rem;
    font-weight: 900;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: .24rem .62rem;
    border-radius: 999px;
    font-size: .7rem;
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

.table-actions {
    display: flex;
    align-items: center;
    gap: .4rem;
    flex-wrap: wrap;
}

.btn-table-outline,
.btn-table-purple {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .45rem .65rem;
    border-radius: 10px;
    font-size: .76rem;
    font-weight: 900;
    text-decoration: none;
    border: 1px solid transparent;
    white-space: nowrap;
}

.btn-table-outline {
    background: #fff;
    color: var(--navy-deep);
    border-color: var(--border);
}

.btn-table-outline:hover {
    background: #f5f3ff;
    color: #6d28d9;
    border-color: #c4b5fd;
}

.pagination-wrap {
    padding: .9rem 1.15rem;
    border-top: 1px solid var(--border);
    background: #f8fafc;
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
</style>
@endpush