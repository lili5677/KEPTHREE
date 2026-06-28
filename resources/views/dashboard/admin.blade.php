@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
@php
    $pengajuanTerbaru = $pengajuanTerbaru ?? collect();
    $skeTerbaru = $skeTerbaru ?? collect();

    $statusMap = [
        'new_proposal' => ['label' => 'Pengajuan Baru', 'class' => 'status-blue'],
        'waiting_verification' => ['label' => 'Menunggu Verifikasi', 'class' => 'status-amber'],
        'submitted' => ['label' => 'Submitted', 'class' => 'status-blue'],
        'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'class' => 'status-amber'],
        'pending_verification' => ['label' => 'Menunggu Verifikasi', 'class' => 'status-amber'],
        'under_review' => ['label' => 'Sedang Review', 'class' => 'status-indigo'],
        'in_review' => ['label' => 'Sedang Review', 'class' => 'status-indigo'],
        'review' => ['label' => 'Sedang Review', 'class' => 'status-indigo'],
        'approved' => ['label' => 'Approved', 'class' => 'status-green'],
        'approved_with_recommendation' => ['label' => 'Revisi Rekomendasi', 'class' => 'status-orange'],
        'revision_required' => ['label' => 'Perlu Revisi', 'class' => 'status-orange'],
        'rejected' => ['label' => 'Rejected', 'class' => 'status-red'],
        'disapproved' => ['label' => 'Disapproved', 'class' => 'status-red'],
    ];

    $skeStatusMap = [
        'menunggu_konfirmasi' => ['label' => 'Menunggu Konfirmasi', 'class' => 'status-amber'],
        'revisi' => ['label' => 'Revisi', 'class' => 'status-orange'],
        'menunggu_ttd' => ['label' => 'Menunggu TTD', 'class' => 'status-indigo'],
        'sudah_ttd' => ['label' => 'Sudah TTD', 'class' => 'status-blue'],
        'terbit' => ['label' => 'Terbit', 'class' => 'status-green'],
    ];
@endphp

