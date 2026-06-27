@extends('layouts.peneliti')
@section('title', 'Dashboard Peneliti')

@section('content')

@php
    $userId = auth()->id();

    $proposalQuery = \App\Models\Protocol::where('user_id', $userId);

    $totalProposal = (clone $proposalQuery)->count();

    $proposalDiproses = (clone $proposalQuery)
        ->whereIn('status', ['new_proposal', 'waiting_verification', 'under_review'])
        ->count();

    $proposalRevisi = (clone $proposalQuery)
        ->whereIn('status', ['revision_required', 'approved_with_recommendation'])
        ->count();

    $proposalApproved = (clone $proposalQuery)
        ->where('status', 'approved')
        ->count();

    $skeQuery = \App\Models\SkeDocument::whereHas('protocol', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    });

    $skePerluDicek = (clone $skeQuery)->where('status', 'menunggu_konfirmasi')->count();
    $skeTerbit = (clone $skeQuery)->where('status', 'terbit')->count();

    $statusMap = [
        'new_proposal'                 => ['label' => 'Pengajuan Baru', 'class' => 'status-blue'],
        'waiting_verification'         => ['label' => 'Menunggu Verifikasi', 'class' => 'status-amber'],
        'under_review'                 => ['label' => 'Sedang Review', 'class' => 'status-indigo'],
        'approved'                     => ['label' => 'Disetujui', 'class' => 'status-green'],
        'rejected'                     => ['label' => 'Ditolak', 'class' => 'status-red'],
        'revision_required'            => ['label' => 'Perlu Revisi', 'class' => 'status-orange'],
        'approved_with_recommendation' => ['label' => 'Revisi Rekomendasi', 'class' => 'status-orange'],
    ];

    $latest = $pengajuanTerbaru->first();
@endphp

