@extends('layouts.sekretariat')

@section('title', 'Dashboard Sekretariat')

@section('content')
@php
    $menungguVerifikasi = $menungguVerifikasi ?? 0;
    $sedangOnReview = $sedangOnReview ?? 0;
    $perluKeputusan = $perluKeputusan ?? 0;
    $selesaiBulanIni = $selesaiBulanIni ?? 0;

    $prioritas = $prioritas ?? collect();
    $reviewProgress = $reviewProgress ?? collect();
@endphp

<div class="sekretariat-dashboard">

    {{-- HERO --}}
    <div class="secretariat-hero">
        <div class="hero-main">
            <div class="hero-badge">
                <i class="bi bi-shield-check"></i>
                Dashboard Sekretariat
            </div>

            <h1>Halo, {{ auth()->user()->name }}</h1>

            <p>
                Kelola proses verifikasi dokumen, pemantauan review reviewer, dan penetapan
                keputusan sekretariat untuk proposal penelitian.
            </p>
        </div>

        <div class="hero-side">
            <div class="side-label">Perlu Tindakan</div>
            <div class="side-number">{{ $menungguVerifikasi + $perluKeputusan }}</div>
            <div class="side-text">
                Proposal yang membutuhkan proses dari sekretariat.
            </div>
        </div>
    </div>

    {{-- STATISTIC CARDS --}}
    <div class="stats-grid">
        <a href="{{ route('sekretariat.verifikasi.index') }}" class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-file-earmark-text"></i>
            </div>

            <div>
                <div class="stat-value">{{ $menungguVerifikasi }}</div>
                <div class="stat-label">Menunggu Verifikasi</div>
                <div class="stat-desc">Dokumen perlu diperiksa</div>
            </div>
        </a>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-hourglass-split"></i>
            </div>

            <div>
                <div class="stat-value">{{ $sedangOnReview }}</div>
                <div class="stat-label">Sedang Review</div>
                <div class="stat-desc">Menunggu feedback reviewer</div>
            </div>
        </div>

        <a href="{{ route('sekretariat.decision.index') }}" class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-clipboard-check"></i>
            </div>

            <div>
                <div class="stat-value">{{ $perluKeputusan }}</div>
                <div class="stat-label">Perlu Keputusan</div>
                <div class="stat-desc">Review sudah selesai</div>
            </div>
        </a>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <div class="stat-value">{{ $selesaiBulanIni }}</div>
                <div class="stat-label">Selesai Bulan Ini</div>
                <div class="stat-desc">Proposal selesai diproses</div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="quick-action-grid">
        <a href="{{ route('sekretariat.verifikasi.index') }}" class="quick-action-card primary-action">
            <div class="quick-icon">
                <i class="bi bi-file-earmark-check"></i>
            </div>

            <div class="quick-content">
                <span class="quick-label">Verifikasi Dokumen</span>
                <strong>{{ $menungguVerifikasi }}</strong>
                <p>Periksa kelengkapan dokumen pengajuan dari peneliti.</p>
            </div>

            <div class="quick-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('sekretariat.decision.index') }}" class="quick-action-card decision-action">
            <div class="quick-icon">
                <i class="bi bi-check2-circle"></i>
            </div>

            <div class="quick-content">
                <span class="quick-label">Secretary Decision</span>
                <strong>{{ $perluKeputusan }}</strong>
                <p>Tetapkan keputusan setelah reviewer menyelesaikan feedback.</p>
            </div>

            <div class="quick-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <div class="quick-action-card review-action">
            <div class="quick-icon">
                <i class="bi bi-people"></i>
            </div>

            <div class="quick-content">
                <span class="quick-label">Dalam Review</span>
                <strong>{{ $sedangOnReview }}</strong>
                <p>Pantau proposal yang sedang dalam proses review.</p>
            </div>

            <div class="quick-arrow muted">
                <i class="bi bi-activity"></i>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="dashboard-main-grid">

        {{-- PROPOSAL PRIORITAS --}}
        <div class="kep-panel">
            <div class="panel-heading">
                <div>
                    <h2>Proposal Prioritas</h2>
                    <p>Daftar proposal yang membutuhkan tindak lanjut sekretariat.</p>
                </div>

                <a href="{{ route('sekretariat.verifikasi.index') }}" class="panel-link">
                    Lihat Verifikasi
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @if($prioritas->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>Tidak ada proposal prioritas</h3>
                    <p>Belum ada proposal yang membutuhkan tindakan sekretariat saat ini.</p>
                </div>
            @else
                <div class="priority-list">
                    @foreach($prioritas as $proposal)
                        @php
                            $isVerification = ($proposal->action_label ?? '') === 'Verifikasi Dokumen';
                            $isDecision = ($proposal->action_label ?? '') === 'Secretary Decision';

                            $badgeClass = $isVerification ? 'badge-verification' : 'badge-decision';
                            $actionUrl = $isVerification
                                ? route('sekretariat.verifikasi.show', $proposal->id)
                                : route('sekretariat.decision.show', $proposal->id);

                            $actionText = $isVerification ? 'Periksa Dokumen' : 'Tetapkan Keputusan';
                            $actionIcon = $isVerification ? 'bi-file-earmark-check' : 'bi-check2-circle';
                        @endphp

                        <div class="priority-item">
                            <div class="priority-main">
                                <div class="priority-meta">
                                    <span class="protocol-code">
                                        {{ $proposal->protocol_number ?? 'PRO-' . $proposal->id }}
                                    </span>

                                    <span class="status-badge {{ $badgeClass }}">
                                        {{ $proposal->action_label }}
                                    </span>
                                </div>

                                <h3>{{ $proposal->title }}</h3>

                                <div class="priority-sub">
                                    <span>
                                        <i class="bi bi-person"></i>
                                        {{ $proposal->user->name ?? '-' }}
                                    </span>

                                    @if(!empty($proposal->deadline_display))
                                        <span>
                                            <i class="bi bi-calendar-event"></i>
                                            Deadline: {{ $proposal->deadline_display }}
                                        </span>
                                    @else
                                        <span>
                                            <i class="bi bi-clock"></i>
                                            Diperbarui {{ $proposal->updated_at?->translatedFormat('d M Y') ?? '-' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="priority-action">
                                <a href="{{ $actionUrl }}" class="btn-action {{ $isVerification ? 'btn-red' : 'btn-blue' }}">
                                    <i class="bi {{ $actionIcon }}"></i>
                                    {{ $actionText }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- REVIEW PROGRESS --}}
        <div class="kep-panel">
            <div class="panel-heading">
                <div>
                    <h2>Review Progress</h2>
                    <p>Pantau progres reviewer pada proposal yang sedang berjalan.</p>
                </div>

                <a href="{{ route('sekretariat.decision.index') }}" class="panel-link">
                    Lihat Decision
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @if($reviewProgress->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-clipboard-check"></i>
                    <h3>Tidak ada proses review</h3>
                    <p>Belum ada proposal yang sedang dalam proses review reviewer.</p>
                </div>
            @else
                <div class="review-list">
                    @foreach($reviewProgress as $item)
                        @php
                            $progressText = $item->progress ?? '0/0';
                            $progressParts = explode('/', $progressText);

                            $selesai = isset($progressParts[0]) ? (int) $progressParts[0] : 0;
                            $total = isset($progressParts[1]) ? (int) $progressParts[1] : 0;
                            $persen = $total > 0 ? min(100, round(($selesai / $total) * 100)) : 0;
                        @endphp

                        <div class="review-item">
                            <div class="review-top">
                                <div>
                                    <div class="review-code">
                                        PRO-{{ $item->id }}
                                    </div>

                                    <h3>{{ $item->judul ?? $item->title }}</h3>
                                </div>

                                <span class="progress-pill">
                                    {{ $progressText }} reviewer
                                </span>
                            </div>

                            <p class="review-status">
                                {{ $item->status_text ?? 'Menunggu progres reviewer.' }}
                            </p>

                            <div class="progress-track">
                                <div class="progress-bar" style="width: {{ $persen }}%"></div>
                            </div>

                            <div class="progress-footer">
                                <span>{{ $persen }}% selesai</span>
                                <span>{{ $selesai }} dari {{ $total }} reviewer</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
.sekretariat-dashboard,
.sekretariat-dashboard * {
    font-family: inherit;
}

/* HERO */
.secretariat-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 260px;
    gap: 1rem;
    padding: 1.35rem;
    margin-bottom: 1.25rem;
    border-radius: 22px;
    background:
        radial-gradient(circle at top left, rgba(220, 38, 38, .14), transparent 35%),
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

.secretariat-hero h1 {
    margin: 0;
    color: #111827;
    font-size: 1.6rem;
    font-weight: 900;
}

.secretariat-hero p {
    margin: .45rem 0 0;
    color: #6b7280;
    font-size: .9rem;
    line-height: 1.6;
    max-width: 760px;
}

.hero-side {
    background: #fff;
    border: 1px solid #fecaca;
    border-radius: 18px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.side-label {
    color: #6b7280;
    font-size: .78rem;
    font-weight: 900;
}

.side-number {
    color: #b91c1c;
    font-size: 2.5rem;
    font-weight: 900;
    line-height: 1;
    margin: .35rem 0;
}

.side-text {
    color: #6b7280;
    font-size: .78rem;
    line-height: 1.45;
}

/* STATS */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: .9rem;
    margin-bottom: 1rem;
}

.stat-card {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    padding: 1rem;
    border-radius: 18px;
    background: #fff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
    color: inherit;
    text-decoration: none;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

a.stat-card:hover {
    transform: translateY(-2px);
    border-color: #fecaca;
    box-shadow: 0 14px 28px rgba(15, 23, 42, .07);
}

.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-icon.red {
    background: #fee2e2;
    color: #b91c1c;
}

.stat-icon.orange {
    background: #fff7ed;
    color: #c2410c;
}

.stat-icon.blue {
    background: #eff6ff;
    color: #1d4ed8;
}

.stat-icon.green {
    background: #ecfdf5;
    color: #047857;
}

.stat-value {
    color: #111827;
    font-size: 1.6rem;
    font-weight: 900;
    line-height: 1;
}

.stat-label {
    margin-top: .28rem;
    color: #111827;
    font-size: .84rem;
    font-weight: 900;
}

.stat-desc {
    margin-top: .25rem;
    color: #6b7280;
    font-size: .74rem;
}

/* QUICK ACTION */
.quick-action-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .9rem;
    margin-bottom: 1.2rem;
}

.quick-action-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    min-height: 135px;
    padding: 1.15rem;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    text-decoration: none;
    color: inherit;
    overflow: hidden;
    transition: all .2s ease;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    border-color: #fecaca;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
}

.quick-action-card::after {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    right: -42px;
    bottom: -45px;
    border-radius: 999px;
    background: rgba(220, 38, 38, .08);
}

.quick-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: #fee2e2;
    color: #b91c1c;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.55rem;
    flex-shrink: 0;
}

.decision-action .quick-icon {
    background: #eff6ff;
    color: #1d4ed8;
}

.review-action .quick-icon {
    background: #fff7ed;
    color: #c2410c;
}

.quick-content {
    min-width: 0;
    position: relative;
    z-index: 1;
}

.quick-label {
    display: block;
    font-size: .84rem;
    font-weight: 900;
    color: #111827;
    margin-bottom: .2rem;
}

.quick-content strong {
    display: block;
    font-size: 2rem;
    line-height: 1;
    color: #111827;
    margin-bottom: .4rem;
}

.quick-content p {
    margin: 0;
    color: #6b7280;
    font-size: .8rem;
    line-height: 1.45;
}

.quick-arrow {
    margin-left: auto;
    color: #b91c1c;
    position: relative;
    z-index: 1;
}

.quick-arrow.muted {
    color: #c2410c;
}

/* MAIN GRID */
.dashboard-main-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 430px;
    gap: 1.2rem;
}

