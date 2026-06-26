@extends('layouts.reviewer')
@section('title', 'Tugas Review')

@section('content')

<div class="page-header">
    <h1>Tugas Review</h1>
    <p>Daftar protokol penelitian yang ditugaskan kepada Anda untuk direview</p>
</div>

<div class="kep-card">
    <div class="kep-card-title" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;">
        <span>
            <i class="bi bi-clipboard-check"></i> Daftar Tugas Review
        </span>

        <span style="font-size:.85rem;color:var(--text-muted);font-weight:500;">
            Total: {{ $assignments->total() }} tugas
        </span>
    </div>

    @if($assignments->isEmpty())
        <div class="empty-reviewer-state">
            <i class="bi bi-inbox"></i>
            <p>Belum ada tugas review pending.</p>
        </div>
    @else
        <div class="review-table-wrap">
            <table class="review-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Registrasi</th>
                        <th>Judul Penelitian</th>
                        <th>Peneliti</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($assignments as $index => $assignment)
                        @php
                            $protocol = $assignment->protocol;
                            $deadline = $assignment->deadline
                                ? \Carbon\Carbon::parse($assignment->deadline)
                                : null;

                            $isOverdue = $deadline && $deadline->isPast() && !$deadline->isToday();
                            $isNearDeadline = $deadline && !$isOverdue && $deadline->diffInDays(now()) <= 3;
                        @endphp

                        <tr>
                            <td>
                                {{ $assignments->firstItem() + $index }}
                            </td>

                            <td>
                                <span class="protocol-reg">
                                    {{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }}
                                </span>
                            </td>

                            <td>
                                <div class="review-title">
                                    {{ $protocol->title ?? '-' }}
                                </div>
                                <div class="review-sub">
                                    Program Studi: {{ $protocol->program_studi ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="review-name">
                                    {{ $protocol->user->name ?? '-' }}
                                </div>
                                <div class="review-sub">
                                    {{ $protocol->user->email ?? '-' }}
                                </div>
                            </td>

                            <td>
                                @if($deadline)
                                    <div class="{{ $isOverdue ? 'deadline-danger' : ($isNearDeadline ? 'deadline-warning' : '') }}">
                                        {{ $deadline->translatedFormat('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @if($isOverdue)
                                    <span class="kep-badge badge-rejected">Lewat Deadline</span>
                                @elseif($isNearDeadline)
                                    <span class="kep-badge badge-revision">Mendekati Deadline</span>
                                @else
                                    <span class="kep-badge badge-review">Pending</span>
                                @endif
                            </td>

                            <td style="text-align:right;">
                                <a href="{{ route('reviewer.tugas.show', $assignment->id) }}"
                                   class="btn-kep btn-primary"
                                   style="font-size:.8rem;padding:.45rem .75rem;">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div style="margin-top:1.2rem;">
                {{ $assignments->links() }}
            </div>
        @endif
    @endif
</div>

@endsection

@push('styles')
<style>
.review-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.review-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.review-table th {
    text-align: left;
    font-size: .78rem;
    color: var(--text-muted);
    font-weight: 600;
    padding: .75rem .85rem;
    border-bottom: 1px solid var(--border);
    background: var(--blue-pale);
    white-space: nowrap;
}

.review-table td {
    padding: .85rem;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
    font-size: .85rem;
}

.review-table tr:hover td {
    background: #f8fbfd;
}

.review-title {
    font-weight: 600;
    color: var(--navy-deep);
    line-height: 1.35;
    max-width: 360px;
}

.review-name {
    font-weight: 600;
    color: var(--navy-deep);
    line-height: 1.35;
}

.review-sub {
    margin-top: .2rem;
    font-size: .76rem;
    color: var(--text-muted);
}

.deadline-danger {
    color: #991b1b;
    font-weight: 700;
}

.deadline-warning {
    color: #9a3412;
    font-weight: 700;
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
</style>
@endpush