<div class="peneliti-dashboard">

    {{-- HERO --}}
    <div class="research-hero">
        <div class="research-hero-main">
            <div class="hero-badge">
                <i class="bi bi-mortarboard"></i>
                Dashboard Peneliti
            </div>

            <h1>Halo, {{ auth()->user()->name }}</h1>
            <p>
                Pantau progres proposal penelitian Anda mulai dari pengajuan, verifikasi, review, revisi,
                hingga penerbitan Surat Kelayakan Etik.
            </p>

            <div class="hero-actions">
                <a href="{{ route('peneliti.pengajuan.create') }}" class="btn-hero-primary">
                    <i class="bi bi-plus-circle"></i>
                    Buat Pengajuan
                </a>

                <a href="{{ route('peneliti.riwayat') }}" class="btn-hero-secondary">
                    <i class="bi bi-clock-history"></i>
                    Riwayat Pengajuan
                </a>
            </div>
        </div>

        <div class="research-hero-side">
            <div class="side-label">Proposal Aktif</div>
            <div class="side-number">{{ $proposalDiproses + $proposalRevisi }}</div>
            <div class="side-text">
                Proposal yang masih membutuhkan proses atau tindakan lanjutan.
            </div>
        </div>
    </div>

    {{-- PROPOSAL TRACK SUMMARY --}}
    <div class="track-grid">

        <div class="track-card">
            <div class="track-icon blue">
                <i class="bi bi-folder2-open"></i>
            </div>
            <div>
                <div class="track-value">{{ $totalProposal }}</div>
                <div class="track-label">Total Proposal</div>
                <div class="track-desc">Seluruh pengajuan Anda</div>
            </div>
        </div>

        <div class="track-card">
            <div class="track-icon amber">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="track-value">{{ $proposalDiproses }}</div>
                <div class="track-label">Sedang Diproses</div>
                <div class="track-desc">Verifikasi atau review</div>
            </div>
        </div>

        <div class="track-card">
            <div class="track-icon orange">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <div class="track-value">{{ $proposalRevisi }}</div>
                <div class="track-label">Butuh Revisi</div>
                <div class="track-desc">Perlu tindakan Anda</div>
            </div>
        </div>

        <div class="track-card">
            <div class="track-icon green">
                <i class="bi bi-patch-check"></i>
            </div>
            <div>
                <div class="track-value">{{ $proposalApproved }}</div>
                <div class="track-label">Disetujui</div>
                <div class="track-desc">Masuk tahap SKE</div>
            </div>
        </div>

    </div>

    {{-- MAIN AREA --}}
    <div class="dashboard-main-grid">

        {{-- LEFT: TRACKING PROPOSAL --}}
        <div class="dashboard-panel proposal-panel">
            <div class="panel-heading">
                <div>
                    <h2>Tracking Proposal Terbaru</h2>
                    <p>Fokus utama dashboard ini adalah progres proposal penelitian yang Anda ajukan.</p>
                </div>

                <a href="{{ route('peneliti.riwayat') }}" class="panel-link">
                    Lihat Semua
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @if($latest)
                @php
                    $latestStatus = $statusMap[$latest->status] ?? [
                        'label' => ucwords(str_replace('_', ' ', $latest->status)),
                        'class' => 'status-slate',
                    ];

                    $stepIndex = match($latest->status) {
                        'new_proposal' => 1,
                        'waiting_verification' => 2,
                        'under_review' => 3,
                        'revision_required', 'approved_with_recommendation' => 4,
                        'approved' => 5,
                        'rejected' => 4,
                        default => 1,
                    };
                @endphp

                <div class="latest-proposal-card">
                    <div class="latest-top">
                        <div>
                            <div class="latest-code">
                                {{ $latest->nomor_registrasi ?? 'ID-' . $latest->id }}
                            </div>
                            <h3>{{ $latest->title }}</h3>
                        </div>

                        <span class="status-pill {{ $latestStatus['class'] }}">
                            {{ $latestStatus['label'] }}
                        </span>
                    </div>

                    <div class="latest-meta">
                        <span>
                            <i class="bi bi-mortarboard"></i>
                            {{ $latest->program_studi ?? 'Program studi belum diisi' }}
                        </span>
                        <span>
                            <i class="bi bi-calendar"></i>
                            {{ $latest->submitted_at?->format('d M Y') ?? $latest->created_at->format('d M Y') }}
                        </span>
                    </div>

                    <div class="proposal-steps">
                        <div class="proposal-step {{ $stepIndex >= 1 ? 'done' : '' }}">
                            <span>1</span>
                            <p>Diajukan</p>
                        </div>
                        <div class="proposal-line {{ $stepIndex >= 2 ? 'active' : '' }}"></div>

                        <div class="proposal-step {{ $stepIndex >= 2 ? 'done' : '' }}">
                            <span>2</span>
                            <p>Verifikasi</p>
                        </div>
                        <div class="proposal-line {{ $stepIndex >= 3 ? 'active' : '' }}"></div>

                        <div class="proposal-step {{ $stepIndex >= 3 ? 'done' : '' }}">
                            <span>3</span>
                            <p>Review</p>
                        </div>
                        <div class="proposal-line {{ $stepIndex >= 4 ? 'active' : '' }}"></div>

                        <div class="proposal-step {{ $stepIndex >= 4 ? 'done' : '' }}">
                            <span>4</span>
                            <p>Keputusan</p>
                        </div>
                        <div class="proposal-line {{ $stepIndex >= 5 ? 'active' : '' }}"></div>

                        <div class="proposal-step {{ $stepIndex >= 5 ? 'done' : '' }}">
                            <span>5</span>
                            <p>SKE</p>
                        </div>
                    </div>

                    <div class="latest-actions">
                        <a href="{{ route('peneliti.riwayat.show', $latest->id) }}" class="btn-track-primary">
                            Lihat Detail Proposal
                            <i class="bi bi-chevron-right"></i>
                        </a>

                        @if(in_array($latest->status, ['revision_required', 'approved_with_recommendation']))
                            <a href="{{ route('peneliti.revisi.show', $latest->id) }}" class="btn-track-warning">
                                <i class="bi bi-pencil-square"></i>
                                Upload Revisi
                            </a>
                        @endif

                        @if($latest->skeDocument && in_array($latest->status, ['approved', 'issued']))
                            <a href="{{ route('peneliti.ske.show', $latest->skeDocument->id) }}" class="btn-track-ske">
                                <i class="bi bi-shield-check"></i>
                                Cek SKE
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="empty-track">
                    <i class="bi bi-inbox"></i>
                    <h3>Belum ada proposal</h3>
                    <p>Mulai ajukan protokol penelitian pertama Anda.</p>
                    <a href="{{ route('peneliti.pengajuan.create') }}" class="btn-track-primary">
                        <i class="bi bi-plus-circle"></i>
                        Buat Pengajuan Pertama
                    </a>
                </div>
            @endif
        </div>

        {{-- RIGHT: SKE RINGKAS + AKSI --}}
        <div class="side-stack">

            <div class="mini-ske-card">
                <div class="mini-ske-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="mini-ske-content">
                    <h3>Info SKE</h3>
                    <p>SKE ditampilkan saat proposal sudah disetujui dan surat sudah dibuat admin.</p>

                    <div class="mini-ske-row">
                        <span>Perlu Dicek</span>
                        <strong>{{ $skePerluDicek }}</strong>
                    </div>

                    <div class="mini-ske-row">
                        <span>Sudah Terbit</span>
                        <strong>{{ $skeTerbit }}</strong>
                    </div>

                    <a href="{{ route('peneliti.riwayat') }}" class="mini-link">
                        Cek di Riwayat
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="help-card">
                <div class="help-title">
                    <i class="bi bi-lightbulb"></i>
                    Langkah Berikutnya
                </div>

                <div class="help-item">
                    <span>1</span>
                    <p>Pantau status proposal dari daftar terbaru.</p>
                </div>

                <div class="help-item">
                    <span>2</span>
                    <p>Jika diminta revisi, unggah perbaikan melalui tombol revisi.</p>
                </div>

                <div class="help-item">
                    <span>3</span>
                    <p>Jika SKE tersedia, cek kebenaran data sebelum diteruskan ke ketua.</p>
                </div>
            </div>

        </div>

    </div>

    {{-- LIST PROPOSAL --}}
    <div class="proposal-list-section">
        <div class="section-heading">
            <div>
                <h2>Proposal yang Diajukan</h2>
                <p>Ringkasan beberapa pengajuan terbaru milik Anda.</p>
            </div>
        </div>

        <div class="proposal-list">
            @forelse($pengajuanTerbaru as $item)

                @php
                    $statusData = $statusMap[$item->status] ?? [
                        'label' => ucwords(str_replace('_', ' ', $item->status)),
                        'class' => 'status-slate',
                    ];

                    $ske = $item->skeDocument ?? null;
                    $showRevisionButton = in_array($item->status, ['revision_required', 'approved_with_recommendation']);
                    $showSkeButton = $ske && in_array($item->status, ['approved', 'issued']);
                @endphp

                <div class="proposal-row">
                    <div class="proposal-row-main">
                        <div class="proposal-row-meta">
                            <span class="proposal-code">
                                {{ $item->nomor_registrasi ?? 'ID-' . $item->id }}
                            </span>

                            <span class="status-pill {{ $statusData['class'] }}">
                                {{ $statusData['label'] }}
                            </span>

                            @if($ske)
                                <span class="status-pill status-soft">
                                    SKE: {{ $ske->statusLabel() }}
                                </span>
                            @endif
                        </div>

                        <h3>{{ $item->title }}</h3>

                        <div class="proposal-row-sub">
                            <span>
                                <i class="bi bi-mortarboard"></i>
                                {{ $item->program_studi ?? 'Program studi belum diisi' }}
                            </span>
                            <span>
                                <i class="bi bi-calendar"></i>
                                {{ $item->submitted_at?->format('d M Y') ?? $item->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="proposal-row-actions">
                        <a href="{{ route('peneliti.riwayat.show', $item->id) }}" class="btn-row-detail">
                            Detail
                        </a>

                        @if($showRevisionButton)
                            <a href="{{ route('peneliti.revisi.show', $item->id) }}" class="btn-row-revisi">
                                Revisi
                            </a>
                        @endif

                        @if($showSkeButton)
                            <a href="{{ route('peneliti.ske.show', $ske->id) }}" class="btn-row-ske">
                                Cek SKE
                            </a>
                        @endif
                    </div>
                </div>

            @empty
                <div class="empty-track small">
                    <i class="bi bi-inbox"></i>
                    <h3>Belum ada pengajuan</h3>
                    <p>Data proposal akan muncul setelah Anda membuat pengajuan.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
.peneliti-dashboard,
.peneliti-dashboard * {
    font-family: inherit;
}

/* HERO */
.research-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 260px;
    gap: 1rem;
    padding: 1.35rem;
    margin-bottom: 1.25rem;
    border-radius: 22px;
    background:
        radial-gradient(circle at top left, rgba(37, 99, 235, .14), transparent 35%),
        linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
    border: 1px solid var(--border);
    box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .35rem .75rem;
    border-radius: 999px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    font-size: .78rem;
    font-weight: 800;
    margin-bottom: .75rem;
}

.research-hero h1 {
    margin: 0;
    color: var(--navy-deep);
    font-size: 1.55rem;
    font-weight: 800;
}

.research-hero p {
    margin: .45rem 0 0;
    color: var(--text-muted);
    font-size: .9rem;
    line-height: 1.6;
    max-width: 720px;
}

.hero-actions {
    display: flex;
    gap: .65rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.btn-hero-primary,
.btn-hero-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .68rem 1rem;
    border-radius: 12px;
    font-size: .86rem;
    font-weight: 800;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .2s ease;
}

.btn-hero-primary {
    background: var(--blue-accent);
    color: #fff;
    border-color: var(--blue-accent);
}

.btn-hero-primary:hover {
    background: #1d4ed8;
    color: #fff;
    transform: translateY(-1px);
}

.btn-hero-secondary {
    background: #fff;
    color: var(--navy-deep);
    border-color: var(--border);
}

.btn-hero-secondary:hover {
    background: #f8fafc;
    color: var(--navy-deep);
}

.research-hero-side {
    background: #ffffff;
    border: 1px solid #bfdbfe;
    border-radius: 18px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.side-label {
    color: var(--text-muted);
    font-size: .78rem;
    font-weight: 800;
}

.side-number {
    color: var(--blue-accent);
    font-size: 2.4rem;
    font-weight: 900;
    line-height: 1;
    margin: .35rem 0;
}

.side-text {
    color: var(--text-muted);
    font-size: .78rem;
    line-height: 1.4;
}

/* TRACK CARDS */
.track-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: .9rem;
    margin-bottom: 1.25rem;
}

.track-card {
    display: flex;
    align-items: flex-start;
    gap: .8rem;
    padding: 1rem;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
}

.track-icon {
    width: 44px;
    height: 44px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.track-icon.blue {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.track-icon.amber {
    background: #fffbeb;
    color: #b45309;
}

.track-icon.orange {
    background: #fff7ed;
    color: #c2410c;
}

.track-icon.green {
    background: #ecfdf5;
    color: #047857;
}

.track-value {
    color: var(--navy-deep);
    font-size: 1.6rem;
    font-weight: 900;
    line-height: 1;
}

.track-label {
    margin-top: .28rem;
    color: var(--navy-deep);
    font-size: .84rem;
    font-weight: 800;
}

.track-desc {
    margin-top: .25rem;
    color: var(--text-muted);
    font-size: .74rem;
}

/* MAIN GRID */
.dashboard-main-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 330px;
    gap: 1.25rem;
    margin-bottom: 1.35rem;
}

.dashboard-panel,
.mini-ske-card,
.help-card,
.proposal-list-section {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    overflow: hidden;
}

.panel-heading,
.section-heading {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .8rem;
}

.panel-heading h2,
.section-heading h2 {
    margin: 0;
    color: var(--navy-deep);
    font-size: 1rem;
    font-weight: 900;
}

.panel-heading p,
.section-heading p {
    margin: .2rem 0 0;
    color: var(--text-muted);
    font-size: .8rem;
    line-height: 1.45;
}

.panel-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: var(--blue-accent);
    font-size: .82rem;
    font-weight: 800;
    text-decoration: none;
    white-space: nowrap;
}

/* LATEST PROPOSAL */
.latest-proposal-card {
    padding: 1.15rem;
}

.latest-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .8rem;
}