.kep-panel {
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
    gap: .8rem;
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
    font-size: .8rem;
    line-height: 1.45;
}

.panel-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: #b91c1c;
    font-size: .8rem;
    font-weight: 900;
    text-decoration: none;
    white-space: nowrap;
}

/* PRIORITY */
.priority-list,
.review-list {
    padding: .85rem;
    display: flex;
    flex-direction: column;
    gap: .85rem;
}

.priority-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 1rem;
    background: #fff;
    transition: all .2s ease;
}

.priority-item:hover {
    background: #fffafa;
    border-color: #fecaca;
}

.priority-main {
    min-width: 0;
    flex: 1;
}

.priority-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .45rem;
    margin-bottom: .45rem;
}

.protocol-code {
    color: #6b7280;
    font-size: .74rem;
    font-weight: 900;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: .28rem .65rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 900;
    white-space: nowrap;
}

.badge-verification {
    background: #fee2e2;
    color: #991b1b;
}

.badge-decision {
    background: #eff6ff;
    color: #1d4ed8;
}

.priority-item h3 {
    margin: 0;
    color: #111827;
    font-size: .96rem;
    font-weight: 900;
    line-height: 1.45;
}

.priority-sub {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem;
    margin-top: .5rem;
    color: #6b7280;
    font-size: .78rem;
}

