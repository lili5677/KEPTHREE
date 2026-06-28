@extends('layouts.sekretariat')

@section('title', 'Verifikasi Dokumen – Sistem KEP')

@section('content')
@php
    $protocolItems = method_exists($protocols, 'getCollection')
        ? $protocols->getCollection()
        : collect($protocols);

    $totalDisplayed = $protocolItems->count();
@endphp

<div class="verification-page">

    {{-- HEADER --}}
    <div class="verification-hero">
        <div>
            <div class="hero-badge">
                <i class="bi bi-file-earmark-check"></i>
                Verifikasi Sekretariat
            </div>

            <h1>Verifikasi Dokumen</h1>

            <p>
                Periksa kelengkapan dokumen pengajuan penelitian sebelum proposal diteruskan
                ke proses review berikutnya.
            </p>
        </div>

        <div class="hero-summary">
            <div class="summary-label">Perlu Diverifikasi</div>
            <div class="summary-number">{{ $totalDisplayed }}</div>
            <div class="summary-text">Proposal tampil pada halaman ini.</div>
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

    {{-- LIST HEADER --}}
    <div class="section-heading">
        <div>
            <h2>Daftar Proposal</h2>
            <p>Pilih proposal untuk memeriksa dokumen dan menentukan kelengkapan berkas.</p>
        </div>
    </div>

    {{-- LIST PROPOSAL --}}
    <div class="proposal-grid">
        @forelse($protocols as $p)
            @php
                $isRevision = $p->status === 'revision_required';
                $statusLabel = $isRevision ? 'Revisi Diunggah Ulang' : 'Menunggu Verifikasi';
                $statusClass = $isRevision ? 'status-revision' : 'status-waiting';
            @endphp

            <div class="proposal-card {{ $isRevision ? 'revision-card' : 'new-card' }}">
                <div class="proposal-top">
                    <div class="proposal-code-wrap">
                        <div class="proposal-code">
                            PRO-{{ $p->id }}
                        </div>

                        <span class="status-pill {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="proposal-icon {{ $isRevision ? 'orange' : 'red' }}">
                        <i class="bi {{ $isRevision ? 'bi-arrow-repeat' : 'bi-file-earmark-text' }}"></i>
                    </div>
                </div>

                <div class="proposal-body">
                    <h3>{{ $p->title }}</h3>

                    <div class="proposal-meta">
                        <span>
                            <i class="bi bi-person"></i>
                            {{ $p->user->name ?? '-' }}
                        </span>

                        <span>
                            <i class="bi bi-calendar-event"></i>
                            {{ $p->created_at?->translatedFormat('d M Y') ?? '-' }}
                        </span>
                    </div>

                    @if($isRevision && $p->latestRevision)
                        <div class="revision-info">
                            <i class="bi bi-paperclip"></i>
                            <span>
                                Revisi diunggah
                                {{ $p->latestRevision->submitted_at?->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                    @else
                        <div class="normal-info">
                            <i class="bi bi-info-circle"></i>
                            <span>Dokumen menunggu pemeriksaan awal sekretariat.</span>
                        </div>
                    @endif
                </div>

                <div class="proposal-footer">
                    <a href="{{ route('sekretariat.verifikasi.show', $p->id) }}"
                       class="verify-button {{ $isRevision ? 'revision-button' : 'primary-button' }}">
                        <span>Periksa Dokumen</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>

                <h3>Belum Ada Proposal</h3>
                <p>Belum ada proposal yang perlu diverifikasi oleh sekretariat.</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($protocols, 'links'))
        <div class="pagination-wrap">
            {{ $protocols->links() }}
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
.verification-page,
.verification-page * {
    font-family: inherit;
}

.verification-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 250px;
    gap: 1rem;
    padding: 1.35rem;
    margin-bottom: 1.1rem;
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
    font-weight: 800;
    margin-bottom: .75rem;
}

