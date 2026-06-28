@extends('layouts.peneliti')
@section('title', 'Riwayat Pengajuan')

@section('content')

<div class="page-header">
    <h1>Riwayat Pengajuan</h1>
    <p>Lihat semua pengajuan protokol penelitian Anda</p>
</div>

{{-- ══ FILTER BAR ══════════════════════════════════════════════════ --}}
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('peneliti.riwayat') }}"
          style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;width:100%;">

        <div class="filter-group">
            <label class="kep-label" style="font-size:.78rem;margin-bottom:.3rem;">
                Filter Status
            </label>
            <select name="status" class="kep-select" style="width:240px;">
                <option value="">Semua Status</option>
                @foreach([
                    'new_proposal'                 => 'New Proposal',
                    'assigned_to_secretary'         => 'Assigned to Secretary',
                    'waiting_secretary_decision'    => 'Waiting Secretary Decision',
                    'ready_for_reviewer_assignment' => 'Ready for Reviewer Assignment',
                    'on_review'                    => 'On Review',
                    'revision_required'            => 'Revision Required',
                    'revised'                      => 'Revised',
                    'approved'                     => 'Approved',
                    'approved_with_recommendation'  => 'Approved with Recommendation',
                    'disapproved'                  => 'Disapproved',
                    'issued'                       => 'Issued',
                ] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="kep-label" style="font-size:.78rem;margin-bottom:.3rem;">
                Filter Tahun
            </label>
            <select name="tahun" class="kep-select" style="width:130px;">
                <option value="">Semua Tahun</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
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

<div class="protocol-list">

@forelse($protocols as $item)

    @php
        $statusMap = [
            'new_proposal'                 => ['class' => 'badge-new',       'label' => 'New Proposal'],
            'assigned_to_secretary'         => ['class' => 'badge-review',    'label' => 'Assigned to Secretary'],
            'waiting_secretary_decision'    => ['class' => 'badge-review',    'label' => 'Waiting Secretary Decision'],
            'ready_for_reviewer_assignment' => ['class' => 'badge-review',    'label' => 'Ready for Reviewer Assignment'],
            'on_review'                    => ['class' => 'badge-review',    'label' => 'On Review'],
            'revision_required'            => ['class' => 'badge-revision',  'label' => 'Revision Required'],
            'revised'                      => ['class' => 'badge-review',    'label' => 'Revised'],
            'approved'                     => ['class' => 'badge-approved',  'label' => 'Approved'],
            'approved_with_recommendation'  => ['class' => 'badge-approved',  'label' => 'Approved with Recommendation'],
            'disapproved'                  => ['class' => 'badge-rejected',  'label' => 'Disapproved'],
            'issued'                       => ['class' => 'badge-approved',  'label' => 'Issued'],
        ];

        $afterSubmittedStatuses = [
            'assigned_to_secretary',
            'waiting_secretary_decision',
            'ready_for_reviewer_assignment',
            'on_review',
            'revision_required',
            'revised',
            'approved',
            'approved_with_recommendation',
            'disapproved',
            'issued',
        ];

        $reviewStatuses = [
            'ready_for_reviewer_assignment',
            'on_review',
        ];

        $badge   = $statusMap[$item->status] ?? ['class' => 'badge-new', 'label' => $item->status];
        $tanggal = $item->submitted_at?->translatedFormat('d M Y')
                  ?? $item->created_at->translatedFormat('d M Y');

        $ske = $item->skeDocument;
        $showSkeButton = $ske && in_array($item->status, ['approved', 'issued']);
    @endphp

    <div class="protocol-item" id="protocol-{{ $item->id }}">

        {{-- KIRI --}}
        <div style="flex:1;min-width:0;">

            <div class="protocol-meta" style="margin-bottom:.5rem;">
                <span class="protocol-reg">{{ $item->nomor_registrasi ?? '—' }}</span>
                <span class="kep-badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
            </div>

            <div class="protocol-title" style="font-size:1.08rem;margin-bottom:.55rem;line-height:1.4;">
                {{ $item->title }}
            </div>

            <div class="protocol-sub d-flex gap-2 flex-wrap" style="margin-top:0;">
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

            @if($ske && in_array($item->status, ['approved', 'issued']))
                <div class="ske-mini-status {{ 'ske-mini-' . $ske->status }}">
                    <i class="bi bi-shield-check"></i>
                    SKE:
                    <strong>{{ $ske->statusLabel() }}</strong>
                </div>
            @endif

            {{-- Activity log --}}
            @if($item->status !== 'new_proposal')
                <div style="margin-top:.6rem;">
                    <button type="button"
                            onclick="toggleActivity({{ $item->id }})"
                            class="activity-toggle">
                        <i class="bi bi-chevron-right"
                           id="chevron-{{ $item->id }}"
                           style="transition:transform .2s;font-size:.7rem;"></i>
                        Lihat Activity Log
                    </button>

                    <div id="activity-{{ $item->id }}" class="activity-panel">

                        <div class="activity-entry">
                            <span class="activity-dot dot-done"></span>
                            <div>
                                <div class="activity-text">Pengajuan diterima</div>
                                <div class="activity-time">
                                    {{ $item->submitted_at?->translatedFormat('d M Y, H:i') ?? $item->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                            </div>
                        </div>

                        @if(in_array($item->status, $afterSubmittedStatuses))
                            <div class="activity-entry">
                                <span class="activity-dot dot-done"></span>
                                <div>
                                    <div class="activity-text">Pengajuan diteruskan ke sekretariat</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'assigned_to_secretary')
                            <div class="activity-entry">
                                <span class="activity-dot dot-pending"></span>
                                <div>
                                    <div class="activity-text">Menunggu verifikasi dokumen oleh sekretariat</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'waiting_secretary_decision')
                            <div class="activity-entry">
                                <span class="activity-dot dot-pending"></span>
                                <div>
                                    <div class="activity-text">Menunggu keputusan sekretariat</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'ready_for_reviewer_assignment')
                            <div class="activity-entry">
                                <span class="activity-dot dot-pending"></span>
                                <div>
                                    <div class="activity-text">Menunggu penugasan reviewer</div>
                                </div>
                            </div>
                        @endif

                        @if(in_array($item->status, $reviewStatuses))
                            <div class="activity-entry">
                                <span class="activity-dot dot-done"></span>
                                <div>
                                    <div class="activity-text">Pengajuan masuk tahap review</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'on_review')
                            <div class="activity-entry">
                                <span class="activity-dot dot-pending"></span>
                                <div>
                                    <div class="activity-text">Sedang direview oleh reviewer</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'revision_required')
                            <div class="activity-entry">
                                <span class="activity-dot dot-revision"></span>
                                <div>
                                    <div class="activity-text" style="color:#9a3412;">Revisi diperlukan</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'approved_with_recommendation')
                            <div class="activity-entry">
                                <span class="activity-dot dot-revision"></span>
                                <div>
                                    <div class="activity-text" style="color:#9a3412;">Disetujui dengan rekomendasi — revisi diperlukan</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'revised')
                            <div class="activity-entry">
                                <span class="activity-dot dot-done"></span>
                                <div>
                                    <div class="activity-text">Revisi telah dikirim</div>
                                </div>
                            </div>
                        @endif

                        @if($ske && in_array($item->status, ['approved', 'issued']))
                            @if($ske->status === 'menunggu_konfirmasi')
                                <div class="activity-entry">
                                    <span class="activity-dot dot-ske"></span>
                                    <div>
                                        <div class="activity-text" style="color:#1d4ed8;">SKE menunggu konfirmasi peneliti</div>
                                        @if($ske->dikirim_ke_peneliti_at)
                                            <div class="activity-time">
                                                {{ $ske->dikirim_ke_peneliti_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($ske->status === 'revisi')
                                <div class="activity-entry">
                                    <span class="activity-dot dot-revision"></span>
                                    <div>
                                        <div class="activity-text" style="color:#9a3412;">SKE dikembalikan ke admin untuk diperbaiki</div>
                                        @if($ske->direvisi_at)
                                            <div class="activity-time">
                                                {{ $ske->direvisi_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if(in_array($ske->status, ['menunggu_ttd', 'sudah_ttd', 'terbit']))
                                <div class="activity-entry">
                                    <span class="activity-dot dot-ske"></span>
                                    <div>
                                        <div class="activity-text" style="color:#3730a3;">SKE diteruskan ke ketua untuk tanda tangan</div>
                                        @if($ske->dikirim_ke_ketua_at)
                                            <div class="activity-time">
                                                {{ $ske->dikirim_ke_ketua_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if(in_array($ske->status, ['sudah_ttd', 'terbit']))
                                <div class="activity-entry">
                                    <span class="activity-dot dot-approved"></span>
                                    <div>
                                        <div class="activity-text" style="color:#065f46;">SKE sudah ditandatangani ketua</div>
                                        @if($ske->ditandatangani_at)
                                            <div class="activity-time">
                                                {{ $ske->ditandatangani_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($ske->status === 'terbit')
                                <div class="activity-entry">
                                    <span class="activity-dot dot-approved"></span>
                                    <div>
                                        <div class="activity-text" style="color:#065f46;">SKE telah diterbitkan</div>
                                        @if($ske->diterbitkan_at)
                                            <div class="activity-time">
                                                {{ $ske->diterbitkan_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if(in_array($item->status, ['approved', 'issued']))
                            <div class="activity-entry">
                                <span class="activity-dot dot-approved"></span>
                                <div>
                                    <div class="activity-text" style="color:#065f46;">Pengajuan disetujui</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'approved' && !$ske)
                            <div class="activity-entry">
                                <span class="activity-dot dot-pending"></span>
                                <div>
                                    <div class="activity-text">Menunggu pembuatan SKE oleh admin</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'issued')
                            <div class="activity-entry">
                                <span class="activity-dot dot-approved"></span>
                                <div>
                                    <div class="activity-text" style="color:#065f46;">Surat kelaikan etik telah diterbitkan</div>
                                </div>
                            </div>
                        @endif

                        @if($item->status === 'disapproved')
                            <div class="activity-entry">
                                <span class="activity-dot dot-rejected"></span>
                                <div>
                                    <div class="activity-text" style="color:#991b1b;">Pengajuan tidak disetujui</div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </div>

        {{-- KANAN: AKSI --}}
        <div class="protocol-actions">

            <a href="{{ route('peneliti.riwayat.show', $item->id) }}" class="btn-detail">
                <i class="bi bi-eye" style="font-size:.8rem;"></i>
                Lihat Detail
            </a>

            @if(in_array($item->status, ['revision_required', 'approved_with_recommendation']) && !$item->sudah_kirim_revisi_menunggu_sekretaris)
                <a href="{{ route('peneliti.revisi.show', $item->id) }}"
                   class="btn-kep btn-primary btn-action-small"
                   title="Upload Revisi">
                    <i class="bi bi-upload" style="font-size:.78rem;"></i>
                    Upload Revisi
                </a>
            @endif

            @if($showSkeButton)
                <a href="{{ route('peneliti.ske.show', $ske->id) }}"
                   class="btn-kep btn-ske btn-action-small"
                   title="Cek SKE">
                    <i class="bi bi-shield-check" style="font-size:.78rem;"></i>
                    Cek SKE
                </a>
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

.protocol-item {
    padding: 1.1rem 1.4rem;
    gap: 1.25rem;
}

.protocol-meta {
    gap: .5rem;
}

.protocol-reg {
    font-size: .8rem;
}

.protocol-sub {
    font-size: .8rem;
    gap: .5rem !important;
    color: var(--text-muted);
}

.protocol-actions {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .45rem;
    flex-wrap: wrap;
}

.btn-action-small {
    padding: .38rem .75rem !important;
    font-size: .78rem !important;
}

.btn-ske {
    background: #2563eb;
    color: #fff;
    border: 1px solid #2563eb;
}

.btn-ske:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #fff;
}

.ske-mini-status {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    margin-top: .55rem;
    padding: .32rem .65rem;
    border-radius: 999px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: .76rem;
    font-weight: 600;
}

.ske-mini-status i {
    font-size: .8rem;
}

.ske-mini-menunggu_konfirmasi {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}

.ske-mini-revisi {
    background: #fff7ed;
    color: #9a3412;
    border-color: #fed7aa;
}

.ske-mini-menunggu_ttd {
    background: #eef2ff;
    color: #3730a3;
    border-color: #c7d2fe;
}

.ske-mini-sudah_ttd,
.ske-mini-terbit {
    background: #ecfdf5;
    color: #047857;
    border-color: #a7f3d0;
}

.activity-toggle {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--blue-accent);
    font-size: .78rem;
    font-weight: 600;
    padding: 0;
    display: flex;
    align-items: center;
    gap: .35rem;
}

.activity-panel {
    display: none;
    margin-top: .6rem;
    padding-left: .5rem;
    border-left: 2px solid var(--blue-light);
}

.activity-entry {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    padding: .3rem 0;
    font-size: .78rem;
}

.activity-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: .28rem;
}

.dot-done     { background: #10b981; }
.dot-revision { background: #f97316; }
.dot-approved { background: #059669; }
.dot-rejected { background: #dc3545; }
.dot-pending  { background: #d1d5db; border: 2px solid #9ca3af; }
.dot-ske      { background: #2563eb; }

.activity-text {
    color: var(--navy-deep);
    font-weight: 500;
    line-height: 1.3;
}

.activity-time {
    color: var(--text-muted);
    font-size: .72rem;
    margin-top: .1rem;
}

.flex-wrap {
    flex-wrap: wrap;
}

@media (max-width: 900px) {
    .protocol-item {
        align-items: flex-start;
    }

    .protocol-actions {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 640px) {
    .protocol-actions .btn-kep,
    .protocol-actions .btn-detail {
        padding: .38rem .65rem !important;
        font-size: .76rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleActivity(id) {
    const panel   = document.getElementById('activity-' + id);
    const chevron = document.getElementById('chevron-' + id);

    const open = panel.style.display === 'none';

    panel.style.display = open ? 'block' : 'none';
    chevron.style.transform = open ? 'rotate(90deg)' : 'rotate(0deg)';
}
</script>
@endpush