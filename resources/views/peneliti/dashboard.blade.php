@extends('layouts.peneliti')
@section('title', 'Dashboard Peneliti')

@section('content')

@php
    $userId = auth()->id();

    $skeQuery = \App\Models\SkeDocument::whereHas('protocol', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    });

    $skeMenungguKonfirmasi = (clone $skeQuery)->where('status', 'menunggu_konfirmasi')->count();
    $skeRevisi             = (clone $skeQuery)->where('status', 'revisi')->count();
    $skeMenungguTtd        = (clone $skeQuery)->where('status', 'menunggu_ttd')->count();
    $skeTerbit             = (clone $skeQuery)->where('status', 'terbit')->count();

    $butuhRevisi = $pengajuanTerbaru
        ->whereIn('status', ['revision_required', 'approved_with_recommendation'])
        ->count();

    $statusMap = [
        'new_proposal'                 => ['label' => 'Pengajuan Baru', 'class' => 'badge-blue'],
        'waiting_verification'         => ['label' => 'Menunggu Verifikasi', 'class' => 'badge-amber'],
        'under_review'                 => ['label' => 'Sedang Review', 'class' => 'badge-indigo'],
        'approved'                     => ['label' => 'Disetujui', 'class' => 'badge-green'],
        'rejected'                     => ['label' => 'Ditolak', 'class' => 'badge-red'],
        'revision_required'            => ['label' => 'Perlu Revisi', 'class' => 'badge-orange'],
        'approved_with_recommendation' => ['label' => 'Revisi Rekomendasi', 'class' => 'badge-orange'],
    ];
@endphp

<div class="peneliti-dashboard">

    <div class="page-header dashboard-header-clean">
        <div>
            <h1>Dashboard</h1>
            <p>
                Selamat datang, {{ auth()->user()->name }}. Pantau status pengajuan, revisi, dan SKE Anda di sini.
            </p>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="stat-grid">

        <div class="stat-card">
            <div class="stat-icon-wrap stat-blue">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $totalPengajuan }}</div>
                <div class="stat-label">Total Pengajuan</div>
                <div class="stat-desc">Seluruh protokol yang Anda ajukan</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-amber">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $sedangDiproses }}</div>
                <div class="stat-label">Sedang Diproses</div>
                <div class="stat-desc">Menunggu verifikasi atau review</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-green">
                <i class="bi bi-patch-check"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $disetujui }}</div>
                <div class="stat-label">Disetujui</div>
                <div class="stat-desc">Pengajuan sudah approved</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-orange">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $butuhRevisi }}</div>
                <div class="stat-label">Butuh Revisi</div>
                <div class="stat-desc">Perlu tindakan dari peneliti</div>
            </div>
        </div>

    </div>

    {{-- SKE SUMMARY --}}
    <div class="dashboard-grid">

        <div class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h2>Ringkasan SKE</h2>
                    <p>SKE akan muncul setelah proposal disetujui sekretaris dan admin membuat dokumen SKE.</p>
                </div>

                <span class="panel-pill">
                    {{ $skeTerbit }} Terbit
                </span>
            </div>

            <div class="ske-status-grid">

                <div class="ske-status-item status-blue">
                    <span class="ske-dot"></span>
                    <div>
                        <strong>{{ $skeMenungguKonfirmasi }}</strong>
                        <p>Perlu Dicek</p>
                    </div>
                </div>

                <div class="ske-status-item status-orange">
                    <span class="ske-dot"></span>
                    <div>
                        <strong>{{ $skeRevisi }}</strong>
                        <p>Dalam Revisi Admin</p>
                    </div>
                </div>

                <div class="ske-status-item status-indigo">
                    <span class="ske-dot"></span>
                    <div>
                        <strong>{{ $skeMenungguTtd }}</strong>
                        <p>Menunggu TTD Ketua</p>
                    </div>
                </div>

                <div class="ske-status-item status-green">
                    <span class="ske-dot"></span>
                    <div>
                        <strong>{{ $skeTerbit }}</strong>
                        <p>SKE Terbit</p>
                    </div>
                </div>

            </div>

            <div class="panel-action">
                <a href="{{ route('peneliti.riwayat') }}">
                    Lihat Riwayat Pengajuan
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h2>Aksi Cepat</h2>
                    <p>Shortcut untuk aktivitas utama peneliti.</p>
                </div>
            </div>

            <div class="quick-list">

                <a href="{{ route('peneliti.pengajuan.create') }}" class="quick-item">
                    <div class="quick-icon blue">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div>
                        <strong>Buat Pengajuan</strong>
                        <span>Ajukan protokol penelitian baru</span>
                    </div>
                    <i class="bi bi-chevron-right quick-arrow"></i>
                </a>

                <a href="{{ route('peneliti.riwayat') }}" class="quick-item">
                    <div class="quick-icon green">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <strong>Riwayat Pengajuan</strong>
                        <span>Lihat status dan dokumen pengajuan</span>
                    </div>
                    <i class="bi bi-chevron-right quick-arrow"></i>
                </a>

            </div>
        </div>

    </div>

    {{-- PENGAJUAN TERBARU --}}
    <div class="section-header section-header-clean">
        <div>
            <span class="section-title">Pengajuan Terbaru</span>
            <p>Daftar pengajuan terakhir yang Anda kirimkan.</p>
        </div>

        <a href="{{ route('peneliti.riwayat') }}" class="btn-kep btn-outline btn-small-dashboard">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="protocol-list">
        @forelse($pengajuanTerbaru as $item)

            @php
                $ske = $item->skeDocument ?? null;

                $statusData = $statusMap[$item->status] ?? [
                    'label' => ucwords(str_replace('_', ' ', $item->status)),
                    'class' => 'badge-slate',
                ];

                $showSkeButton = $ske && in_array($item->status, ['approved', 'issued']);
                $showRevisionButton = in_array($item->status, ['revision_required', 'approved_with_recommendation']);
            @endphp

            <div class="protocol-item">
                <div class="protocol-main">
                    <div class="protocol-meta">
                        <span class="protocol-reg">
                            {{ $item->nomor_registrasi ?? 'ID-' . $item->id }}
                        </span>

                        <span class="kep-badge {{ $statusData['class'] }}">
                            {{ $statusData['label'] }}
                        </span>

                        @if($ske)
                            <span class="kep-badge badge-soft-blue">
                                SKE: {{ $ske->statusLabel() }}
                            </span>
                        @endif
                    </div>

                    <div class="protocol-title">
                        {{ $item->title }}
                    </div>

                    <div class="protocol-sub">
                        <span>
                            <i class="bi bi-mortarboard"></i>
                            {{ $item->program_studi ?? 'Program studi belum diisi' }}
                        </span>
                        <span>
                            <i class="bi bi-calendar"></i>
                            Diajukan {{ $item->submitted_at?->format('d M Y') ?? $item->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>

                <div class="protocol-actions">
                    <a href="{{ route('peneliti.riwayat.show', $item->id) }}" class="btn-detail">
                        Lihat Detail
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    @if($showRevisionButton)
                        <a href="{{ route('peneliti.revisi.show', $item->id) }}" class="btn-revision-dashboard">
                            <i class="bi bi-pencil-square"></i>
                            Upload Revisi
                        </a>
                    @endif

                    @if($showSkeButton)
                        <a href="{{ route('peneliti.ske.show', $ske->id) }}" class="btn-ske-dashboard">
                            <i class="bi bi-shield-check"></i>
                            Cek SKE
                        </a>
                    @endif
                </div>
            </div>

        @empty
            <div class="empty-state dashboard-empty">
                <i class="bi bi-inbox"></i>
                <p>Belum ada pengajuan protokol penelitian.</p>
                <a href="{{ route('peneliti.pengajuan.create') }}" class="btn-kep btn-primary btn-empty-action">
                    <i class="bi bi-plus"></i>
                    Buat Pengajuan Pertama
                </a>
            </div>
        @endforelse
    </div>

</div>

@endsection

@push('styles')
<style>
.peneliti-dashboard,
.peneliti-dashboard * {
    font-family: inherit;
}

.dashboard-header-clean {
    margin-bottom: 1.2rem;
}

.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.stat-card {
    display: flex;
    align-items: flex-start;
    gap: .9rem;
    padding: 1rem;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
}

.stat-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.stat-blue {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.stat-amber {
    background: #fffbeb;
    color: #b45309;
}

.stat-green {
    background: #ecfdf5;
    color: #047857;
}

.stat-orange {
    background: #fff7ed;
    color: #c2410c;
}

.stat-body {
    min-width: 0;
}

.stat-value {
    color: var(--navy-deep);
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1;
}

.stat-label {
    color: var(--navy-deep);
    font-size: .86rem;
    font-weight: 800;
    margin-top: .25rem;
}

.stat-desc {
    color: var(--text-muted);
    font-size: .76rem;
    margin-top: .25rem;
    line-height: 1.35;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.25fr) minmax(320px, .75fr);
    gap: 1.25rem;
    margin-bottom: 1.35rem;
}

.dashboard-panel {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    overflow: hidden;
}

.panel-header {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .8rem;
}

.panel-header h2 {
    margin: 0;
    color: var(--navy-deep);
    font-size: .98rem;
    font-weight: 800;
}

.panel-header p {
    margin: .18rem 0 0;
    color: var(--text-muted);
    font-size: .8rem;
    line-height: 1.4;
}

.panel-pill {
    padding: .3rem .7rem;
    border-radius: 999px;
    background: var(--blue-pale);
    color: var(--blue-accent);
    font-size: .75rem;
    font-weight: 800;
    white-space: nowrap;
}

.ske-status-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .8rem;
    padding: 1.15rem;
}

.ske-status-item {
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: .85rem;
    background: #fff;
    display: flex;
    align-items: flex-start;
    gap: .65rem;
}

.ske-status-item strong {
    display: block;
    color: var(--navy-deep);
    font-size: 1.3rem;
    line-height: 1;
    margin-bottom: .3rem;
}

.ske-status-item p {
    margin: 0;
    color: var(--text-muted);
    font-size: .75rem;
    font-weight: 600;
    line-height: 1.3;
}

.ske-dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    margin-top: .2rem;
    flex-shrink: 0;
}

