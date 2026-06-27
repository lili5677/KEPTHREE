@extends('layouts.reviewer')
@section('title', 'Dashboard Reviewer')

@section('content')

@php
    $pendingCount = $pendingCount ?? 0;
    $doneCount = $doneCount ?? 0;
    $initialPendingCount = $initialPendingCount ?? 0;
    $revisionPendingCount = $revisionPendingCount ?? 0;
    $nearDeadlineCount = $nearDeadlineCount ?? 0;
    $overdueCount = $overdueCount ?? 0;
@endphp

<div class="page-header">
    <h1>Dashboard Reviewer</h1>
    <p>Pusat aksi cepat untuk mengelola tugas review protokol penelitian Anda.</p>
</div>

{{-- AKSI CEPAT --}}
<div class="quick-action-grid">
    <a href="{{ route('reviewer.tugas.index') }}"
       class="quick-action-card primary-action">
        <div class="quick-icon">
            <i class="bi bi-pencil-square"></i>
        </div>

        <div class="quick-content">
            <span class="quick-label">Mulai Review Awal</span>
            <strong>{{ $initialPendingCount }}</strong>
            <p>Proposal baru yang perlu direview pertama kali.</p>
        </div>

        <div class="quick-arrow">
            <i class="bi bi-arrow-right"></i>
        </div>
    </a>

    <a href="{{ route('reviewer.tugas.index') }}"
       class="quick-action-card revision-action">
        <div class="quick-icon">
            <i class="bi bi-arrow-repeat"></i>
        </div>

        <div class="quick-content">
            <span class="quick-label">Tinjau Revisi</span>
            <strong>{{ $revisionPendingCount }}</strong>
            <p>Hasil revisi peneliti yang perlu ditelaah kembali.</p>
        </div>

        <div class="quick-arrow">
            <i class="bi bi-arrow-right"></i>
        </div>
    </a>

    <a href="{{ route('reviewer.riwayat') }}"
       class="quick-action-card history-action">
        <div class="quick-icon">
            <i class="bi bi-clock-history"></i>
        </div>

        <div class="quick-content">
            <span class="quick-label">Riwayat Review</span>
            <strong>{{ $doneCount }}</strong>
            <p>Lihat hasil review yang sudah pernah disubmit.</p>
        </div>

        <div class="quick-arrow">
            <i class="bi bi-arrow-right"></i>
        </div>
    </a>
</div>

{{-- STATUS RINGKAS --}}
<div class="reviewer-summary-grid">
    <div class="mini-stat-card">
        <div>
            <span>Total Pending</span>
            <strong>{{ $pendingCount }}</strong>
        </div>
        <i class="bi bi-hourglass-split"></i>
    </div>

    <div class="mini-stat-card">
        <div>
            <span>Review Awal</span>
            <strong>{{ $initialPendingCount }}</strong>
        </div>
        <i class="bi bi-clipboard-check"></i>
    </div>

    <div class="mini-stat-card">
        <div>
            <span>Tinjauan Revisi</span>
            <strong>{{ $revisionPendingCount }}</strong>
        </div>
        <i class="bi bi-arrow-repeat"></i>
    </div>

    <div class="mini-stat-card warning-stat">
        <div>
            <span>Dekat Deadline</span>
            <strong>{{ $nearDeadlineCount }}</strong>
        </div>
        <i class="bi bi-exclamation-triangle"></i>
    </div>

    <div class="mini-stat-card danger-stat">
        <div>
            <span>Deadline Berakhir</span>
            <strong>{{ $overdueCount }}</strong>
        </div>
        <i class="bi bi-lock-fill"></i>
    </div>
</div>