.priority-sub span {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}

.priority-action {
    flex-shrink: 0;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    padding: .6rem .9rem;
    border-radius: 12px;
    color: #fff;
    font-size: .78rem;
    font-weight: 900;
    text-decoration: none;
    transition: all .2s ease;
    white-space: nowrap;
}

.btn-action:hover {
    color: #fff;
    transform: translateY(-1px);
}

.btn-red {
    background: #dc2626;
}

.btn-red:hover {
    background: #b91c1c;
}

.btn-blue {
    background: #2563eb;
}

.btn-blue:hover {
    background: #1d4ed8;
}

/* REVIEW */
.review-item {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 1rem;
    background: #fff;
}

.review-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .8rem;
}

.review-code {
    color: #6b7280;
    font-size: .74rem;
    font-weight: 900;
    margin-bottom: .25rem;
}

.review-item h3 {
    margin: 0;
    color: #111827;
    font-size: .92rem;
    font-weight: 900;
    line-height: 1.45;
}

.progress-pill {
    background: #fff7ed;
    color: #c2410c;
    padding: .3rem .65rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 900;
    white-space: nowrap;
}

.review-status {
    margin: .6rem 0 .75rem;
    color: #6b7280;
    font-size: .8rem;
    line-height: 1.45;
}

.progress-track {
    width: 100%;
    height: 8px;
    background: #f3f4f6;
    border-radius: 999px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #dc2626, #f97316);
    border-radius: 999px;
}