.status-blue .ske-dot { background: #2563eb; }
.status-orange .ske-dot { background: #f97316; }
.status-indigo .ske-dot { background: #4f46e5; }
.status-green .ske-dot { background: #16a34a; }

.panel-action {
    padding: .95rem 1.15rem;
    border-top: 1px solid var(--border);
    background: #f8fafc;
    text-align: right;
}

.panel-action a {
    color: var(--blue-accent);
    text-decoration: none;
    font-size: .84rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}

.quick-list {
    padding: .75rem;
}

.quick-item {
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr) auto;
    gap: .8rem;
    align-items: center;
    padding: .85rem;
    border-radius: 14px;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .2s ease;
}

.quick-item:hover {
    background: #f8fafc;
    border-color: var(--border);
}

.quick-item.muted {
    cursor: default;
}

.quick-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.18rem;
}

.quick-icon.blue {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.quick-icon.green {
    background: #ecfdf5;
    color: #047857;
}

.quick-icon.amber {
    background: #fffbeb;
    color: #b45309;
}

.quick-item strong {
    display: block;
    color: var(--navy-deep);
    font-size: .88rem;
    margin-bottom: .15rem;
}

.quick-item span {
    display: block;
    color: var(--text-muted);
    font-size: .76rem;
    line-height: 1.35;
}

.quick-arrow {
    color: var(--text-muted);
    font-size: .85rem;
}

.section-header-clean {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .85rem;
}

.section-header-clean p {
    margin: .18rem 0 0;
    color: var(--text-muted);
    font-size: .8rem;
}

.btn-small-dashboard {
    padding: .42rem .9rem !important;
    font-size: .82rem !important;
}

.protocol-list {
    display: flex;
    flex-direction: column;
    gap: .75rem;
}

.protocol-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.1rem;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
}

.protocol-main {
    flex: 1;
    min-width: 0;
}

.protocol-meta {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
    margin-bottom: .4rem;
}

.protocol-reg {
    color: var(--text-muted);
    font-size: .75rem;
    font-weight: 800;
}

.kep-badge {
    display: inline-flex;
    align-items: center;
    padding: .22rem .6rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 800;
    line-height: 1.2;
}

.badge-blue {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.badge-soft-blue {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.badge-amber {
    background: #fffbeb;
    color: #b45309;
}

.badge-indigo {
    background: #eef2ff;
    color: #4338ca;
}

.badge-green {
    background: #ecfdf5;
    color: #047857;
}

.badge-orange {
    background: #fff7ed;
    color: #c2410c;
}

.badge-red {
    background: #fff1f2;
    color: #be123c;
}

.badge-slate {
    background: #f1f5f9;
    color: #475569;
}

.protocol-title {
    color: var(--navy-deep);
    font-size: .95rem;
    font-weight: 800;
    line-height: 1.4;
    margin-bottom: .35rem;
}

.protocol-sub {
    display: flex;
    align-items: center;
    gap: .9rem;
    flex-wrap: wrap;
    color: var(--text-muted);
    font-size: .78rem;
}

.protocol-sub span {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
}

.protocol-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .45rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}

.btn-detail,
.btn-revision-dashboard,
.btn-ske-dashboard {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: .55rem .78rem;
    border-radius: 12px;
    font-size: .78rem;
    font-weight: 800;
    text-decoration: none;
    border: 1px solid transparent;
    white-space: nowrap;
    transition: all .2s ease;
}

.btn-detail {
    background: #f8fafc;
    color: var(--navy-deep);
    border-color: var(--border);
}

.btn-detail:hover {
    background: #f1f5f9;
    color: var(--navy-deep);
}

.btn-revision-dashboard {
    background: #fff7ed;
    color: #c2410c;
    border-color: #fed7aa;
}

.btn-revision-dashboard:hover {
    background: #ffedd5;
    color: #9a3412;
}

.btn-ske-dashboard {
    background: var(--blue-accent);
    color: #fff;
    border-color: var(--blue-accent);
}

.btn-ske-dashboard:hover {
    background: #1d4ed8;
    color: #fff;
}

.dashboard-empty {
    background: var(--white);
    border: 1px dashed var(--border);
    border-radius: 18px;
    padding: 2.5rem 1rem;
}

.dashboard-empty i {
    font-size: 2.2rem;
    color: #cbd5e1;
    display: block;
    margin-bottom: .6rem;
}

.dashboard-empty p {
    color: var(--text-muted);
    font-size: .9rem;
    font-weight: 600;
}

.btn-empty-action {
    padding: .55rem 1.2rem !important;
    font-size: .84rem !important;
}

@media (max-width: 1180px) {
    .stat-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    .stat-grid,
    .ske-status-grid {
        grid-template-columns: 1fr;
    }

    .section-header-clean {
        align-items: flex-start;
        flex-direction: column;
    }

    .section-header-clean .btn-kep {
        width: 100%;
        justify-content: center;
    }

    .protocol-item {
        align-items: flex-start;
        flex-direction: column;
    }

    .protocol-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .btn-detail,
    .btn-revision-dashboard,
    .btn-ske-dashboard {
        width: 100%;
    }
}
</style>
@endpush