{{-- PRIORITAS TERDEKAT --}}
<div class="kep-card priority-card">
    <div class="kep-card-title priority-header">
        <div>
            <span>
                <i class="bi bi-lightning-charge"></i> Prioritas Terdekat
            </span>
            <p>Tugas pending yang perlu diperhatikan berdasarkan deadline terdekat.</p>
        </div>

        <a href="{{ route('reviewer.tugas.index') }}" class="btn-kep btn-outline" style="font-size:.82rem;">
            Lihat Semua
        </a>
    </div>

    @if(($latestAssignments ?? collect())->isEmpty())
        <div class="empty-reviewer-state">
            <i class="bi bi-inbox"></i>
            <p>Belum ada tugas review pending.</p>
        </div>
    @else
        <div class="priority-list">
            @foreach($latestAssignments as $assignment)
                @php
                    $deadline = $assignment->deadline
                        ? \Carbon\Carbon::parse($assignment->deadline)
                        : null;

                    $isOverdue = $deadline && $deadline->isPast() && !$deadline->isToday();
                    $isNearDeadline = $deadline && !$isOverdue && $deadline->diffInDays(now()) <= 3;

                    $round = (int) ($assignment->round ?? 1);
                    $isRevision = $round > 1;

                    $stageLabel = $isRevision
                        ? 'Tinjauan Revisi ke-' . $round
                        : 'Review Awal';

                    $actionLabel = $isRevision
                        ? 'Tinjau Revisi'
                        : 'Mulai Review';

                    $actionIcon = $isRevision
                        ? 'bi-arrow-repeat'
                        : 'bi-pencil-square';
                @endphp

                <div class="priority-item {{ $isOverdue ? 'locked-item' : '' }}">
                    <div class="priority-main">
                        <div class="priority-meta">
                            <span class="protocol-reg">
                                {{ $assignment->protocol->nomor_registrasi ?? 'PRO-' . $assignment->protocol_id }}
                            </span>

                            <span class="kep-badge {{ $isRevision ? 'badge-revision' : 'badge-review' }}">
                                {{ $stageLabel }}
                            </span>

                            @if($isOverdue)
                                <span class="kep-badge badge-rejected">Deadline Berakhir</span>
                            @elseif($isNearDeadline)
                                <span class="kep-badge badge-revision">Mendekati Deadline</span>
                            @else
                                <span class="kep-badge badge-review">Pending</span>
                            @endif
                        </div>

                        <div class="priority-title">
                            {{ $assignment->protocol->title ?? '-' }}
                        </div>

                        <div class="priority-sub">
                            <span>
                                <i class="bi bi-person"></i>
                                {{ $assignment->protocol->user->name ?? '-' }}
                            </span>

                            <span>
                                <i class="bi bi-calendar-event"></i>
                                Deadline:
                                {{ $deadline ? $deadline->translatedFormat('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="priority-action">
                        @if($isOverdue)
                            <button type="button"
                                    class="btn-kep btn-disabled"
                                    disabled
                                    title="Deadline review sudah lewat">
                                <i class="bi bi-lock"></i> Terkunci
                            </button>
                        @else
                            <a href="{{ route('reviewer.tugas.show', $assignment->id) }}"
                               class="btn-kep btn-primary">
                                <i class="bi {{ $actionIcon }}"></i> {{ $actionLabel }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
.quick-action-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.quick-action-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    min-height: 140px;
    padding: 1.15rem;
    border: 1px solid var(--border);
    border-radius: 18px;
    background: #fff;
    text-decoration: none;
    color: inherit;
    overflow: hidden;
    transition: all var(--transition);
}

.quick-action-card:hover {
    transform: translateY(-2px);
    border-color: var(--blue-light);
    box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
}

.quick-action-card::after {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    right: -40px;
    bottom: -45px;
    border-radius: 999px;
    background: rgba(74, 127, 167, .08);
}

.quick-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.55rem;
    flex-shrink: 0;
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.revision-action .quick-icon {
    background: #ffedd5;
    color: #c2410c;
}

.history-action .quick-icon {
    background: #ecfdf5;
    color: #047857;
}

.quick-content {
    min-width: 0;
    position: relative;
    z-index: 1;
}

.quick-label {
    display: block;
    font-size: .86rem;
    font-weight: 700;
    color: var(--navy-deep);
    margin-bottom: .2rem;
}

.quick-content strong {
    display: block;
    font-size: 2rem;
    line-height: 1;
    color: var(--navy-deep);
    margin-bottom: .4rem;
}

.quick-content p {
    font-size: .8rem;
    color: var(--text-muted);
    line-height: 1.45;
    margin: 0;
}

.quick-arrow {
    margin-left: auto;
    color: var(--blue-accent);
    position: relative;
    z-index: 1;
}

.reviewer-summary-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: .85rem;
    margin-bottom: 1.2rem;
}

.mini-stat-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: #fff;
}

.mini-stat-card span {
    display: block;
    font-size: .78rem;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: .25rem;
}

.mini-stat-card strong {
    display: block;
    font-size: 1.55rem;
    line-height: 1;
    color: var(--navy-deep);
}

.mini-stat-card i {
    font-size: 1.35rem;
    color: var(--blue-accent);
}

.warning-stat i {
    color: #c2410c;
}

.danger-stat i {
    color: #991b1b;
}

.priority-card {
    margin-top: .5rem;
}

.priority-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.priority-header p {
    margin-top: .25rem;
    font-size: .8rem;
    color: var(--text-muted);
    font-weight: 500;
}

.priority-list {
    display: flex;
    flex-direction: column;
    gap: .85rem;
}

.priority-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1rem;
    background: #fff;
    transition: all var(--transition);
}

.priority-item:hover {
    border-color: var(--blue-light);
    background: #f8fbfd;
}

.locked-item {
    background: #f9fafb;
    opacity: .9;
}

.priority-main {
    flex: 1;
    min-width: 0;
}

.priority-meta {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
    margin-bottom: .45rem;
}

.priority-title {
    font-size: .96rem;
    font-weight: 700;
    color: var(--navy-deep);
    line-height: 1.4;
    margin-bottom: .45rem;
}

.priority-sub {
    display: flex;
    gap: .85rem;
    align-items: center;
    flex-wrap: wrap;
    font-size: .8rem;
    color: var(--text-muted);
}

.priority-action {
    flex-shrink: 0;
}

.btn-disabled {
    background: #e5e7eb;
    color: #6b7280;
    border: 1px solid #d1d5db;
    cursor: not-allowed;
    opacity: .85;
}

.btn-disabled:hover {
    background: #e5e7eb;
    color: #6b7280;
}

.empty-reviewer-state {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--text-muted);
}

.empty-reviewer-state i {
    display: block;
    font-size: 2rem;
    margin-bottom: .6rem;
    color: var(--blue-light);
}

@media (max-width: 1100px) {
    .quick-action-grid {
        grid-template-columns: 1fr;
    }

    .reviewer-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .reviewer-summary-grid {
        grid-template-columns: 1fr;
    }

    .quick-action-card {
        align-items: flex-start;
    }

    .priority-header {
        flex-direction: column;
    }

    .priority-item {
        align-items: flex-start;
        flex-direction: column;
    }

    .priority-action {
        width: 100%;
    }

    .priority-action .btn-kep {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush