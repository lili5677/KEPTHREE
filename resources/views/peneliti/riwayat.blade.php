@extends('layouts.peneliti')
@section('title', 'Riwayat Pengajuan')

@section('content')

<div class="page-header">
    <h1 style="font-size:2rem;">Riwayat Pengajuan</h1>
    <p>Lihat semua pengajuan protokol penelitian Anda</p>
</div>

{{-- ══ FILTER BAR ══════════════════════════════════════════════════ --}}
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('peneliti.riwayat') }}"
          style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;width:100%;">

        {{-- Filter Status --}}
        <div class="filter-group">
            <label class="kep-label" style="font-size:.78rem;margin-bottom:.3rem;">
                Filter Status
            </label>
            <select name="status" class="kep-select" style="width:200px;">
                <option value="">Semua Status</option>
                @foreach([
                    'new_proposal'         => 'New Proposal',
                    'waiting_verification' => 'Waiting Verification',
                    'under_review'         => 'Under Review',
                    'revision_required'    => 'Revision Required',
                    'approved'             => 'Approved',
                    'rejected'             => 'Rejected',
                ] as $value => $label)
                    <option value="{{ $value }}"
                        {{ request('status') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Tahun --}}
        <div class="filter-group">
            <label class="kep-label" style="font-size:.78rem;margin-bottom:.3rem;">
                Filter Tahun
            </label>
            <select name="tahun" class="kep-select" style="width:130px;">
                <option value="">Semua Tahun</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}"
                        {{ request('tahun') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-kep btn-primary" style="padding:.55rem 1rem;">
            <i class="bi bi-funnel"></i> Filter
        </button>

        @if(request()->hasAny(['status', 'tahun']))
            <a href="{{ route('peneliti.riwayat') }}" class="btn-reset-filter">
                <i class="bi bi-x-circle-fill"></i> Reset Filter
            </a>
        @endif

    </form>
</div>

{{-- ══ RESULT COUNT ════════════════════════════════════════════════ --}}
@if(request()->hasAny(['status', 'tahun']))
    <p class="text-muted text-sm mb-3">
        Menampilkan <strong>{{ $protocols->total() }}</strong> pengajuan
        @if(request('status'))
            dengan status
            <strong>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', request('status'))) }}</strong>
        @endif
        @if(request('tahun'))
            tahun <strong>{{ request('tahun') }}</strong>
        @endif
    </p>
@endif

{{-- ══ LIST PROTOKOL ════════════════════════════════════════════════ --}}
<div class="protocol-list">

@forelse($protocols as $item)

    @php
        $statusMap = [
            'new_proposal'         => ['class' => 'badge-new',       'label' => 'New Proposal'],
            'waiting_verification' => ['class' => 'badge-review',    'label' => 'Waiting Verification'],
            'under_review'         => ['class' => 'badge-review',    'label' => 'Under Review'],
            'revision_required'    => ['class' => 'badge-revision',  'label' => 'Revision Required'],
            'approved'             => ['class' => 'badge-approved',  'label' => 'Approved'],
            'rejected'             => ['class' => 'badge-rejected',  'label' => 'Rejected'],
        ];

        $badge      = $statusMap[$item->status] ?? ['class' => 'badge-new', 'label' => $item->status];
        $tanggal    = $item->submitted_at?->translatedFormat('d M Y')
                      ?? $item->created_at->translatedFormat('d M Y');
        $hasDocumen = $item->documents->isNotEmpty();
    @endphp

    <div class="protocol-item" id="protocol-{{ $item->id }}">

        {{-- ── Kiri: info utama ────────────────────────────────── --}}
        <div style="flex:1;min-width:0;">

            <div class="protocol-meta">
                <span class="protocol-reg">{{ $item->nomor_registrasi ?? '—' }}</span>
                <span class="kep-badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
            </div>

            <div class="protocol-title">{{ $item->title }}</div>

            <div class="protocol-sub d-flex gap-2 flex-wrap" style="margin-top:.2rem;">
                <span>
                    <i class="bi bi-mortarboard" style="font-size:.8rem;"></i>
                    {{ $item->program_studi }}
                </span>
                <span>·</span>
                <span>
                    <i class="bi bi-calendar3" style="font-size:.8rem;"></i>
                    {{ $tanggal }}
                </span>
            </div>

            {{-- Activity log (collapsible) --}}
            @if($item->status !== 'new_proposal')
                <div style="margin-top:.6rem;">
                    <button type="button"
                            onclick="toggleActivity({{ $item->id }})"
                            style="background:none;border:none;cursor:pointer;
                                   color:var(--blue-accent);font-size:.78rem;
                                   font-weight:600;padding:0;display:flex;
                                   align-items:center;gap:.35rem;">
                        <i class="bi bi-chevron-right"
                           id="chevron-{{ $item->id }}"
                           style="transition:transform .2s;font-size:.7rem;"></i>
                        Lihat Activity Log
                    </button>

                    <div id="activity-{{ $item->id }}"
                         style="display:none;margin-top:.6rem;padding-left:.5rem;
                                border-left:2px solid var(--blue-light);">

                        <div class="activity-entry">
                            <span class="activity-dot dot-done"></span>
                            <div>
                                <div class="activity-text">Pengajuan diterima</div>
                                <div class="activity-time">
                                    {{ $item->submitted_at?->translatedFormat('d M Y, H:i') ?? $item->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                            </div>
                        </div>

                        @if(in_array($item->status, ['waiting_verification','under_review','revision_required','approved','rejected']))
                            <div class="activity-entry">
                                <span class="activity-dot dot-done"></span>
                                <div><div class="activity-text">Sedang diverifikasi sekretariat</div></div>
                            </div>
                        @endif

                        @if(in_array($item->status, ['under_review','revision_required','approved','rejected']))
                            <div class="activity-entry">
                                <span class="activity-dot dot-done"></span>
                                <div><div class="activity-text">Sedang dalam proses review</div></div>
                            </div>
                        @endif

                        @if($item->status === 'revision_required')
                            <div class="activity-entry">
                                <span class="activity-dot dot-revision"></span>
                                <div><div class="activity-text" style="color:#9a3412;">Revisi diperlukan</div></div>
                            </div>
                        @endif

                        @if($item->status === 'approved')
                            <div class="activity-entry">
                                <span class="activity-dot dot-approved"></span>
                                <div><div class="activity-text" style="color:#065f46;">Disetujui</div></div>
                            </div>
                        @endif

                        @if($item->status === 'rejected')
                            <div class="activity-entry">
                                <span class="activity-dot dot-rejected"></span>
                                <div><div class="activity-text" style="color:#991b1b;">Ditolak</div></div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </div>

        {{-- ── Kanan: aksi ─────────────────────────────────────── --}}
        <div class="d-flex gap-1 align-center" style="flex-shrink:0;">

            {{-- Lihat Detail → ke halaman detail --}}
            <a href="{{ route('peneliti.riwayat.show', $item->id) }}" class="btn-detail">
                <i class="bi bi-eye" style="font-size:.8rem;"></i>
                Lihat Detail
            </a>

            {{-- Upload Revisi (hanya jika revision_required) --}}
            @if($item->status === 'revision_required')
                <a href="#"
                   class="btn-kep btn-primary"
                   style="padding:.38rem .75rem;font-size:.78rem;"
                   title="Upload Revisi">
                    <i class="bi bi-upload" style="font-size:.78rem;"></i>
                    <span class="d-none d-md-inline">Upload Revisi</span>
                </a>
            @endif

            {{-- Download Dokumen --}}
            @if($hasDocumen)
                <div class="dropdown-wrap" style="position:relative;">
                    <button type="button"
                            class="btn-kep btn-outline"
                            style="padding:.38rem .75rem;font-size:.78rem;"
                            title="Download Dokumen"
                            onclick="toggleDropdown({{ $item->id }})">
                        <i class="bi bi-download" style="font-size:.78rem;"></i>
                    </button>
                    <div id="dropdown-{{ $item->id }}"
                         style="display:none;position:absolute;right:0;top:calc(100% + 4px);
                                min-width:220px;background:var(--white);
                                border:1px solid var(--border);border-radius:var(--radius-sm);
                                box-shadow:var(--shadow-md);z-index:50;padding:.35rem 0;">
                        @foreach($item->documents as $doc)
                            <a href="{{ route('peneliti.riwayat.download', $doc->id) }}"
                               class="dropdown-doc-item">
                                <i class="bi bi-file-earmark-pdf" style="color:var(--blue-accent);font-size:.85rem;"></i>
                                <span>{{ $doc->label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <button class="btn-kep btn-outline"
                        style="padding:.38rem .75rem;font-size:.78rem;opacity:.45;cursor:not-allowed;"
                        disabled title="Belum ada dokumen">
                    <i class="bi bi-download" style="font-size:.78rem;"></i>
                </button>
            @endif

        </div>
    </div>

@empty
    <div class="empty-state">
        <i class="bi bi-folder2-open"></i>
        <p>
            @if(request()->hasAny(['status', 'tahun']))
                Tidak ada pengajuan yang cocok dengan filter yang dipilih.
            @else
                Belum ada pengajuan. Mulai buat pengajuan pertama Anda!
            @endif
        </p>
        @if(request()->hasAny(['status', 'tahun']))
            <a href="{{ route('peneliti.riwayat') }}" class="btn-kep btn-primary">
                <i class="bi bi-x-circle"></i> Hapus Filter
            </a>
        @else
            <a href="{{ route('peneliti.pengajuan.create') }}" class="btn-kep btn-primary">
                <i class="bi bi-plus"></i> Buat Pengajuan
            </a>
        @endif
    </div>
@endforelse
</div>

{{-- ══ PAGINATION ══════════════════════════════════════════════════ --}}
@if($protocols->hasPages())
    <div style="margin-top:1.5rem;">
        {{ $protocols->withQueryString()->links() }}
    </div>
@endif

@endsection

@push('styles')
<style>
.btn-reset-filter {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .55rem 1rem;
    border-radius: var(--radius-sm);
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    border: 1.5px solid #fca5a5;
    background: #fff1f2;
    color: #dc2626;
    text-decoration: none;
    transition: all var(--transition);
}
.btn-reset-filter:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #fff;
}

.activity-entry {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    padding: .3rem 0;
    font-size: .78rem;
}
.activity-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: .28rem;
}
.dot-done     { background: #10b981; }
.dot-revision { background: #f97316; }
.dot-approved { background: #059669; }
.dot-rejected { background: #dc3545; }
.dot-pending  { background: #d1d5db; border: 2px solid #9ca3af; }

.activity-text { color: var(--navy-deep); font-weight: 500; line-height: 1.3; }
.activity-time { color: var(--text-muted); font-size: .72rem; margin-top: .1rem; }

.dropdown-doc-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .55rem 1rem;
    font-size: .8rem;
    color: var(--navy-deep);
    font-weight: 500;
    transition: background var(--transition);
    white-space: nowrap;
}
.dropdown-doc-item:hover {
    background: var(--blue-pale);
    color: var(--blue-accent);
}

.flex-wrap { flex-wrap: wrap; }

@media (max-width: 640px) {
    .d-none.d-md-inline { display: none !important; }
}
@media (min-width: 641px) {
    .d-none.d-md-inline { display: inline !important; }
}
</style>
@endpush

@push('scripts')
<script>
function toggleActivity(id) {
    const panel   = document.getElementById('activity-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const open    = panel.style.display === 'none';
    panel.style.display     = open ? 'block' : 'none';
    chevron.style.transform = open ? 'rotate(90deg)' : 'rotate(0deg)';
}

function toggleDropdown(id) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(function (el) {
        if (el.id !== 'dropdown-' + id) el.style.display = 'none';
    });
    const dd = document.getElementById('dropdown-' + id);
    dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.dropdown-wrap')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(function (el) {
            el.style.display = 'none';
        });
    }
});
</script>
@endpush