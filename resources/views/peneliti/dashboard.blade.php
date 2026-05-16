@extends('layouts.peneliti')
@section('title', 'Dashboard Peneliti')

@section('content')

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Selamat datang, {{ auth()->user()->name }}. Pantau status pengajuan Anda di sini.</p>
</div>

{{-- ── Stat Cards ─────────────────────────────────────────── --}}
<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-icon-wrap stat-blue">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value">{{ $totalPengajuan }}</div>
            <div class="stat-label">Total Pengajuan</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap stat-amber">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value">{{ $sedangDiproses }}</div>
            <div class="stat-label">Sedang Diproses</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap stat-green">
            <i class="bi bi-patch-check"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value">{{ $disetujui }}</div>
            <div class="stat-label">Disetujui</div>
        </div>
    </div>

</div>

{{-- ── Pengajuan Terbaru ─────────────────────────────────── --}}
<div class="section-header mb-3">
    <span class="section-title">Pengajuan Terbaru</span>
    <a href="{{ route('peneliti.riwayat') }}" class="btn-kep btn-outline" style="padding:.4rem .9rem;font-size:.82rem;">
        Lihat Semua <i class="bi bi-arrow-right"></i>
    </a>
</div>

<div class="protocol-list">
@forelse($pengajuanTerbaru as $item)

    @php
        $badgeClass = match($item->status) {
            'new_proposal'          => 'badge-new',
            'waiting_verification'  => 'badge-review',
            'under_review'          => 'badge-review',
            'approved'              => 'badge-approved',
            'rejected'              => 'badge-rejected',
            'revision_required'     => 'badge-revision',
            default                 => 'badge-new',
        };

        $statusDisplay = ucwords(str_replace('_', ' ', $item->status));
    @endphp

    <div class="protocol-item">
        <div style="flex:1;min-width:0;">
            <div class="protocol-meta">
                <span class="protocol-reg">{{ $item->nomor_registrasi }}</span>
                <span class="kep-badge {{ $badgeClass }}">{{ $statusDisplay }}</span>
            </div>
            <div class="protocol-title">{{ $item->title }}</div>
            <div class="protocol-sub">
                {{ $item->program_studi }} &bull;
                Diajukan {{ $item->submitted_at?->format('d M Y') ?? $item->created_at->format('d M Y') }}
            </div>
        </div>
        <a href="#" class="btn-detail">
            Lihat Detail <i class="bi bi-chevron-right" style="font-size:.7rem;"></i>
        </a>
    </div>

@empty
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p>Belum ada pengajuan protokol penelitian.</p>
        <a href="{{ route('peneliti.pengajuan.create') }}" class="btn-kep btn-primary"
           style="padding:.5rem 1.2rem;font-size:.845rem;">
            <i class="bi bi-plus"></i> Buat Pengajuan Pertama
        </a>
    </div>
@endforelse
</div>

@endsection