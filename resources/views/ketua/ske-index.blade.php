@extends('layouts.ketua')
@section('title', 'Tanda Tangan SKE')

@section('content')

<div class="page-header">
    <h1>Tanda Tangan SKE</h1>
    <p>Daftar SKE yang menunggu tanda tangan Anda.</p>
</div>

<div class="ketua-page">

    <div class="info-box-purple">
        <i class="bi bi-info-circle"></i>
        <span>
            Periksa dokumen SKE terlebih dahulu sebelum mengunggah file PDF yang sudah ditandatangani.
            Setelah file diunggah, status SKE akan menjadi <strong>Sudah Ditandatangani</strong> dan admin dapat menerbitkannya ke peneliti.
        </span>
    </div>

    <div class="ketua-panel">
        <div class="panel-header">
            <div>
                <h2>SKE Menunggu TTD</h2>
                <p>Total {{ $skeList->total() }} SKE menunggu tanda tangan.</p>
            </div>
        </div>

        @if($skeList->isEmpty())
            <div class="empty-state-ketua">
                <i class="bi bi-check2-circle"></i>
                <p>Tidak ada SKE yang menunggu tanda tangan.</p>
            </div>
        @else
            <div class="ske-list">
                @foreach($skeList as $ske)
                    <div class="ske-item">
                        <div class="ske-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>

                        <div class="ske-main">
                            <div class="ske-meta">
                                <span class="ske-number">{{ $ske->nomor_surat }}</span>
                                <span class="status-pill">Menunggu TTD</span>
                            </div>

                            <div class="ske-title">
                                {{ $ske->protocol->title ?? '-' }}
                            </div>

                            <div class="ske-sub">
                                <span><i class="bi bi-person"></i> {{ $ske->protocol->user->name ?? '-' }}</span>
                                <span><i class="bi bi-clock"></i> Dikirim {{ $ske->dikirim_ke_ketua_at?->format('d M Y, H:i') ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="ske-actions">
                            @if($ske->file_path)
                                <a href="{{ route('ketua.ske.preview', $ske->id) }}"
                                   target="_blank"
                                   class="btn-outline-purple">
                                    <i class="bi bi-eye"></i>
                                    Preview
                                </a>
                            @endif

                            <a href="{{ route('ketua.ske.show', $ske->id) }}" class="btn-purple">
                                <i class="bi bi-pen"></i>
                                TTD
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($skeList->hasPages())
                <div class="pagination-wrap">
                    {{ $skeList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

@endsection

@push('styles')
<style>
.ketua-page,
.ketua-page * {
    font-family: inherit;
}

.info-box-purple {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: .9rem 1rem;
    margin-bottom: 1rem;
    border-radius: 16px;
    background: #f5f3ff;
    border: 1px solid #ddd6fe;
    color: #5b21b6;
    font-size: .86rem;
    line-height: 1.55;
}

.info-box-purple i {
    font-size: 1.1rem;
    margin-top: .1rem;
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
}

.ske-list {
    padding: .75rem;
}

.ske-item {
    display: grid;
    grid-template-columns: 44px minmax(0, 1fr) auto;
    gap: .85rem;
    align-items: center;
    padding: .95rem;
    border-radius: 16px;
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
    background: #ede9fe;
    color: #7c3aed;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.ske-main {
    min-width: 0;
}

.ske-meta {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
    margin-bottom: .35rem;
}

.ske-number {
    color: #7c3aed;
    font-size: .76rem;
    font-weight: 900;
    font-family: monospace;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: .22rem .6rem;
    border-radius: 999px;
    background: #ede9fe;
    color: #6d28d9;
    font-size: .7rem;
    font-weight: 900;
}

.ske-title {
    color: var(--navy-deep);
    font-size: .92rem;
    font-weight: 900;
    line-height: 1.45;
}

.ske-sub {
    display: flex;
    align-items: center;
    gap: .9rem;
    flex-wrap: wrap;
    margin-top: .38rem;
    color: var(--text-muted);
    font-size: .77rem;
}

.ske-sub span {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
}

.ske-actions {
    display: flex;
    align-items: center;
    gap: .45rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.btn-purple,
.btn-outline-purple {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    padding: .55rem .8rem;
    border-radius: 12px;
    font-size: .8rem;
    font-weight: 900;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .2s ease;
    white-space: nowrap;
}

.btn-purple {
    background: #7c3aed;
    color: #fff;
    border-color: #7c3aed;
}

.btn-purple:hover {
    background: #6d28d9;
    color: #fff;
}

.btn-outline-purple {
    background: #fff;
    color: #6d28d9;
    border-color: #c4b5fd;
}

.btn-outline-purple:hover {
    background: #f5f3ff;
    color: #5b21b6;
}

.pagination-wrap {
    padding: .9rem 1.15rem;
    border-top: 1px solid var(--border);
    background: #f8fafc;
}

.empty-state-ketua {
    padding: 2.8rem 1rem;
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

@media (max-width: 760px) {
    .ske-item {
        grid-template-columns: 44px minmax(0, 1fr);
        align-items: flex-start;
    }

    .ske-actions {
        grid-column: 2;
        width: 100%;
        justify-content: flex-start;
    }

    .btn-purple,
    .btn-outline-purple {
        width: 100%;
    }
}
</style>
@endpush