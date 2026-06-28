@extends('layouts.reviewer')
@section('title', 'Tugas Review')

@section('content')

@php
    $deadlineOptions = [
        'all' => 'Semua Deadline',
        'overdue' => 'Lewat Deadline',
        'near' => 'Mendekati Deadline',
        'normal' => 'Deadline Aman',
    ];

    $perPageOptions = [5, 10, 15, 25];

    $sections = [
        [
            'title' => 'Review Awal',
            'subtitle' => 'Proposal baru yang perlu Anda review pertama kali.',
            'icon' => 'bi-clipboard-check',
            'items' => $initialAssignments,
            'empty_icon' => 'bi-inbox',
            'empty_text' => 'Belum ada tugas review awal.',
            'action_label' => 'Mulai Review',
            'action_icon' => 'bi-pencil-square',
            'badge_class' => 'badge-review',
            'status_text' => 'Menunggu Review',
        ],
        [
            'title' => 'Tinjauan Revisi',
            'subtitle' => 'Dokumen revisi dari peneliti yang perlu Anda telaah kembali.',
            'icon' => 'bi-arrow-repeat',
            'items' => $revisionAssignments,
            'empty_icon' => 'bi-folder2-open',
            'empty_text' => 'Belum ada tugas tinjauan revisi.',
            'action_label' => 'Tinjau Revisi',
            'action_icon' => 'bi-arrow-repeat',
            'badge_class' => 'badge-revision',
            'status_text' => 'Menunggu Tinjauan Revisi',
        ],
    ];

    $totalTasks = $initialAssignments->total() + $revisionAssignments->total();
@endphp

<div class="page-header">
    <h1>Tugas Review</h1>
    <p>Daftar protokol penelitian yang ditugaskan kepada Anda untuk direview</p>
</div>

