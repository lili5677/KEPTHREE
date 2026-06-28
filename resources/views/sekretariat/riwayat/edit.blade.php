@extends('layouts.sekretariat')

@section('title', 'Riwayat Proposal – Sistem KEP')

@section('content')
@php
    $logs = $logs ?? collect();

    function riwayatTypeLabel($type) {
        return match($type) {
            'verifikasi' => 'Verifikasi',
            'revisi' => 'Revisi',
            'penugasan' => 'Penugasan Reviewer',
            'keputusan' => 'Keputusan',
            default => ucfirst(str_replace('_', ' ', $type ?? 'Aktivitas')),
        };
    }

    function riwayatIcon($type) {
        return match($type) {
            'verifikasi' => 'bi-file-earmark-check',
            'revisi' => 'bi-arrow-repeat',
            'penugasan' => 'bi-people',
            'keputusan' => 'bi-check2-circle',
            default => 'bi-activity',
        };
    }

    function riwayatClass($type) {
        return match($type) {
            'verifikasi' => 'log-verifikasi',
            'revisi' => 'log-revisi',
            'penugasan' => 'log-penugasan',
            'keputusan' => 'log-keputusan',
            default => 'log-default',
        };
    }
@endphp

<div class="riwayat-page">

    {{-- HEADER --}}
    <div class="riwayat-hero">
        <div>
            <div class="hero-badge">
                <i class="bi bi-clock-history"></i>
                Riwayat Proposal
            </div>

            <h1>Riwayat Proposal</h1>

            <p>
                Lihat catatan aktivitas proposal, mulai dari verifikasi dokumen, revisi,
                penugasan reviewer, sampai keputusan sekretariat.
            </p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="filter-panel">
        <form method="GET" action="{{ route('sekretariat.riwayat.index') }}" class="filter-form">
            <div class="filter-group search-group">
                <label for="search">Cari Aktivitas</label>
                <div class="search-input-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari judul proposal, nomor, user, atau aktivitas...">
                </div>
            </div>

            <div class="filter-group">
                <label for="type">Jenis Aktivitas</label>
                <select id="type" name="type">
                    <option value="">Semua Aktivitas</option>

                    @foreach($typeOptions as $option)
                        <option value="{{ $option }}" {{ $type === $option ? 'selected' : '' }}>
                            {{ riwayatTypeLabel($option) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-action">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>

                <a href="{{ route('sekretariat.riwayat.index') }}" class="btn-reset">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- LOG LIST --}}
    <div class="log-panel">
        <div class="panel-heading">
            <div>
                <h2>Log Aktivitas</h2>
                <p>Aktivitas terbaru ditampilkan dari yang paling baru.</p>
            </div>

            <div class="log-count">
                {{ $logs->total() ?? 0 }} aktivitas
            </div>
        </div>

        @if($logs->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>

                <h3>Belum Ada Riwayat</h3>
                <p>Belum ada aktivitas proposal yang tercatat.</p>
            </div>
        @else
            <div class="timeline-list">
                @foreach($logs as $log)
                    @php
                        $createdAt = $log->created_at
                            ? \Carbon\Carbon::parse($log->created_at)
                            : null;

                        $typeLabel = riwayatTypeLabel($log->type);
                        $icon = riwayatIcon($log->type);
                        $class = riwayatClass($log->type);

                        $protocolCode = $log->nomor_registrasi ?? 'PRO-' . $log->subject_id;
                    @endphp

                    <div class="timeline-item {{ $class }}">
                        <div class="timeline-marker">
                            <i class="bi {{ $icon }}"></i>
                        </div>

                        <div class="timeline-content">
                            <div class="timeline-top">
                                <div>
                                    <span class="type-badge">
                                        {{ $typeLabel }}
                                    </span>

                                    <span class="protocol-code">
                                        {{ $protocolCode }}
                                    </span>
                                </div>

                                <div class="timeline-date">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ $createdAt ? $createdAt->translatedFormat('d M Y, H:i') : '-' }}
                                </div>
                            </div>

                            <h3>
                                {{ $log->protocol_title ?? 'Proposal #' . $log->subject_id }}
                            </h3>

                            <p class="activity-text">
                                {{ $log->action }}
                            </p>

                            <div class="timeline-footer">
                                <span>
                                    <i class="bi bi-person"></i>
                                    {{ $log->user_name ?? 'User tidak diketahui' }}
                                </span>

                                @if($createdAt)
                                    <span>
                                        <i class="bi bi-clock"></i>
                                        {{ $createdAt->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
.riwayat-page,
.riwayat-page * {
    font-family: inherit;
}

/* HERO */
.riwayat-hero {
    padding: 1.35rem;
    margin-bottom: 1rem;
    border-radius: 22px;
    background:
        radial-gradient(circle at top left, rgba(220, 38, 38, .13), transparent 35%),
        linear-gradient(135deg, #ffffff 0%, #fff1f2 100%);
    border: 1px solid #fecaca;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .35rem .75rem;
    border-radius: 999px;
    background: #fee2e2;
    color: #991b1b;
    font-size: .78rem;
    font-weight: 900;
    margin-bottom: .75rem;
}

.riwayat-hero h1 {
    margin: 0;
    color: #111827;
    font-size: 1.65rem;
    font-weight: 900;
}

.riwayat-hero p {
    margin: .45rem 0 0;
    color: #6b7280;
    font-size: .9rem;
    line-height: 1.6;
    max-width: 760px;
}

/* FILTER */
.filter-panel {
    margin-bottom: 1rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 1rem;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
}

.filter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 220px auto;
    gap: .85rem;
    align-items: end;
}

.filter-group label {
    display: block;
    margin-bottom: .35rem;
    color: #374151;
    font-size: .78rem;
    font-weight: 900;
}

.search-input-wrap {
    position: relative;
}

.search-input-wrap i {
    position: absolute;
    left: .8rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.search-input-wrap input {
    padding-left: 2.25rem;
}

.filter-group input,
.filter-group select {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    padding: .68rem .78rem;
    font-size: .84rem;
    color: #111827;
    background: #fff;
    outline: none;
}

.filter-group input:focus,
.filter-group select:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, .12);
}

.filter-action {
    display: flex;
    gap: .5rem;
}

.btn-filter,
.btn-reset {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    padding: .68rem .9rem;
    border-radius: 12px;
    font-size: .82rem;
    font-weight: 900;
    text-decoration: none;
    white-space: nowrap;
    border: none;
    cursor: pointer;
}

.btn-filter {
    background: #dc2626;
    color: #fff;
}

.btn-filter:hover {
    background: #b91c1c;
    color: #fff;
}

.btn-reset {
    background: #fff;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-reset:hover {
    background: #f9fafb;
    color: #111827;
}

/* PANEL */
.log-panel {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    overflow: hidden;
}

.panel-heading {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.panel-heading h2 {
    margin: 0;
    color: #111827;
    font-size: 1rem;
    font-weight: 900;
}

.panel-heading p {
    margin: .25rem 0 0;
    color: #6b7280;
    font-size: .82rem;
}

.log-count {
    padding: .35rem .7rem;
    border-radius: 999px;
    background: #fee2e2;
    color: #991b1b;
    font-size: .72rem;
    font-weight: 900;
    white-space: nowrap;
}

/* TIMELINE */
.timeline-list {
    padding: 1rem;
}

.timeline-item {
    position: relative;
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr);
    gap: .9rem;
    padding-bottom: 1rem;
}

.timeline-item:not(:last-child)::before {
    content: "";
    position: absolute;
    left: 20px;
    top: 42px;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-marker {
    position: relative;
    z-index: 1;
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 1.12rem;
}

.log-verifikasi .timeline-marker {
    background: #fee2e2;
    color: #b91c1c;
}

.log-revisi .timeline-marker {
    background: #fff7ed;
    color: #c2410c;
}

.log-penugasan .timeline-marker {
    background: #eff6ff;
    color: #1d4ed8;
}

.log-keputusan .timeline-marker {
    background: #ecfdf5;
    color: #047857;
}

.timeline-content {
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.timeline-content:hover {
    border-color: #fecaca;
    box-shadow: 0 12px 24px rgba(15, 23, 42, .06);
}

.timeline-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .8rem;
    margin-bottom: .55rem;
}

.type-badge {
    display: inline-flex;
    align-items: center;
    padding: .28rem .65rem;
    border-radius: 999px;
    background: #fee2e2;
    color: #991b1b;
    font-size: .7rem;
    font-weight: 900;
    margin-right: .35rem;
}

.protocol-code {
    color: #6b7280;
    font-size: .72rem;
    font-weight: 900;
}

.timeline-date {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: #6b7280;
    font-size: .74rem;
    font-weight: 800;
    white-space: nowrap;
}

.timeline-content h3 {
    margin: 0;
    color: #111827;
    font-size: .98rem;
    font-weight: 900;
    line-height: 1.45;
}

.activity-text {
    margin: .55rem 0 0;
    color: #4b5563;
    font-size: .85rem;
    line-height: 1.6;
}

.timeline-footer {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .8rem;
    margin-top: .8rem;
    color: #6b7280;
    font-size: .76rem;
}

.timeline-footer span {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}

/* EMPTY */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: #6b7280;
}

.empty-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    margin: 0 auto .8rem;
    background: #f3f4f6;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
}

.empty-state h3 {
    margin: 0;
    color: #111827;
    font-size: 1rem;
    font-weight: 900;
}

.empty-state p {
    margin: .35rem 0 0;
    font-size: .86rem;
}

.pagination-wrap {
    padding: 0 1rem 1rem;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .filter-form {
        grid-template-columns: 1fr;
    }

    .filter-action {
        width: 100%;
    }

    .btn-filter,
    .btn-reset {
        flex: 1;
    }

    .panel-heading {
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    .riwayat-hero {
        padding: 1rem;
        border-radius: 18px;
    }

    .riwayat-hero h1 {
        font-size: 1.35rem;
    }

    .timeline-list {
        padding: .85rem;
    }

    .timeline-item {
        grid-template-columns: 34px minmax(0, 1fr);
        gap: .7rem;
    }

    .timeline-marker {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        font-size: .95rem;
    }

    .timeline-item:not(:last-child)::before {
        left: 16px;
        top: 34px;
    }

    .timeline-top {
        flex-direction: column;
        gap: .4rem;
    }

    .timeline-date {
        white-space: normal;
    }
}
</style>
@endpush