.progress-footer {
    display: flex;
    justify-content: space-between;
    gap: .8rem;
    margin-top: .45rem;
    color: #6b7280;
    font-size: .74rem;
    font-weight: 700;
}

/* EMPTY */
.empty-state {
    padding: 2.5rem 1rem;
    text-align: center;
    color: #6b7280;
}

.empty-state i {
    display: block;
    font-size: 2.2rem;
    color: #d1d5db;
    margin-bottom: .55rem;
}

.empty-state h3 {
    margin: 0;
    color: #111827;
    font-size: 1rem;
    font-weight: 900;
}

.empty-state p {
    margin: .35rem 0 0;
    color: #6b7280;
    font-size: .84rem;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
    .secretariat-hero,
    .dashboard-main-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .quick-action-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .secretariat-hero {
        padding: 1rem;
        border-radius: 18px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .quick-action-card {
        align-items: flex-start;
    }

    .panel-heading {
        flex-direction: column;
    }

    .priority-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .priority-action,
    .btn-action {
        width: 100%;
    }

    .review-top {
        flex-direction: column;
    }

    .progress-footer {
        flex-direction: column;
        gap: .25rem;
    }
}

@media (max-width: 480px) {
    .secretariat-hero h1 {
        font-size: 1.35rem;
    }

    .quick-action-card {
        padding: .9rem;
    }

    .quick-icon {
        width: 46px;
        height: 46px;
        font-size: 1.3rem;
    }
}
</style>
@endpush