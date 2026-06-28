@extends('layouts.sekretariat')

@section('title', 'Manajemen Reviewer – Sistem KEP')

@section('content')
@php
    $protocols = $protocols ?? collect();
@endphp

<div class="review-management-page">

    {{-- HEADER --}}
    <div class="review-hero">
        <div>
            <div class="hero-badge">
                <i class="bi bi-people"></i>
                Manajemen Reviewer
            </div>

            <h1>Manajemen Reviewer</h1>

            <p>
                Pantau reviewer yang sedang ditugaskan pada proposal dan kelola deadline review.
                Reviewer hanya dapat diubah sebelum deadline, sedangkan deadline tetap dapat diperbarui.
            </p>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="kep-alert success-alert">
            <i class="bi bi-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="kep-alert error-alert">
            <i class="bi bi-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- TABLE / LIST --}}
    <div class="review-panel">
        <div class="panel-heading">
            <div>
                <h2>Daftar Proposal Dalam Review</h2>
                <p>Proposal yang sedang ditugaskan kepada reviewer.</p>
            </div>
        </div>

        @if($protocols->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>

                <h3>Belum Ada Proposal Dalam Review</h3>
                <p>Belum ada proposal yang sedang ditugaskan kepada reviewer.</p>
            </div>
        @else
            {{-- DESKTOP TABLE --}}
            <div class="desktop-table-wrap">
                <table class="review-table">
                    <thead>
                        <tr>
                            <th>Proposal</th>
                            <th>Jenis Review</th>
                            <th>Reviewer</th>
                            <th>Deadline</th>
                            <th>Progress</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($protocols as $protocol)
                            @php
                                $deadline = $protocol->review_deadline
                                    ? \Carbon\Carbon::parse($protocol->review_deadline)
                                    : null;

                                $deadlinePassed = $protocol->deadline_passed ?? false;

                                $reviewType = $protocol->verification->review_type ?? '-';

                                $reviewTypeLabel = match($reviewType) {
                                    'expedited' => 'Expedited',
                                    'full_board' => 'Full Board',
                                    'exempted' => 'Exempted',
                                    default => ucfirst(str_replace('_', ' ', $reviewType)),
                                };

                                $assignments = $protocol->assigned_reviewers ?? collect();
                            @endphp

                            <tr>
                                <td>
                                    <div class="proposal-info">
                                        <div class="proposal-code">
                                            {{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }}
                                        </div>

                                        <div class="proposal-title">
                                            {{ $protocol->title }}
                                        </div>

                                        <div class="proposal-author">
                                            <i class="bi bi-person"></i>
                                            {{ $protocol->user->name ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="type-badge">
                                        {{ $reviewTypeLabel }}
                                    </span>
                                </td>

                                <td>
                                    <div class="reviewer-list">
                                        @forelse($assignments as $assignment)
                                            <div class="reviewer-item">
                                                <span class="reviewer-name">
                                                    {{ $assignment->reviewer->name ?? '-' }}
                                                </span>

                                                <span class="reviewer-status {{ $assignment->status === 'done' ? 'done' : 'pending' }}">
                                                    {{ $assignment->status === 'done' ? 'Done' : 'Pending' }}
                                                </span>
                                            </div>
                                        @empty
                                            <span class="muted-text">Belum ada reviewer</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td>
                                    @if($deadline)
                                        <div class="deadline-wrap">
                                            <div class="deadline-date">
                                                {{ $deadline->translatedFormat('d M Y') }}
                                            </div>

                                            <span class="deadline-badge {{ $deadlinePassed ? 'expired' : 'active' }}">
                                                {{ $deadlinePassed ? 'Lewat Deadline' : 'Aktif' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="muted-text">Belum ada deadline</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="progress-wrap">
                                        <span class="progress-text">
                                            {{ $protocol->review_progress ?? '0/0' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <a href="{{ route('sekretariat.review.edit', $protocol->id) }}"
                                       class="manage-button">
                                        <i class="bi bi-pencil-square"></i>
                                        Kelola
                                    </a>

                                    @if($deadlinePassed)
                                        <div class="action-note">
                                            Hanya deadline yang dapat diubah.
                                        </div>
                                    @else
                                        <div class="action-note">
                                            Reviewer & deadline dapat diubah.
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD --}}
            <div class="mobile-card-list">
                @foreach($protocols as $protocol)
                    @php
                        $deadline = $protocol->review_deadline
                            ? \Carbon\Carbon::parse($protocol->review_deadline)
                            : null;

                        $deadlinePassed = $protocol->deadline_passed ?? false;

                        $reviewType = $protocol->verification->review_type ?? '-';

                        $reviewTypeLabel = match($reviewType) {
                            'expedited' => 'Expedited',
                            'full_board' => 'Full Board',
                            'exempted' => 'Exempted',
                            default => ucfirst(str_replace('_', ' ', $reviewType)),
                        };

                        $assignments = $protocol->assigned_reviewers ?? collect();
                    @endphp

                    <div class="mobile-review-card">
                        <div class="mobile-card-top">
                            <div>
                                <div class="proposal-code">
                                    {{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }}
                                </div>

                                <h3>{{ $protocol->title }}</h3>

                                <p>
                                    <i class="bi bi-person"></i>
                                    {{ $protocol->user->name ?? '-' }}
                                </p>
                            </div>

                            <span class="type-badge">
                                {{ $reviewTypeLabel }}
                            </span>
                        </div>

                        <div class="mobile-section">
                            <div class="mobile-label">Reviewer</div>

                            <div class="reviewer-list">
                                @forelse($assignments as $assignment)
                                    <div class="reviewer-item">
                                        <span class="reviewer-name">
                                            {{ $assignment->reviewer->name ?? '-' }}
                                        </span>

                                        <span class="reviewer-status {{ $assignment->status === 'done' ? 'done' : 'pending' }}">
                                            {{ $assignment->status === 'done' ? 'Done' : 'Pending' }}
                                        </span>
                                    </div>
                                @empty
                                    <span class="muted-text">Belum ada reviewer</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="mobile-section-grid">
                            <div>
                                <div class="mobile-label">Deadline</div>

                                @if($deadline)
                                    <div class="deadline-date">
                                        {{ $deadline->translatedFormat('d M Y') }}
                                    </div>

                                    <span class="deadline-badge {{ $deadlinePassed ? 'expired' : 'active' }}">
                                        {{ $deadlinePassed ? 'Lewat Deadline' : 'Aktif' }}
                                    </span>
                                @else
                                    <span class="muted-text">Belum ada deadline</span>
                                @endif
                            </div>

                            <div>
                                <div class="mobile-label">Progress</div>
                                <span class="progress-text">
                                    {{ $protocol->review_progress ?? '0/0' }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('sekretariat.review.edit', $protocol->id) }}"
                           class="manage-button mobile-button">
                            <i class="bi bi-pencil-square"></i>
                            Kelola Reviewer
                        </a>

                        <div class="action-note mobile-note">
                            {{ $deadlinePassed ? 'Hanya deadline yang dapat diubah.' : 'Reviewer & deadline dapat diubah.' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
.review-management-page,
.review-management-page * {
    font-family: inherit;
}

/* HERO */
.review-hero {
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

.review-hero h1 {
    margin: 0;
    color: #111827;
    font-size: 1.65rem;
    font-weight: 900;
}

.review-hero p {
    margin: .45rem 0 0;
    color: #6b7280;
    font-size: .9rem;
    line-height: 1.6;
    max-width: 780px;
}

/* ALERT */
.kep-alert {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .85rem 1rem;
    border-radius: 14px;
    margin-bottom: 1rem;
    font-size: .88rem;
    font-weight: 700;
}

.success-alert {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #bbf7d0;
}

.error-alert {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* PANEL */
.review-panel {
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

/* TABLE */
.desktop-table-wrap {
    overflow-x: auto;
}

.review-table {
    width: 100%;
    min-width: 1050px;
    border-collapse: collapse;
}

.review-table th {
    padding: .85rem 1rem;
    background: #fff;
    color: #6b7280;
    font-size: .74rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .03em;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.review-table td {
    padding: 1rem;
    vertical-align: top;
    border-bottom: 1px solid #f3f4f6;
}

.review-table tr:hover td {
    background: #fffafa;
}

/* CONTENT */
.proposal-code {
    color: #b91c1c;
    font-size: .78rem;
    font-weight: 900;
    margin-bottom: .28rem;
}

.proposal-title {
    color: #111827;
    font-size: .92rem;
    font-weight: 900;
    line-height: 1.45;
    max-width: 320px;
}

.proposal-author {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: #6b7280;
    font-size: .78rem;
    margin-top: .45rem;
}

.type-badge {
    display: inline-flex;
    align-items: center;
    padding: .3rem .7rem;
    border-radius: 999px;
    background: #fee2e2;
    color: #991b1b;
    font-size: .72rem;
    font-weight: 900;
    white-space: nowrap;
}

.reviewer-list {
    display: flex;
    flex-direction: column;
    gap: .45rem;
}

.reviewer-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .7rem;
    padding: .45rem .55rem;
    border-radius: 10px;
    background: #f9fafb;
    border: 1px solid #f3f4f6;
}

.reviewer-name {
    color: #111827;
    font-size: .8rem;
    font-weight: 800;
}

.reviewer-status {
    padding: .18rem .5rem;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 900;
    white-space: nowrap;
}

.reviewer-status.pending {
    background: #fff7ed;
    color: #c2410c;
}

.reviewer-status.done {
    background: #ecfdf5;
    color: #047857;
}

.deadline-wrap {
    display: flex;
    flex-direction: column;
    gap: .4rem;
}

.deadline-date {
    color: #111827;
    font-size: .84rem;
    font-weight: 900;
}

.deadline-badge {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    padding: .25rem .6rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 900;
}

.deadline-badge.active {
    background: #ecfdf5;
    color: #047857;
}

.deadline-badge.expired {
    background: #fef2f2;
    color: #b91c1c;
}

.progress-wrap {
    display: flex;
    align-items: center;
}

.progress-text {
    display: inline-flex;
    align-items: center;
    padding: .3rem .7rem;
    border-radius: 999px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: .76rem;
    font-weight: 900;
    white-space: nowrap;
}

.manage-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .42rem;
    padding: .58rem .85rem;
    border-radius: 12px;
    background: #dc2626;
    color: #fff;
    font-size: .78rem;
    font-weight: 900;
    text-decoration: none;
    white-space: nowrap;
    transition: all .2s ease;
}

.manage-button:hover {
    background: #b91c1c;
    color: #fff;
    transform: translateY(-1px);
}

.action-note {
    color: #6b7280;
    font-size: .7rem;
    margin-top: .4rem;
    max-width: 160px;
    line-height: 1.35;
}

.muted-text {
    color: #9ca3af;
    font-size: .78rem;
}

/* MOBILE */
.mobile-card-list {
    display: none;
    padding: .85rem;
    gap: .85rem;
}

.mobile-review-card {
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 1rem;
    background: #fff;
}

.mobile-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .8rem;
    margin-bottom: .9rem;
}

.mobile-card-top h3 {
    margin: 0;
    color: #111827;
    font-size: .95rem;
    font-weight: 900;
    line-height: 1.45;
}

.mobile-card-top p {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    margin: .45rem 0 0;
    color: #6b7280;
    font-size: .78rem;
}

.mobile-section {
    padding-top: .8rem;
    margin-top: .8rem;
    border-top: 1px solid #f3f4f6;
}

.mobile-section-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .8rem;
    padding-top: .8rem;
    margin-top: .8rem;
    border-top: 1px solid #f3f4f6;
}

.mobile-label {
    color: #6b7280;
    font-size: .72rem;
    font-weight: 900;
    margin-bottom: .35rem;
}

.mobile-button {
    width: 100%;
    margin-top: 1rem;
}

.mobile-note {
    max-width: none;
    text-align: center;
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

/* RESPONSIVE */
@media (max-width: 900px) {
    .desktop-table-wrap {
        display: none;
    }

    .mobile-card-list {
        display: flex;
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .review-hero {
        padding: 1rem;
        border-radius: 18px;
    }

    .review-hero h1 {
        font-size: 1.35rem;
    }

    .mobile-card-top {
        flex-direction: column;
    }

    .mobile-section-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush