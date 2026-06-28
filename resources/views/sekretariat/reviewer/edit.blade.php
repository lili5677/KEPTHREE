@extends('layouts.sekretariat')

@section('title', 'Kelola Reviewer – Sistem KEP')

@section('content')
@php
    $assignments = $assignments ?? collect();
    $reviewers = $reviewers ?? collect();

    $reviewType = $protocol->verification->review_type ?? '-';

    $reviewTypeLabel = match($reviewType) {
        'expedited' => 'Expedited Review',
        'full_board' => 'Full Board Review',
        'exempted' => 'Exempted',
        default => ucfirst(str_replace('_', ' ', $reviewType)),
    };

    $selectedReviewerIds = old(
        'reviewer_ids',
        $assignments->pluck('reviewer_id')->values()->toArray()
    );

    $deadlineValue = old(
        'review_deadline',
        $deadline ? \Carbon\Carbon::parse($deadline)->format('Y-m-d') : ''
    );

    $deadlineDate = $deadline ? \Carbon\Carbon::parse($deadline) : null;
@endphp

<div class="review-edit-page">

    {{-- HEADER --}}
    <div class="review-edit-hero">
        <div>
            <div class="hero-badge">
                <i class="bi bi-pencil-square"></i>
                Kelola Reviewer
            </div>

            <h1>Kelola Reviewer & Deadline</h1>

            <p>
                Ubah deadline review kapan saja. Reviewer hanya dapat diganti sebelum deadline
                dan sebelum ada reviewer yang menyelesaikan review.
            </p>
        </div>

        <a href="{{ route('sekretariat.review.index') }}" class="back-button">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    {{-- ERROR --}}
    @if($errors->any())
        <div class="kep-alert error-alert">
            <i class="bi bi-exclamation-circle"></i>
            <div>
                <strong>Periksa kembali input Anda.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="kep-alert error-alert">
            <i class="bi bi-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="edit-layout">

        {{-- INFO PROPOSAL --}}
        <div class="proposal-panel">
            <div class="panel-heading">
                <div>
                    <h2>Informasi Proposal</h2>
                    <p>Ringkasan proposal yang sedang dikelola reviewernya.</p>
                </div>
            </div>

            <div class="proposal-info-box">
                <div class="proposal-code">
                    {{ $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id }}
                </div>

                <h3>{{ $protocol->title }}</h3>

                <div class="info-list">
                    <div class="info-item">
                        <span>Peneliti</span>
                        <strong>{{ $protocol->user->name ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Jenis Review</span>
                        <strong>{{ $reviewTypeLabel }}</strong>
                    </div>

                    <div class="info-item">
                        <span>Jumlah Reviewer</span>
                        <strong>{{ $requiredReviewerCount }} Reviewer</strong>
                    </div>

                    <div class="info-item">
                        <span>Status Deadline</span>

                        @if($deadlinePassed)
                            <strong class="text-danger">Lewat Deadline</strong>
                        @else
                            <strong class="text-success">Masih Aktif</strong>
                        @endif
                    </div>
                </div>

                <div class="rule-box {{ $canEditReviewer ? 'can-edit' : 'locked' }}">
                    <i class="bi {{ $canEditReviewer ? 'bi-unlock' : 'bi-lock' }}"></i>

                    <div>
                        @if($canEditReviewer)
                            <strong>Reviewer masih dapat diubah.</strong>
                            <p>Deadline belum lewat dan belum ada reviewer yang menyelesaikan review.</p>
                        @else
                            <strong>Reviewer tidak dapat diubah.</strong>

                            @if($deadlinePassed)
                                <p>Deadline sudah lewat. Anda tetap dapat memperbarui deadline.</p>
                            @elseif($hasSubmittedReview)
                                <p>Sudah ada reviewer yang menyelesaikan review. Anda tetap dapat memperbarui deadline.</p>
                            @else
                                <p>Reviewer terkunci, tetapi deadline tetap dapat diperbarui.</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="current-reviewer-box">
                <h3>Reviewer Saat Ini</h3>

                <div class="current-reviewer-list">
                    @foreach($assignments as $assignment)
                        <div class="current-reviewer-item">
                            <div class="reviewer-avatar">
                                {{ strtoupper(substr($assignment->reviewer->name ?? 'R', 0, 1)) }}
                            </div>

                            <div class="reviewer-current-main">
                                <strong>{{ $assignment->reviewer->name ?? '-' }}</strong>
                                <span>{{ $assignment->reviewer->email ?? '-' }}</span>
                            </div>

                            <span class="reviewer-status {{ $assignment->status === 'done' ? 'done' : 'pending' }}">
                                {{ $assignment->status === 'done' ? 'Done' : 'Pending' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <div class="form-panel">
            <div class="panel-heading">
                <div>
                    <h2>Form Perubahan</h2>
                    <p>Perbarui deadline atau reviewer sesuai ketentuan.</p>
                </div>
            </div>

            <form action="{{ route('sekretariat.review.update', $protocol->id) }}"
                  method="POST"
                  class="review-edit-form">
                @csrf
                @method('PUT')

                {{-- DEADLINE --}}
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-icon red">
                            <i class="bi bi-calendar-event"></i>
                        </div>

                        <div>
                            <h3>Deadline Review</h3>
                            <p>Deadline dapat diubah meskipun deadline sebelumnya sudah lewat.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="review_deadline">Tanggal Deadline Baru</label>

                        <input type="date"
                               id="review_deadline"
                               name="review_deadline"
                               value="{{ $deadlineValue }}"
                               class="form-control @error('review_deadline') is-invalid @enderror"
                               required>

                        @error('review_deadline')
                            <div class="input-error">{{ $message }}</div>
                        @enderror

                        @if($deadlineDate)
                            <div class="helper-text">
                                Deadline saat ini:
                                <strong>{{ $deadlineDate->translatedFormat('d M Y') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- REVIEWER --}}
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-icon blue">
                            <i class="bi bi-people"></i>
                        </div>

                        <div>
                            <h3>Reviewer</h3>

                            @if($canEditReviewer)
                                <p>Pilih {{ $requiredReviewerCount }} reviewer. Reviewer tidak boleh sama.</p>
                            @else
                                <p>Reviewer terkunci. Anda hanya dapat mengubah deadline.</p>
                            @endif
                        </div>
                    </div>

                    @if($canEditReviewer)
                        <div class="reviewer-select-grid">
                            @for($i = 0; $i < $requiredReviewerCount; $i++)
                                <div class="form-group">
                                    <label for="reviewer_{{ $i }}">
                                        Reviewer {{ $i + 1 }}
                                    </label>

                                    <select id="reviewer_{{ $i }}"
                                            name="reviewer_ids[]"
                                            class="form-control reviewer-select @error('reviewer_ids.' . $i) is-invalid @enderror"
                                            required>
                                        <option value="">Pilih Reviewer</option>

                                        @foreach($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}"
                                                {{ (string)($selectedReviewerIds[$i] ?? '') === (string)$reviewer->id ? 'selected' : '' }}>
                                                {{ $reviewer->name }} — {{ $reviewer->email }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('reviewer_ids.' . $i)
                                        <div class="input-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endfor
                        </div>
                    @else
                        <div class="locked-reviewer-list">
                            @foreach($assignments as $assignment)
                                <div class="locked-reviewer-item">
                                    <i class="bi bi-person-lock"></i>
                                    <div>
                                        <strong>{{ $assignment->reviewer->name ?? '-' }}</strong>
                                        <span>{{ $assignment->reviewer->email ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ACTION --}}
                <div class="form-actions">
                    <a href="{{ route('sekretariat.review.index') }}" class="btn-cancel">
                        Batal
                    </a>

                    <button type="submit" class="btn-save">
                        <i class="bi bi-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
.review-edit-page,
.review-edit-page * {
    font-family: inherit;
}

/* HERO */
.review-edit-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
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

.review-edit-hero h1 {
    margin: 0;
    color: #111827;
    font-size: 1.65rem;
    font-weight: 900;
}

.review-edit-hero p {
    margin: .45rem 0 0;
    color: #6b7280;
    font-size: .9rem;
    line-height: 1.6;
    max-width: 760px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .42rem;
    padding: .62rem .9rem;
    border-radius: 12px;
    background: #fff;
    color: #991b1b;
    border: 1px solid #fecaca;
    text-decoration: none;
    font-size: .82rem;
    font-weight: 900;
    white-space: nowrap;
    transition: all .2s ease;
}

.back-button:hover {
    background: #fff1f2;
    color: #991b1b;
}

/* ALERT */
.kep-alert {
    display: flex;
    align-items: flex-start;
    gap: .55rem;
    padding: .85rem 1rem;
    border-radius: 14px;
    margin-bottom: 1rem;
    font-size: .88rem;
    font-weight: 700;
}

.error-alert {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

.error-alert ul {
    margin: .3rem 0 0;
    padding-left: 1.2rem;
    font-weight: 600;
}

/* LAYOUT */
.edit-layout {
    display: grid;
    grid-template-columns: 410px minmax(0, 1fr);
    gap: 1rem;
}

.proposal-panel,
.form-panel {
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
    line-height: 1.45;
}

/* PROPOSAL INFO */
.proposal-info-box {
    padding: 1.15rem;
}

.proposal-code {
    color: #b91c1c;
    font-size: .78rem;
    font-weight: 900;
    margin-bottom: .3rem;
}

.proposal-info-box h3 {
    margin: 0;
    color: #111827;
    font-size: 1rem;
    font-weight: 900;
    line-height: 1.45;
}

.info-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: .65rem;
    margin-top: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .8rem;
    padding: .72rem .8rem;
    border-radius: 14px;
    background: #f9fafb;
    border: 1px solid #f3f4f6;
}

.info-item span {
    color: #6b7280;
    font-size: .78rem;
    font-weight: 800;
}

.info-item strong {
    color: #111827;
    font-size: .78rem;
    font-weight: 900;
    text-align: right;
}

.text-danger {
    color: #b91c1c !important;
}

.text-success {
    color: #047857 !important;
}

.rule-box {
    display: flex;
    align-items: flex-start;
    gap: .7rem;
    margin-top: 1rem;
    padding: .85rem;
    border-radius: 16px;
    font-size: .8rem;
}

.rule-box i {
    font-size: 1.15rem;
    margin-top: .1rem;
}

.rule-box strong {
    display: block;
    margin-bottom: .25rem;
    font-weight: 900;
}

.rule-box p {
    margin: 0;
    line-height: 1.45;
}

.rule-box.can-edit {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #bbf7d0;
}

.rule-box.locked {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
}

/* CURRENT REVIEWER */
.current-reviewer-box {
    padding: 1.15rem;
    border-top: 1px solid #e5e7eb;
}

.current-reviewer-box h3 {
    margin: 0 0 .8rem;
    color: #111827;
    font-size: .95rem;
    font-weight: 900;
}

.current-reviewer-list {
    display: flex;
    flex-direction: column;
    gap: .55rem;
}

.current-reviewer-item {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .7rem;
    border-radius: 14px;
    background: #fff;
    border: 1px solid #f3f4f6;
}

.reviewer-avatar {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    background: #fee2e2;
    color: #991b1b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .82rem;
    font-weight: 900;
    flex-shrink: 0;
}

.reviewer-current-main {
    min-width: 0;
    flex: 1;
}

.reviewer-current-main strong {
    display: block;
    color: #111827;
    font-size: .82rem;
    font-weight: 900;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.reviewer-current-main span {
    display: block;
    color: #6b7280;
    font-size: .72rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.reviewer-status {
    padding: .2rem .55rem;
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

/* FORM */
.review-edit-form {
    padding: 1.15rem;
}

.form-section {
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    margin-bottom: 1rem;
}

.section-title {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    margin-bottom: 1rem;
}

.section-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.section-icon.red {
    background: #fee2e2;
    color: #b91c1c;
}

.section-icon.blue {
    background: #eff6ff;
    color: #1d4ed8;
}

.section-title h3 {
    margin: 0;
    color: #111827;
    font-size: .95rem;
    font-weight: 900;
}

.section-title p {
    margin: .25rem 0 0;
    color: #6b7280;
    font-size: .8rem;
    line-height: 1.45;
}

.form-group {
    margin-bottom: .9rem;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: .35rem;
    color: #374151;
    font-size: .8rem;
    font-weight: 900;
}

.form-control {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    padding: .68rem .78rem;
    font-size: .86rem;
    color: #111827;
    background: #fff;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.form-control:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, .12);
}

.form-control.is-invalid {
    border-color: #dc2626;
}

.input-error {
    margin-top: .35rem;
    color: #b91c1c;
    font-size: .75rem;
    font-weight: 700;
}

.helper-text {
    margin-top: .42rem;
    color: #6b7280;
    font-size: .75rem;
}

.reviewer-select-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .9rem;
}

.locked-reviewer-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem;
}

.locked-reviewer-item {
    display: flex;
    align-items: flex-start;
    gap: .55rem;
    padding: .75rem;
    border-radius: 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
}

.locked-reviewer-item i {
    color: #6b7280;
    margin-top: .12rem;
}

.locked-reviewer-item strong {
    display: block;
    color: #111827;
    font-size: .8rem;
    font-weight: 900;
}

.locked-reviewer-item span {
    display: block;
    margin-top: .15rem;
    color: #6b7280;
    font-size: .72rem;
}

/* ACTION */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .7rem;
    padding-top: .2rem;
}

.btn-cancel,
.btn-save {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .42rem;
    padding: .68rem 1rem;
    border-radius: 12px;
    font-size: .84rem;
    font-weight: 900;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .2s ease;
}

.btn-cancel {
    background: #fff;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-cancel:hover {
    background: #f9fafb;
    color: #111827;
}

.btn-save {
    background: #dc2626;
    color: #fff;
}

.btn-save:hover {
    background: #b91c1c;
    color: #fff;
    transform: translateY(-1px);
}

/* RESPONSIVE */
@media (max-width: 1150px) {
    .edit-layout {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .review-edit-hero {
        flex-direction: column;
        padding: 1rem;
        border-radius: 18px;
    }

    .back-button {
        width: 100%;
    }

    .reviewer-select-grid,
    .locked-reviewer-list {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-cancel,
    .btn-save {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .review-edit-hero h1 {
        font-size: 1.35rem;
    }

    .info-item {
        flex-direction: column;
        gap: .25rem;
    }

    .info-item strong {
        text-align: left;
    }

    .current-reviewer-item {
        align-items: flex-start;
    }

    .reviewer-status {
        align-self: flex-start;
    }
}
</style>
@endpush

@push('scripts')
@if($canEditReviewer)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('.reviewer-select');

    function refreshDuplicateOptions() {
        const selectedValues = Array.from(selects)
            .map(select => select.value)
            .filter(value => value !== '');

        selects.forEach(select => {
            const currentValue = select.value;

            Array.from(select.options).forEach(option => {
                if (!option.value) return;

                option.disabled = selectedValues.includes(option.value) && option.value !== currentValue;
            });
        });
    }

    selects.forEach(select => {
        select.addEventListener('change', refreshDuplicateOptions);
    });

    refreshDuplicateOptions();
});
</script>
@endif
@endpush