.verification-hero h1 {
    margin: 0;
    color: #111827;
    font-size: 1.65rem;
    font-weight: 900;
}

.verification-hero p {
    margin: .45rem 0 0;
    color: #6b7280;
    font-size: .9rem;
    line-height: 1.6;
    max-width: 760px;
}

.hero-summary {
    background: #fff;
    border: 1px solid #fecaca;
    border-radius: 18px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.summary-label {
    color: #6b7280;
    font-size: .78rem;
    font-weight: 800;
}

.summary-number {
    color: #b91c1c;
    font-size: 2.4rem;
    font-weight: 900;
    line-height: 1;
    margin: .35rem 0;
}

.summary-text {
    color: #6b7280;
    font-size: .78rem;
    line-height: 1.4;
}

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

.section-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin: 1.1rem 0 .85rem;
}

.section-heading h2 {
    margin: 0;
    color: #111827;
    font-size: 1.05rem;
    font-weight: 900;
}

.section-heading p {
    margin: .25rem 0 0;
    color: #6b7280;
    font-size: .84rem;
}

.proposal-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.proposal-card {
    position: relative;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 1rem;
    overflow: hidden;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.proposal-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 5px;
    background: #dc2626;
}

.proposal-card.revision-card::before {
    background: #f97316;
}

.proposal-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 34px rgba(15, 23, 42, .08);
    border-color: #fecaca;
}

.proposal-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .9rem;
    margin-bottom: .85rem;
}

.proposal-code-wrap {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
    min-width: 0;
}

.proposal-code {
    color: #b91c1c;
    font-size: .95rem;
    font-weight: 900;
}

.proposal-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.proposal-icon.red {
    background: #fee2e2;
    color: #b91c1c;
}

.proposal-icon.orange {
    background: #fff7ed;
    color: #c2410c;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: .28rem .65rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 900;
    line-height: 1.2;
    white-space: nowrap;
}

.status-waiting {
    background: #fee2e2;
    color: #991b1b;
}

.status-revision {
    background: #fff7ed;
    color: #c2410c;
}

.proposal-body h3 {
    margin: 0;
    color: #111827;
    font-size: .98rem;
    font-weight: 900;
    line-height: 1.45;
}

.proposal-meta {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin-top: .55rem;
    color: #6b7280;
    font-size: .8rem;
}

.proposal-meta span {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}

.revision-info,
.normal-info {
    display: flex;
    align-items: flex-start;
    gap: .45rem;
    margin-top: .85rem;
    padding: .72rem .8rem;
    border-radius: 14px;
    font-size: .78rem;
    line-height: 1.45;
}

.revision-info {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
}

.normal-info {
    background: #f9fafb;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}

.proposal-footer {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
}

.verify-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .65rem .95rem;
    border-radius: 12px;
    color: #fff;
    font-size: .82rem;
    font-weight: 900;
    text-decoration: none;
    transition: transform .2s ease, background .2s ease;
}

.verify-button:hover {
    color: #fff;
    transform: translateY(-1px);
}

.primary-button {
    background: #dc2626;
}

.primary-button:hover {
    background: #b91c1c;
}

.revision-button {
    background: #ea580c;
}

.revision-button:hover {
    background: #c2410c;
}

.empty-state {
    grid-column: 1 / -1;
    background: #fff;
    border: 1px dashed #d1d5db;
    border-radius: 20px;
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
    margin-top: 1rem;
}

@media (max-width: 1100px) {
    .verification-hero {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .proposal-grid {
        grid-template-columns: 1fr;
    }

    .verification-hero {
        padding: 1rem;
        border-radius: 18px;
    }

    .proposal-footer {
        justify-content: stretch;
    }

    .verify-button {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .verification-hero h1 {
        font-size: 1.35rem;
    }

    .proposal-card {
        padding: .9rem;
        border-radius: 16px;
    }

    .proposal-top {
        flex-direction: column;
    }

    .proposal-icon {
        display: none;
    }
}
</style>
@endpush