<div class="kep-card filter-card">
    <form method="GET" action="{{ route('reviewer.tugas.index') }}" class="review-filter-form">
        <div class="filter-group filter-search">
            <label for="search">Cari Tugas</label>
            <input
                type="text"
                id="search"
                name="search"
                value="{{ $filters['search'] ?? '' }}"
                placeholder="Cari nomor, judul, peneliti, atau email..."
                class="filter-control"
            >
        </div>

        <div class="filter-group">
            <label for="deadline">Deadline</label>
            <select id="deadline" name="deadline" class="filter-control">
                @foreach($deadlineOptions as $value => $label)
                    <option value="{{ $value }}" {{ ($filters['deadline'] ?? 'all') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label for="per_page">Tampil</label>
            <select id="per_page" name="per_page" class="filter-control">
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}" {{ (int) ($filters['per_page'] ?? 5) === $option ? 'selected' : '' }}>
                        {{ $option }} data
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-kep btn-primary">
                <i class="bi bi-search"></i> Filter
            </button>

            <a href="{{ route('reviewer.tugas.index') }}" class="btn-kep btn-outline">
                <i class="bi bi-x-circle"></i> Reset
            </a>
        </div>
    </form>
</div>

<div class="task-summary-grid">
    <div class="task-summary-card">
        <span class="summary-label">Total Tugas Pending</span>
        <strong>{{ $totalTasks }}</strong>
    </div>

    <div class="task-summary-card">
        <span class="summary-label">Review Awal</span>
        <strong>{{ $initialAssignments->total() }}</strong>
    </div>

    <div class="task-summary-card">
        <span class="summary-label">Tinjauan Revisi</span>
        <strong>{{ $revisionAssignments->total() }}</strong>
    </div>
</div>

@foreach($sections as $section)
    @php
        $assignments = $section['items'];
    @endphp

    <div class="kep-card task-section-card">
        <div class="kep-card-title section-title-wrap">
            <div class="section-title-left">
                <span class="section-icon">
                    <i class="bi {{ $section['icon'] }}"></i>
                </span>

                <div>
                    <div class="section-title-main">
                        {{ $section['title'] }}
                        <span class="section-count">{{ $assignments->total() }}</span>
                    </div>
                    <div class="section-subtitle">
                        {{ $section['subtitle'] }}
                    </div>
                </div>
            </div>
        </div>

        @if($assignments->isEmpty())
            <div class="empty-reviewer-state">
                <i class="bi {{ $section['empty_icon'] }}"></i>
                <p>{{ $section['empty_text'] }}</p>
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
                                <td data-label="No">
                                    {{ $assignments->firstItem() + $index }}
                                </td>

                                <td data-label="No. Registrasi">
                                    <span class="protocol-reg">
                                        {{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }}
                                    </span>
                                </td>

                                <td data-label="Judul Penelitian">
                                    <div class="review-title">
                                        {{ $protocol->title ?? '-' }}
                                    </div>
                                    <div class="review-sub">
                                        Program Studi: {{ $protocol->program_studi ?? '-' }}
                                    </div>
                                </td>

                                <td data-label="Peneliti">
                                    <div class="review-name">
                                        {{ $protocol->user->name ?? '-' }}
                                    </div>
                                    <div class="review-sub">
                                        {{ $protocol->user->email ?? '-' }}
                                    </div>
                                </td>

                                <td data-label="Deadline">
                                    @if($deadline)
                                        <div class="{{ $isOverdue ? 'deadline-danger' : ($isNearDeadline ? 'deadline-warning' : '') }}">
                                            {{ $deadline->translatedFormat('d M Y') }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td data-label="Status">
                                    @if($isOverdue)
                                        <span class="kep-badge badge-rejected">Lewat Deadline</span>
                                    @elseif($isNearDeadline)
                                        <span class="kep-badge badge-revision">Mendekati Deadline</span>
                                    @else
                                        <span class="kep-badge {{ $section['badge_class'] }}">
                                            {{ $section['status_text'] }}
                                        </span>
                                    @endif
                                </td>

                                <td data-label="Aksi" class="action-cell">
                                    @if($isOverdue)
                                        <button type="button"
                                                class="btn-kep btn-disabled btn-action-task"
                                                disabled
                                                title="Deadline review sudah lewat">
                                            <i class="bi bi-lock"></i> Deadline Berakhir
                                        </button>
                                    @else
                                        <a href="{{ route('reviewer.tugas.show', $assignment->id) }}"
                                        class="btn-kep btn-primary btn-action-task">
                                            <i class="bi {{ $section['action_icon'] }}"></i>
                                            {{ $section['action_label'] }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
                <div class="review-pagination">
                    <div class="pagination-info">
                        Menampilkan
                        <strong>{{ $assignments->firstItem() }}</strong>
                        –
                        <strong>{{ $assignments->lastItem() }}</strong>
                        dari
                        <strong>{{ $assignments->total() }}</strong>
                        data
                    </div>

                    <div class="pagination-actions">
                        @if($assignments->onFirstPage())
                            <span class="pg-btn disabled">
                                <i class="bi bi-chevron-left"></i> Previous
                            </span>
                        @else
                            <a href="{{ $assignments->previousPageUrl() }}" class="pg-btn">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        @endif

                        <span class="pg-current">
                            Halaman {{ $assignments->currentPage() }} / {{ $assignments->lastPage() }}
                        </span>

                        @if($assignments->hasMorePages())
                            <a href="{{ $assignments->nextPageUrl() }}" class="pg-btn">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span class="pg-btn disabled">
                                Next <i class="bi bi-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
@endforeach

@endsection

@push('styles')
<style>
.filter-card {
    margin-bottom: 1rem;
}

.review-filter-form {
    display: grid;
    grid-template-columns: minmax(240px, 1fr) 180px 140px auto;
    gap: .85rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: .35rem;
}

.filter-group label {
    font-size: .78rem;
    font-weight: 700;
    color: var(--navy-deep);
}

.filter-control {
    width: 100%;
    height: 40px;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 0 .8rem;
    font-size: .85rem;
    color: var(--navy-deep);
    background: #fff;
    outline: none;
}

.filter-control:focus {
    border-color: var(--blue-accent);
    box-shadow: 0 0 0 3px rgba(74,127,167,.12);
}

.filter-actions {
    display: flex;
    gap: .5rem;
    justify-content: flex-end;
}

.task-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .85rem;
    margin-bottom: 1rem;
}

.task-summary-card {
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.summary-label {
    font-size: .82rem;
    color: var(--text-muted);
    font-weight: 600;
}

.task-summary-card strong {
    font-size: 1.35rem;
    color: var(--navy-deep);
}

.task-section-card {
    margin-bottom: 1.2rem;
}

.section-title-wrap {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.section-title-left {
    display: flex;
    align-items: center;
    gap: .75rem;
}

.section-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    display: flex;
    align-items: center;
    justify-content: center;
}

.section-title-main {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-weight: 700;
    color: var(--navy-deep);
}

.section-count {
    min-width: 26px;
    height: 22px;
    padding: 0 .45rem;
    border-radius: 999px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    font-size: .78rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.section-subtitle {
    margin-top: .15rem;
    font-size: .8rem;
    color: var(--text-muted);
    font-weight: 500;
}

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

.action-cell {
    text-align: right;
}

.btn-action-task {
    font-size: .8rem;
    padding: .45rem .75rem;
    white-space: nowrap;
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

.review-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.pagination-info {
    font-size: .82rem;
    color: var(--text-muted);
}

.pagination-actions {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
}

.pg-btn,
.pg-current {
    min-height: 34px;
    padding: .45rem .75rem;
    border-radius: 9px;
    font-size: .8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    text-decoration: none;
}

.pg-btn {
    border: 1px solid var(--border);
    background: #fff;
    color: var(--navy-deep);
}

.pg-btn:hover {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.pg-btn.disabled {
    opacity: .45;
    cursor: not-allowed;
}

.pg-current {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

@media (max-width: 960px) {
    .review-filter-form {
        grid-template-columns: 1fr 1fr;
    }

    .filter-search {
        grid-column: 1 / -1;
    }

    .filter-actions {
        justify-content: flex-start;
    }

    .task-summary-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .review-filter-form {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        flex-direction: column;
    }

    .filter-actions .btn-kep {
        width: 100%;
        justify-content: center;
    }

    .review-table-wrap {
        overflow-x: visible;
    }

    .review-table {
        min-width: 0;
        border-collapse: separate;
        border-spacing: 0 .85rem;
    }

    .review-table thead {
        display: none;
    }

    .review-table,
    .review-table tbody,
    .review-table tr,
    .review-table td {
        display: block;
        width: 100%;
    }

    .review-table tr {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: .85rem;
        background: #fff;
    }

    .review-table tr:hover td {
        background: transparent;
    }

    .review-table td {
        border-bottom: none;
        padding: .55rem 0;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        text-align: right;
    }

    .review-table td::before {
        content: attr(data-label);
        font-weight: 700;
        color: var(--text-muted);
        text-align: left;
        flex-shrink: 0;
    }

    .review-table td[data-label="Judul Penelitian"],
    .review-table td[data-label="Peneliti"] {
        display: block;
        text-align: left;
    }

    .review-table td[data-label="Judul Penelitian"]::before,
    .review-table td[data-label="Peneliti"]::before {
        display: block;
        margin-bottom: .35rem;
    }

    .review-title {
        max-width: none;
    }

    .action-cell {
        display: block !important;
        text-align: left !important;
        padding-top: .75rem !important;
    }

    .action-cell::before {
        display: none;
    }

    .btn-action-task {
        width: 100%;
        justify-content: center;
    }

    .review-pagination {
        align-items: stretch;
    }

    .pagination-actions {
        width: 100%;
        justify-content: space-between;
    }

    .pg-current {
        flex: 1;
        justify-content: center;
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
}
</style>
@endpush