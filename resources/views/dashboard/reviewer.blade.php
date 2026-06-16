@extends('layouts.reviewer')
@section('title', 'Dashboard Reviewer')

@section('content')

<div class="page-header">
    <h1>Dashboard Reviewer</h1>
    <p>Ringkasan tugas review protokol penelitian Anda</p>
</div>

{{-- SUMMARY CARDS --}}
<div class="reviewer-summary-grid">
    <div class="kep-card reviewer-stat-card">
        <div class="kep-card-title">
            <i class="bi bi-hourglass-split"></i> Tugas Pending
        </div>
        <div class="stat-number">{{ $pendingCount ?? 0 }}</div>
        <p class="stat-desc">Belum direview</p>
    </div>

    <div class="kep-card reviewer-stat-card">
        <div class="kep-card-title">
            <i class="bi bi-check2-circle"></i> Review Selesai
        </div>
        <div class="stat-number">{{ $doneCount ?? 0 }}</div>
        <p class="stat-desc">Sudah disubmit</p>
    </div>

    <div class="kep-card reviewer-stat-card">
        <div class="kep-card-title">
            <i class="bi bi-exclamation-triangle"></i> Mendekati Deadline
        </div>
        <div class="stat-number">{{ $nearDeadlineCount ?? 0 }}</div>
        <p class="stat-desc">Deadline kurang dari atau sama dengan 3 hari</p>
    </div>
</div>

{{-- TUGAS TERBARU --}}
<div class="kep-card">
    <div class="kep-card-title" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;">
        <span>
            <i class="bi bi-list-task"></i> Tugas Review Terbaru
        </span>

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
        <div class="assignment-list">
            @foreach($latestAssignments as $assignment)
                @php
                    $deadline = $assignment->deadline
                        ? \Carbon\Carbon::parse($assignment->deadline)
                        : null;

                    $isOverdue = $deadline && $deadline->isPast() && !$deadline->isToday();
                    $isNearDeadline = $deadline && !$isOverdue && $deadline->diffInDays(now()) <= 3;
                @endphp

                <div class="assignment-item">
                    <div style="flex:1;min-width:0;">
                        <div class="assignment-meta">
                            <span class="protocol-reg">
                                {{ $assignment->protocol->nomor_registrasi ?? 'PRO-' . $assignment->protocol_id }}
                            </span>

                            @if($isOverdue)
                                <span class="kep-badge badge-rejected">Lewat Deadline</span>
                            @elseif($isNearDeadline)
                                <span class="kep-badge badge-revision">Mendekati Deadline</span>
                            @else
                                <span class="kep-badge badge-review">Pending</span>
                            @endif
                        </div>

                        <div class="assignment-title">
                            {{ $assignment->protocol->title ?? '-' }}
                        </div>

                        <div class="assignment-sub">
                            <span>
                                <i class="bi bi-person"></i>
                                {{ $assignment->protocol->user->name ?? '-' }}
                            </span>
                            <span>·</span>
                            <span>
                                <i class="bi bi-calendar-event"></i>
                                Deadline:
                                {{ $deadline ? $deadline->translatedFormat('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    <div style="flex-shrink:0;">
                        <a href="{{ route('reviewer.tugas.show', $assignment->id) }}"
                           class="btn-kep btn-primary"
                           style="font-size:.82rem;">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
.reviewer-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.reviewer-stat-card .stat-number {
    font-size: 2.1rem;
    font-weight: 700;
    color: var(--navy-deep);
    line-height: 1.1;
    margin-top: .4rem;
}

.reviewer-stat-card .stat-desc {
    margin-top: .35rem;
    font-size: .85rem;
    color: var(--text-muted);
}

.assignment-list {
    display: flex;
    flex-direction: column;
    gap: .85rem;
}

.assignment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .95rem 1rem;
    background: var(--white);
    transition: all var(--transition);
}

.assignment-item:hover {
    border-color: var(--blue-light);
    background: var(--blue-pale);
}

.assignment-meta {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .45rem;
    flex-wrap: wrap;
}

.assignment-title {
    font-size: .98rem;
    font-weight: 600;
    color: var(--navy-deep);
    line-height: 1.35;
    margin-bottom: .45rem;
}

.assignment-sub {
    display: flex;
    gap: .5rem;
    align-items: center;
    flex-wrap: wrap;
    font-size: .8rem;
    color: var(--text-muted);
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

@media (max-width: 900px) {
    .reviewer-summary-grid {
        grid-template-columns: 1fr;
    }

    .assignment-item {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
@endpush