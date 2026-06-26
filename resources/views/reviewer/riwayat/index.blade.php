@extends('layouts.reviewer')
@section('title', 'Riwayat Review')

@section('content')

@php
    $decisionLabels = [
        'approved' => ['label' => 'Layak', 'class' => 'badge-approved'],
        'approved_with_recommendation' => ['label' => 'Layak dengan Rekomendasi', 'class' => 'badge-approved'],
        'minor_revision' => ['label' => 'Revisi Minor', 'class' => 'badge-revision'],
        'major_revision' => ['label' => 'Revisi Mayor', 'class' => 'badge-revision'],
        'rejected' => ['label' => 'Tidak Layak', 'class' => 'badge-rejected'],
    ];
@endphp

<div class="page-header">
    <h1>Riwayat Review</h1>
    <p>Daftar protokol penelitian yang sudah Anda review</p>
</div>

<div class="kep-card">
    <div class="kep-card-title" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;">
        <span>
            <i class="bi bi-clock-history"></i> Riwayat Review
        </span>

        <span style="font-size:.85rem;color:var(--text-muted);font-weight:500;">
            Total: {{ $assignments->total() }} review selesai
        </span>
    </div>

    @if($assignments->isEmpty())
        <div class="empty-reviewer-state">
            <i class="bi bi-folder2-open"></i>
            <p>Belum ada riwayat review.</p>
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
                        <th>Keputusan</th>
                        <th>Tanggal Review</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($assignments as $index => $assignment)
                        @php
                            $protocol = $assignment->protocol;
                            $review = $assignment->review;

                            $decision = $review?->keputusan;
                            $decisionData = $decisionLabels[$decision] ?? [
                                'label' => $decision ?? '-',
                                'class' => 'badge-review',
                            ];
                        @endphp

                        <tr>
                            <td>{{ $assignments->firstItem() + $index }}</td>

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
                                <span class="kep-badge {{ $decisionData['class'] }}">
                                    {{ $decisionData['label'] }}
                                </span>
                            </td>

                            <td>
                                @if($review && $review->reviewed_at)
                                    {{ $review->reviewed_at->translatedFormat('d M Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </td>

                            <td style="text-align:right;">
                                <div style="display:flex;justify-content:flex-end;gap:.45rem;flex-wrap:wrap;">
                                    <a href="{{ route('reviewer.tugas.show', $assignment->id) }}"
                                    class="btn-kep btn-outline"
                                    style="font-size:.8rem;padding:.45rem .75rem;">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>

                                    @if($review)
                                        <a href="{{ route('reviewer.riwayat.edit', $assignment->id) }}"
                                        class="btn-kep btn-primary"
                                        style="font-size:.8rem;padding:.45rem .75rem;">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                    @endif
                                </div>
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
    min-width: 920px;
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