<div class="admin-dashboard">

    {{-- HERO --}}
    <div class="admin-hero">
        <div>
            <div class="hero-badge">
                <i class="bi bi-speedometer2"></i>
                Dashboard Admin
            </div>

            <h1>Halo, {{ auth()->user()->name }}</h1>

            <p>
                Pantau ringkasan pengguna, pengajuan proposal, proses review, dan penerbitan SKE
                dalam sistem KEPTHREE.
            </p>
        </div>

        <div class="hero-side">
            <div class="side-label">Total Pengajuan</div>
            <div class="side-number">{{ $totalPengajuan ?? 0 }}</div>
            <div class="side-text">
                Seluruh proposal yang tercatat di sistem.
            </div>
        </div>
    </div>

    {{-- MAIN STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
                <div class="stat-label">Total User</div>
                <div class="stat-desc">Semua akun terdaftar</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="bi bi-folder2-open"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pengajuanAktif ?? 0 }}</div>
                <div class="stat-label">Pengajuan Aktif</div>
                <div class="stat-desc">Verifikasi atau review</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pengajuanRevisi ?? 0 }}</div>
                <div class="stat-label">Butuh Revisi</div>
                <div class="stat-desc">Revisi rekomendasi</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-patch-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pengajuanDisetujui ?? 0 }}</div>
                <div class="stat-label">Approved</div>
                <div class="stat-desc">Proposal disetujui</div>
            </div>
        </div>
    </div>

    {{-- ROLE + SKE SUMMARY --}}
    <div class="dashboard-grid">

        <div class="panel">
            <div class="panel-heading">
                <div>
                    <h2>Ringkasan Role Pengguna</h2>
                    <p>Komposisi akun berdasarkan role dalam sistem.</p>
                </div>

                <a href="{{ route('admin.users.index') }}" class="panel-link">
                    Kelola User
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="role-grid">
                <div class="role-card">
                    <span>Admin</span>
                    <strong>{{ $totalAdmin ?? 0 }}</strong>
                </div>

                <div class="role-card">
                    <span>Peneliti</span>
                    <strong>{{ $totalPeneliti ?? 0 }}</strong>
                </div>

                <div class="role-card">
                    <span>Sekretariat</span>
                    <strong>{{ $totalSekretariat ?? 0 }}</strong>
                </div>

                <div class="role-card">
                    <span>Reviewer</span>
                    <strong>{{ $totalReviewer ?? 0 }}</strong>
                </div>

                <div class="role-card">
                    <span>Ketua</span>
                    <strong>{{ $totalKetua ?? 0 }}</strong>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                <div>
                    <h2>Ringkasan SKE</h2>
                    <p>Status penerbitan Surat Kelayakan Etik.</p>
                </div>

                <a href="{{ route('admin.ethical-clearance.index') }}" class="panel-link">
                    Kelola SKE
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="ske-grid">
                <div class="ske-card">
                    <span>Total SKE</span>
                    <strong>{{ $totalSke ?? 0 }}</strong>
                </div>

                <div class="ske-card">
                    <span>Menunggu Konfirmasi</span>
                    <strong>{{ $skeMenungguKonfirmasi ?? 0 }}</strong>
                </div>

                <div class="ske-card">
                    <span>Revisi</span>
                    <strong>{{ $skeRevisi ?? 0 }}</strong>
                </div>

                <div class="ske-card">
                    <span>Menunggu TTD</span>
                    <strong>{{ $skeMenungguTtd ?? 0 }}</strong>
                </div>

                <div class="ske-card">
                    <span>Sudah TTD</span>
                    <strong>{{ $skeSudahTtd ?? 0 }}</strong>
                </div>

                <div class="ske-card">
                    <span>Terbit</span>
                    <strong>{{ $skeTerbit ?? 0 }}</strong>
                </div>
            </div>
        </div>

    </div>

    {{-- QUICK ACTIONS --}}
    <div class="quick-actions">
        <a href="{{ route('admin.users.index') }}" class="quick-card">
            <div class="quick-icon">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <h3>Manajemen User</h3>
                <p>Tambah, ubah, aktifkan, atau nonaktifkan akun pengguna.</p>
            </div>
        </a>

        <a href="{{ route('admin.sekretaris.index') }}" class="quick-card">
            <div class="quick-icon">
                <i class="bi bi-person-check"></i>
            </div>
            <div>
                <h3>Assign Sekretaris</h3>
                <p>Tentukan sekretaris untuk menangani proposal.</p>
            </div>
        </a>

        <a href="{{ route('admin.dokumen.index') }}" class="quick-card">
            <div class="quick-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <h3>Dokumen Proposal</h3>
                <p>Lihat dan kelola dokumen pengajuan penelitian.</p>
            </div>
        </a>

        <a href="{{ route('admin.ethical-clearance.index') }}" class="quick-card">
            <div class="quick-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h3>Ethical Clearance</h3>
                <p>Kelola proses pembuatan dan penerbitan SKE.</p>
            </div>
        </a>
    </div>

    {{-- LATEST DATA --}}
    <div class="dashboard-grid">

        <div class="panel">
            <div class="panel-heading">
                <div>
                    <h2>Pengajuan Terbaru</h2>
                    <p>Proposal terbaru yang masuk ke sistem.</p>
                </div>

                <a href="{{ route('admin.dokumen.index') }}" class="panel-link">
                    Lihat Semua
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="list-area">
                @forelse($pengajuanTerbaru as $item)
                    @php
                        $statusData = $statusMap[$item->status] ?? [
                            'label' => ucwords(str_replace('_', ' ', $item->status ?? '-')),
                            'class' => 'status-slate',
                        ];
                    @endphp

                    <div class="list-row">
                        <div class="list-main">
                            <div class="list-meta">
                                <span class="code">
                                    {{ $item->nomor_registrasi ?? 'PRO-' . $item->id }}
                                </span>

                                <span class="status-pill {{ $statusData['class'] }}">
                                    {{ $statusData['label'] }}
                                </span>
                            </div>

                            <h3>{{ $item->title }}</h3>

                            <p>
                                <i class="bi bi-person"></i>
                                {{ $item->user->name ?? '-' }}
                            </p>
                        </div>

                        <a href="{{ route('admin.dokumen.show', $item->id) }}" class="btn-detail">
                            Detail
                        </a>
                    </div>
                @empty
                    <div class="empty-box">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada pengajuan terbaru.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                <div>
                    <h2>SKE Terbaru</h2>
                    <p>Surat Kelayakan Etik terbaru dalam sistem.</p>
                </div>

                <a href="{{ route('admin.ethical-clearance.index') }}" class="panel-link">
                    Lihat Semua
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="list-area">
                @forelse($skeTerbaru as $ske)
                    @php
                        $skeStatus = $skeStatusMap[$ske->status] ?? [
                            'label' => ucwords(str_replace('_', ' ', $ske->status ?? '-')),
                            'class' => 'status-slate',
                        ];
                    @endphp

                    <div class="list-row">
                        <div class="list-main">
                            <div class="list-meta">
                                <span class="code">
                                    SKE-{{ $ske->id }}
                                </span>

                                <span class="status-pill {{ $skeStatus['class'] }}">
                                    {{ $skeStatus['label'] }}
                                </span>
                            </div>

                            <h3>{{ $ske->protocol->title ?? 'Judul tidak tersedia' }}</h3>

                            <p>
                                <i class="bi bi-person"></i>
                                {{ $ske->protocol->user->name ?? '-' }}
                            </p>
                        </div>

                        <a href="{{ route('admin.ethical-clearance.index') }}" class="btn-detail">
                            Kelola
                        </a>
                    </div>
                @empty
                    <div class="empty-box">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada data SKE terbaru.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
.admin-dashboard,
.admin-dashboard * {
    font-family: inherit;
}

.admin-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 260px;
    gap: 1rem;
    padding: 1.35rem;
    margin-bottom: 1.25rem;
    border-radius: 22px;
    background:
        radial-gradient(circle at top left, rgba(26, 61, 99, .12), transparent 35%),
        linear-gradient(135deg, #ffffff 0%, #eef6ff 100%);
    border: 1px solid #d0e3f0;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .35rem .75rem;
    border-radius: 999px;
    background: #dbedf7;
    color: #1A3D63;
    font-size: .78rem;
    font-weight: 800;
    margin-bottom: .75rem;
}

.admin-hero h1 {
    margin: 0;
    color: #0A1931;
    font-size: 1.55rem;
    font-weight: 900;
}

.admin-hero p {
    margin: .45rem 0 0;
    color: #5b7a96;
    font-size: .9rem;
    line-height: 1.6;
    max-width: 740px;
}

.hero-side {
    background: #fff;
    border: 1px solid #bfdbfe;
    border-radius: 18px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.side-label {
    color: #5b7a96;
    font-size: .78rem;
    font-weight: 800;
}

.side-number {
    color: #1A3D63;
    font-size: 2.4rem;
    font-weight: 900;
    line-height: 1;
    margin: .35rem 0;
}

.side-text {
    color: #5b7a96;
    font-size: .78rem;
    line-height: 1.4;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: .9rem;
    margin-bottom: 1.25rem;
}

.stat-card {
    display: flex;
    gap: .8rem;
    align-items: flex-start;
    padding: 1rem;
    background: #fff;
    border: 1px solid #d0e3f0;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
}

.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-icon.blue { background: #dbedf7; color: #1A3D63; }
.stat-icon.amber { background: #fffbeb; color: #b45309; }
.stat-icon.orange { background: #fff7ed; color: #c2410c; }
.stat-icon.green { background: #ecfdf5; color: #047857; }

.stat-value {
    color: #0A1931;
    font-size: 1.6rem;
    font-weight: 900;
    line-height: 1;
}

.stat-label {
    margin-top: .28rem;
    color: #0A1931;
    font-size: .84rem;
    font-weight: 800;
}

.stat-desc {
    margin-top: .25rem;
    color: #5b7a96;
    font-size: .74rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}

.panel,
.quick-card {
    background: #fff;
    border: 1px solid #d0e3f0;
    border-radius: 20px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    overflow: hidden;
}

.panel-heading {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid #d0e3f0;
    background: #f8fafc;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .8rem;
}

.panel-heading h2 {
    margin: 0;
    color: #0A1931;
    font-size: 1rem;
    font-weight: 900;
}

.panel-heading p {
    margin: .2rem 0 0;
    color: #5b7a96;
    font-size: .8rem;
    line-height: 1.45;
}

.panel-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: #1A3D63;
    font-size: .82rem;
    font-weight: 800;
    text-decoration: none;
    white-space: nowrap;
}

.role-grid,
.ske-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem;
    padding: 1rem;
}

.role-card,
.ske-card {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: .85rem;
    background: #f9fafb;
}

.role-card span,
.ske-card span {
    display: block;
    color: #5b7a96;
    font-size: .78rem;
    font-weight: 800;
}

.role-card strong,
.ske-card strong {
    display: block;
    margin-top: .3rem;
    color: #0A1931;
    font-size: 1.45rem;
    font-weight: 900;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: .9rem;
    margin-bottom: 1.25rem;
}

.quick-card {
    display: flex;
    gap: .85rem;
    padding: 1rem;
    text-decoration: none;
    color: inherit;
    transition: transform .2s ease, box-shadow .2s ease;
}

.quick-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
}

.quick-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: #dbedf7;
    color: #1A3D63;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.quick-card h3 {
    margin: 0;
    color: #0A1931;
    font-size: .9rem;
    font-weight: 900;
}

.quick-card p {
    margin: .25rem 0 0;
    color: #5b7a96;
    font-size: .76rem;
    line-height: 1.45;
}

.list-area {
    padding: .75rem;
}

.list-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding: .9rem;
    border-radius: 16px;
    border-bottom: 1px solid #f1f5f9;
}

