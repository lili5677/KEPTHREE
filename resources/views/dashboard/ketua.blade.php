@extends('layouts.ketua')
@section('title', 'Dashboard Ketua')

@section('content')

<div class="page-header">
    <h1>Dashboard Ketua</h1>
    <p>Selamat datang, {{ auth()->user()->name }}. Pantau SKE yang menunggu tanda tangan dan riwayat TTD Anda.</p>
</div>

@if(blank(auth()->user()->nip))
    <div class="nip-alert-card">
        <div class="nip-alert-icon">
            <i class="bi bi-person-vcard"></i>
        </div>

        <div class="nip-alert-content">
            <strong>Lengkapi NIP terlebih dahulu</strong>
            <p>
                Akun ketua belum memiliki NIP. Admin tidak dapat memilih Anda sebagai ketua penandatangan SKE sebelum NIP diisi.
            </p>

            <form method="POST" action="{{ route('ketua.profil.nip.update') }}" class="nip-form">
                @csrf
                @method('PATCH')

                <input type="text"
                       name="nip"
                       class="nip-input"
                       placeholder="Masukkan NIP"
                       value="{{ old('nip', auth()->user()->nip) }}"
                       required>

                <button type="submit" class="nip-submit">
                    <i class="bi bi-save"></i>
                    Simpan NIP
                </button>
            </form>

            @error('nip')
                <div class="nip-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
@endif

<div class="ketua-dashboard">

    {{-- STAT CARDS --}}
    <div class="stat-grid">

        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="bi bi-pen"></i>
            </div>
            <div>
                <div class="stat-value">{{ $menungguTtd }}</div>
                <div class="stat-label">Menunggu TTD</div>
                <div class="stat-desc">SKE yang perlu ditandatangani</div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <div class="stat-value">{{ $sudahTtd }}</div>
                <div class="stat-label">Sudah TTD</div>
                <div class="stat-desc">SKE sudah diunggah ke sistem</div>
            </div>
        </div>

        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="bi bi-file-earmark-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $terbit }}</div>
                <div class="stat-label">SKE Terbit</div>
                <div class="stat-desc">SKE sudah dipublish admin</div>
            </div>
        </div>

        <div class="stat-card slate">
            <div class="stat-icon">
                <i class="bi bi-archive"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalDitangani }}</div>
                <div class="stat-label">Total Ditangani</div>
                <div class="stat-desc">Total SKE dalam tanggung jawab Anda</div>
            </div>
        </div>

    </div>

    {{-- MAIN GRID --}}
    <div class="dashboard-grid">

        {{-- SKE MENUNGGU --}}
        <div class="ketua-panel">
            <div class="panel-header">
                <div>
                    <h2>SKE Menunggu Tanda Tangan</h2>
                    <p>Daftar SKE terbaru yang perlu Anda periksa dan tandatangani.</p>
                </div>

                <a href="{{ route('ketua.ske.index') }}" class="panel-link">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @if($skeMenunggu->isEmpty())
                <div class="empty-state-ketua">
                    <i class="bi bi-check2-circle"></i>
                    <p>Tidak ada SKE yang menunggu tanda tangan.</p>
                </div>
            @else
                <div class="ske-list">
                    @foreach($skeMenunggu as $ske)
                        <div class="ske-item">
                            <div class="ske-icon purple">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>

                            <div class="ske-main">
                                <div class="ske-meta">
                                    <span>{{ $ske->nomor_surat }}</span>
                                    <span class="status-pill purple">Menunggu TTD</span>
                                </div>

                                <div class="ske-title">
                                    {{ \Illuminate\Support\Str::limit($ske->protocol->title ?? '-', 75) }}
                                </div>

                                <div class="ske-sub">
                                    <span><i class="bi bi-person"></i> {{ $ske->protocol->user->name ?? '-' }}</span>
                                    <span><i class="bi bi-clock"></i> {{ $ske->dikirim_ke_ketua_at?->format('d M Y, H:i') ?? $ske->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>

                            <a href="{{ route('ketua.ske.show', $ske->id) }}" class="btn-open">
                                Detail
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- RIWAYAT TERBARU --}}
    <div class="ketua-panel">
        <div class="panel-header">
            <div>
                <h2>Riwayat TTD Terbaru</h2>
                <p>SKE yang baru saja Anda tandatangani.</p>
            </div>

            <a href="{{ route('ketua.riwayat') }}" class="panel-link">
                Riwayat TTD <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @if($riwayatTerbaru->isEmpty())
            <div class="empty-state-ketua">
                <i class="bi bi-clock-history"></i>
                <p>Belum ada riwayat tanda tangan SKE.</p>
            </div>
        @else
            <div class="ske-list">
                @foreach($riwayatTerbaru as $ske)
                    <div class="ske-item">
                        <div class="ske-icon green">
                            <i class="bi bi-check-circle"></i>
                        </div>

                        <div class="ske-main">
                            <div class="ske-meta">
                                <span>{{ $ske->nomor_surat }}</span>
                                <span class="status-pill green">{{ $ske->statusLabel() }}</span>
                            </div>

                            <div class="ske-title">
                                {{ \Illuminate\Support\Str::limit($ske->protocol->title ?? '-', 80) }}
                            </div>

                            <div class="ske-sub">
                                <span><i class="bi bi-person"></i> {{ $ske->protocol->user->name ?? '-' }}</span>
                                <span><i class="bi bi-calendar-check"></i> {{ $ske->ditandatangani_at?->format('d M Y, H:i') ?? '-' }}</span>
                            </div>
                        </div>

                        <a href="{{ route('ketua.riwayat.show', $ske->id) }}" class="btn-open">
                            Detail
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection

@push('styles')
<style>
.ketua-dashboard,
.ketua-dashboard * {
    font-family: inherit;
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

.stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.stat-card.purple .stat-icon { background: #ede9fe; color: #7c3aed; }
.stat-card.green .stat-icon { background: #ecfdf5; color: #047857; }
.stat-card.blue .stat-icon { background: #eff6ff; color: #2563eb; }
.stat-card.slate .stat-icon { background: #f1f5f9; color: #475569; }

.stat-value {
    color: var(--navy-deep);
    font-size: 1.75rem;
    font-weight: 900;
    line-height: 1;
}

.stat-label {
    margin-top: .25rem;
    color: var(--navy-deep);
    font-size: .86rem;
    font-weight: 800;
}

.stat-desc {
    margin-top: .25rem;
    color: var(--text-muted);
    font-size: .76rem;
    line-height: 1.35;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}

.ketua-panel {
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
    font-size: 1rem;
    font-weight: 900;
}

.panel-header p {
    margin: .2rem 0 0;
    color: var(--text-muted);
    font-size: .8rem;
    line-height: 1.45;
}

.panel-link {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: #7c3aed;
    font-size: .82rem;
    font-weight: 900;
    text-decoration: none;
    white-space: nowrap;
}

.ske-list {
    padding: .75rem;
}

.ske-item {
    display: grid;
    grid-template-columns: 44px minmax(0, 1fr) auto;
    gap: .8rem;
    align-items: center;
    padding: .9rem;
    border-radius: 15px;
    border-bottom: 1px solid #f1f5f9;
}

.ske-item:last-child {
    border-bottom: none;
}

.ske-item:hover {
    background: #f8fafc;
}

.ske-icon {
    width: 44px;
    height: 44px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.ske-icon.purple { background: #ede9fe; color: #7c3aed; }
.ske-icon.green { background: #ecfdf5; color: #047857; }

.ske-main {
    min-width: 0;
}

.ske-meta {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
    margin-bottom: .32rem;
}

.ske-meta > span:first-child {
    color: #7c3aed;
    font-size: .76rem;
    font-weight: 900;
    font-family: monospace;
}

.ske-title {
    color: var(--navy-deep);
    font-size: .9rem;
    font-weight: 900;
    line-height: 1.4;
}

.ske-sub {
    display: flex;
    align-items: center;
    gap: .9rem;
    flex-wrap: wrap;
    margin-top: .35rem;
    color: var(--text-muted);
    font-size: .76rem;
}

.ske-sub span {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: .22rem .6rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 900;
}

.status-pill.purple {
    background: #ede9fe;
    color: #6d28d9;
}

.status-pill.green {
    background: #ecfdf5;
    color: #047857;
}

.btn-open {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .5rem .75rem;
    border-radius: 12px;
    background: #fff;
    color: var(--navy-deep);
    border: 1px solid var(--border);
    font-size: .78rem;
    font-weight: 900;
    text-decoration: none;
    transition: all .2s ease;
}

.btn-open:hover {
    background: #ede9fe;
    color: #6d28d9;
    border-color: #c4b5fd;
}

.empty-state-ketua {
    padding: 2.6rem 1rem;
    text-align: center;
    color: var(--text-muted);
}

.empty-state-ketua i {
    display: block;
    font-size: 2.2rem;
    color: #cbd5e1;
    margin-bottom: .55rem;
}

.empty-state-ketua p {
    margin: 0;
    font-size: .86rem;
    font-weight: 700;
}

@media (max-width: 1180px) {
    .stat-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 720px) {
    .stat-grid {
        grid-template-columns: 1fr;
    }

    .panel-header {
        flex-direction: column;
    }

    .ske-item {
        grid-template-columns: 44px minmax(0, 1fr);
    }

    .btn-open {
        grid-column: 2;
        justify-self: flex-start;
    }
}

.nip-alert-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.15rem;
    margin-bottom: 1.25rem;
    border-radius: 18px;
    background: #f5f3ff;
    border: 1px solid #ddd6fe;
    box-shadow: 0 10px 24px rgba(124, 58, 237, .08);
}

.nip-alert-icon {
    width: 46px;
    height: 46px;
    border-radius: 15px;
    background: #ede9fe;
    color: #7c3aed;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.nip-alert-content {
    flex: 1;
    min-width: 0;
}

.nip-alert-content strong {
    display: block;
    color: var(--navy-deep);
    font-size: .98rem;
    font-weight: 900;
    margin-bottom: .25rem;
}

.nip-alert-content p {
    margin: 0 0 .85rem;
    color: var(--text-muted);
    font-size: .84rem;
    line-height: 1.5;
}

.nip-form {
    display: flex;
    gap: .65rem;
    flex-wrap: wrap;
}

.nip-input {
    flex: 1;
    min-width: 220px;
    border: 1px solid #c4b5fd;
    border-radius: 12px;
    padding: .68rem .85rem;
    font-size: .86rem;
    color: var(--navy-deep);
    outline: none;
    background: #fff;
}

.nip-input:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, .12);
}

.nip-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    padding: .68rem .95rem;
    border-radius: 12px;
    background: #7c3aed;
    color: #fff;
    border: 1px solid #7c3aed;
    font-size: .84rem;
    font-weight: 900;
    cursor: pointer;
}

.nip-submit:hover {
    background: #6d28d9;
}

.nip-error {
    margin-top: .5rem;
    color: #be123c;
    font-size: .78rem;
    font-weight: 700;
}

@media (max-width: 680px) {
    .nip-alert-card {
        flex-direction: column;
    }

    .nip-form,
    .nip-input,
    .nip-submit {
        width: 100%;
    }
}
</style>
@endpush