.latest-code,
.proposal-code {
    color: var(--text-muted);
    font-size: .74rem;
    font-weight: 900;
    margin-bottom: .3rem;
}

.latest-top h3,
.proposal-row h3 {
    margin: 0;
    color: var(--navy-deep);
    font-size: 1rem;
    font-weight: 900;
    line-height: 1.45;
}

.latest-meta,
.proposal-row-sub {
    display: flex;
    flex-wrap: wrap;
    gap: .85rem;
    margin-top: .6rem;
    color: var(--text-muted);
    font-size: .78rem;
}

.latest-meta span,
.proposal-row-sub span {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: .25rem .65rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 900;
    line-height: 1.2;
    white-space: nowrap;
}

.status-blue { background: var(--blue-pale); color: var(--blue-accent); }
.status-amber { background: #fffbeb; color: #b45309; }
.status-indigo { background: #eef2ff; color: #4338ca; }
.status-green { background: #ecfdf5; color: #047857; }
.status-orange { background: #fff7ed; color: #c2410c; }
.status-red { background: #fff1f2; color: #be123c; }
.status-slate { background: #f1f5f9; color: #475569; }
.status-soft {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

/* STEPS */
.proposal-steps {
    display: grid;
    grid-template-columns: auto 1fr auto 1fr auto 1fr auto 1fr auto;
    align-items: center;
    gap: .45rem;
    margin: 1.2rem 0 1rem;
    padding: .95rem;
    border-radius: 16px;
    background: #f8fafc;
    border: 1px solid var(--border);
}

.proposal-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .32rem;
    min-width: 54px;
}

.proposal-step span {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    background: #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .76rem;
    font-weight: 900;
}

.proposal-step.done span {
    background: var(--blue-accent);
    color: #fff;
}

.proposal-step p {
    margin: 0;
    color: var(--text-muted);
    font-size: .68rem;
    font-weight: 800;
    text-align: center;
}

.proposal-line {
    height: 3px;
    border-radius: 999px;
    background: #e2e8f0;
}

.proposal-line.active {
    background: var(--blue-accent);
}

.latest-actions {
    display: flex;
    gap: .55rem;
    flex-wrap: wrap;
}

.btn-track-primary,
.btn-track-warning,
.btn-track-ske,
.btn-row-detail,
.btn-row-revisi,
.btn-row-ske {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    border-radius: 12px;
    text-decoration: none;
    font-size: .8rem;
    font-weight: 900;
    border: 1px solid transparent;
    transition: all .2s ease;
}

.btn-track-primary,
.btn-track-warning,
.btn-track-ske {
    padding: .6rem .9rem;
}

.btn-track-primary {
    background: var(--blue-accent);
    color: #fff;
    border-color: var(--blue-accent);
}

.btn-track-primary:hover,
.btn-row-ske:hover {
    background: #1d4ed8;
    color: #fff;
}

.btn-track-warning {
    background: #fff7ed;
    color: #c2410c;
    border-color: #fed7aa;
}

.btn-track-warning:hover,
.btn-row-revisi:hover {
    background: #ffedd5;
    color: #9a3412;
}

.btn-track-ske,
.btn-row-ske {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}

.side-stack {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.mini-ske-card {
    padding: 1rem;
    display: flex;
    gap: .85rem;
}

.mini-ske-icon {
    width: 44px;
    height: 44px;
    border-radius: 15px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.mini-ske-content h3 {
    margin: 0;
    color: var(--navy-deep);
    font-size: .95rem;
    font-weight: 900;
}

.mini-ske-content p {
    margin: .25rem 0 .8rem;
    color: var(--text-muted);
    font-size: .78rem;
    line-height: 1.45;
}

.mini-ske-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .55rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: .8rem;
}

.mini-ske-row span {
    color: var(--text-muted);
    font-weight: 700;
}

.mini-ske-row strong {
    color: var(--navy-deep);
    font-size: 1rem;
    font-weight: 900;
}

.mini-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: var(--blue-accent);
    font-size: .8rem;
    font-weight: 900;
    text-decoration: none;
    margin-top: .8rem;
}

.help-card {
    padding: 1rem;
}

.help-title {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: var(--navy-deep);
    font-size: .9rem;
    font-weight: 900;
    margin-bottom: .75rem;
}

.help-item {
    display: flex;
    gap: .65rem;
    padding: .55rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.help-item:last-child {
    border-bottom: none;
}

.help-item span {
    width: 24px;
    height: 24px;
    border-radius: 999px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    font-weight: 900;
    flex-shrink: 0;
}

.help-item p {
    margin: 0;
    color: var(--text-muted);
    font-size: .78rem;
    line-height: 1.45;
    font-weight: 600;
}

/* LIST */
.proposal-list {
    padding: .75rem;
}

.proposal-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding: .95rem;
    border-radius: 16px;
    border: 1px solid transparent;
    border-bottom: 1px solid #f1f5f9;
}

.proposal-row:last-child {
    border-bottom: none;
}

.proposal-row:hover {
    background: #f8fafc;
    border-color: var(--border);
}

.proposal-row-main {
    min-width: 0;
    flex: 1;
}

.proposal-row-meta {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
    margin-bottom: .35rem;
}

.proposal-row-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .45rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}

.btn-row-detail,
.btn-row-revisi,
.btn-row-ske {
    padding: .48rem .75rem;
}

.btn-row-detail {
    background: #fff;
    color: var(--navy-deep);
    border-color: var(--border);
}

.btn-row-detail:hover {
    background: #f1f5f9;
    color: var(--navy-deep);
}

.btn-row-revisi {
    background: #fff7ed;
    color: #c2410c;
    border-color: #fed7aa;
}

.empty-track {
    padding: 2.5rem 1rem;
    text-align: center;
    color: var(--text-muted);
}

.empty-track.small {
    padding: 2rem 1rem;
}

.empty-track i {
    font-size: 2.2rem;
    color: #cbd5e1;
    display: block;
    margin-bottom: .5rem;
}

.empty-track h3 {
    margin: 0;
    color: var(--navy-deep);
    font-size: 1rem;
    font-weight: 900;
}

.empty-track p {
    margin: .3rem 0 1rem;
    font-size: .85rem;
}

@media (max-width: 1180px) {
    .research-hero,
    .dashboard-main-grid {
        grid-template-columns: 1fr;
    }

    .track-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 760px) {
    .track-grid {
        grid-template-columns: 1fr;
    }

    .hero-actions,
    .latest-actions,
    .proposal-row-actions {
        width: 100%;
    }

    .btn-hero-primary,
    .btn-hero-secondary,
    .btn-track-primary,
    .btn-track-warning,
    .btn-track-ske,
    .btn-row-detail,
    .btn-row-revisi,
    .btn-row-ske {
        width: 100%;
    }

    .latest-top,
    .proposal-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .proposal-steps {
        grid-template-columns: 1fr;
        gap: .55rem;
    }

    .proposal-line {
        width: 3px;
        height: 18px;
        justify-self: center;
    }

    .proposal-step {
        flex-direction: row;
        justify-content: center;
    }
}
</style>
@endpush