.list-row:last-child {
    border-bottom: none;
}

.list-row:hover {
    background: #f8fafc;
}

.list-main {
    min-width: 0;
    flex: 1;
}

.list-meta {
    display: flex;
    gap: .45rem;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: .35rem;
}

.code {
    color: #64748b;
    font-size: .74rem;
    font-weight: 900;
}

.list-row h3 {
    margin: 0;
    color: #0A1931;
    font-size: .92rem;
    font-weight: 900;
    line-height: 1.45;
}

.list-row p {
    margin: .35rem 0 0;
    color: #5b7a96;
    font-size: .78rem;
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

.status-blue { background: #dbedf7; color: #1A3D63; }
.status-amber { background: #fffbeb; color: #b45309; }
.status-indigo { background: #eef2ff; color: #4338ca; }
.status-green { background: #ecfdf5; color: #047857; }
.status-orange { background: #fff7ed; color: #c2410c; }
.status-red { background: #fff1f2; color: #be123c; }
.status-slate { background: #f1f5f9; color: #475569; }

.btn-detail {
    align-self: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .48rem .8rem;
    border-radius: 12px;
    border: 1px solid #d0e3f0;
    background: #fff;
    color: #0A1931;
    font-size: .78rem;
    font-weight: 900;
    text-decoration: none;
    white-space: nowrap;
}

.btn-detail:hover {
    background: #f1f5f9;
    color: #0A1931;
}

.empty-box {
    padding: 2rem 1rem;
    text-align: center;
    color: #64748b;
}

.empty-box i {
    display: block;
    font-size: 2rem;
    color: #cbd5e1;
    margin-bottom: .5rem;
}

.empty-box p {
    margin: 0;
    font-size: .85rem;
}

@media (max-width: 1180px) {
    .admin-hero,
    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid,
    .quick-actions {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 760px) {
    .stats-grid,
    .quick-actions,
    .role-grid,
    .ske-grid {
        grid-template-columns: 1fr;
    }

    .admin-hero {
        padding: 1rem;
    }

    .list-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .btn-detail {
        width: 100%;
    }

    .panel-heading {
        flex-direction: column;
    }
}
</style>
@endpush