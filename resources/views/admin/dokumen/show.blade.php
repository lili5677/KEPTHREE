@extends('layouts.admin')

@section('title', 'Detail Dokumen')

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1.4fr 0.8fr;
        gap: 20px;
        align-items: start;
    }

    .card-detail {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .card-header-detail {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
        background: #f8f9fc;
    }

    .card-header-detail h2 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .card-header-detail p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 4px 0 0;
    }

    .card-body-detail {
        padding: 20px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 170px 1fr;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid var(--border);
        font-size: 13.5px;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: var(--text-muted);
        font-weight: 600;
    }

    .info-value {
        color: var(--text-primary);
        font-weight: 500;
        line-height: 1.5;
    }

    .summary-box {
        font-size: 13.5px;
        line-height: 1.7;
        color: var(--text-primary);
        background: #fafafa;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 15px;
        white-space: pre-line;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .badge-amber  { background: #fef3c7; color: #92400e; }
    .badge-blue   { background: #dbeafe; color: #1e40af; }
    .badge-indigo { background: #e0e7ff; color: #3730a3; }
    .badge-orange { background: #ffedd5; color: #9a3412; }
    .badge-green  { background: #dcfce7; color: #166534; }
    .badge-red    { background: #fee2e2; color: #991b1b; }
    .badge-slate  { background: #f1f5f9; color: #475569; }

    .document-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .document-table thead tr {
        background: #f8f9fc;
        border-bottom: 1px solid var(--border);
    }

    .document-table th {
        padding: 11px 14px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    .document-table td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
        color: var(--text-primary);
        vertical-align: top;
    }

    .document-table tr:last-child td {
        border-bottom: none;
    }

    .empty-document {
        padding: 28px 18px;
        text-align: center;
        color: var(--text-muted);
        font-size: 13.5px;
    }

    .action-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .side-note {
        font-size: 13px;
        line-height: 1.6;
        color: var(--text-muted);
    }

    @media (max-width: 900px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .info-row {
            grid-template-columns: 1fr;
            gap: 3px;
        }
    }
</style>
@endpush

@section('content')

@php
    $statusLabels = [
        'new_proposal'         => 'Proposal Baru',
        'waiting_verification' => 'Menunggu Verifikasi',
        'under_review'         => 'Dalam Review',
        'on_review'            => 'Dalam Review',
        'revision_required'    => 'Perlu Revisi',
        'approved'             => 'Disetujui',
        'rejected'             => 'Ditolak',
    ];

    $colorMap = [
        'new_proposal'         => 'amber',
        'waiting_verification' => 'blue',
        'under_review'         => 'indigo',
        'on_review'            => 'indigo',
        'revision_required'    => 'orange',
        'approved'             => 'green',
        'rejected'             => 'red',
    ];

    $status = $protocol->status ?? '-';
    $statusLabel = method_exists($protocol, 'statusLabel')
        ? $protocol->statusLabel()
        : ($statusLabels[$status] ?? ucwords(str_replace('_', ' ', $status)));

    $statusColor = $colorMap[$status] ?? 'slate';

    $documents = $protocol->documents ?? collect();
@endphp

    {{-- Header --}}
    <div class="page-header">
        <h1 class="page-title">Detail Dokumen</h1>
        <p class="page-subtitle">
            Detail dokumen pengajuan protocol penelitian.
        </p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Action --}}
    <div class="action-bar">
        <a href="{{ route('admin.dokumen.index') }}" class="btn btn-secondary">
            Kembali
        </a>

        <a href="{{ route('admin.dokumen.download', $protocol->id) }}" class="btn btn-primary">
            Download Dokumen
        </a>
    </div>

    <div class="detail-grid">

        {{-- Main Detail --}}
        <div class="card-detail">
            <div class="card-header-detail">
                <h2>{{ $protocol->title ?? '-' }}</h2>
                <p>
                    {{ $protocol->nomor_registrasi ?? 'PRO-'.$protocol->id }}
                </p>
            </div>

            <div class="card-body-detail">

                <div class="info-row">
                    <div class="info-label">Kode Dokumen</div>
                    <div class="info-value">
                        {{ $protocol->nomor_registrasi ?? 'PRO-'.$protocol->id }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Judul Penelitian</div>
                    <div class="info-value">
                        {{ $protocol->title ?? '-' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Nama Peneliti</div>
                    <div class="info-value">
                        {{ $protocol->user->name ?? '-' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Program Studi</div>
                    <div class="info-value">
                        {{ $protocol->program_studi ?? '-' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Sumber Pendanaan</div>
                    <div class="info-value">
                        {{ $protocol->sumber_pendanaan ?? '-' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Durasi Penelitian</div>
                    <div class="info-value">
                        {{ $protocol->durasi_penelitian ?? '-' }}
                        {{ $protocol->durasi_penelitian ? 'bulan' : '' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tanggal Ajuan</div>
                    <div class="info-value">
                        {{ $protocol->submitted_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Update Terakhir</div>
                    <div class="info-value">
                        {{ $protocol->updated_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Sekretariat</div>
                    <div class="info-value">
                        {{ $protocol->sekretariat->name ?? '-' }}
                    </div>
                </div>

                <div style="margin-top: 18px;">
                    <div class="info-label" style="margin-bottom: 8px;">
                        Ringkasan Penelitian
                    </div>

                    <div class="summary-box">
                        {{ $protocol->ringkasan_penelitian ?? '-' }}
                    </div>
                </div>

            </div>
        </div>

        {{-- Side Info --}}
        <div class="card-detail">
            <div class="card-header-detail">
                <h2>Informasi Dokumen</h2>
                <p>Daftar dokumen yang terhubung dengan pengajuan ini.</p>
            </div>

            <div class="card-body-detail" style="padding: 0;">
                @if($documents->isEmpty())
                    <div class="empty-document">
                        Belum ada dokumen yang terunggah untuk pengajuan ini.
                    </div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="document-table">
                            <thead>
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Tipe</th>
                                    <th>Upload</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td>
                                            {{ $document->original_name
                                                ?? $document->file_name
                                                ?? $document->filename
                                                ?? $document->name
                                                ?? 'Dokumen #'.$document->id }}
                                        </td>
                                        <td>
                                            {{ $document->document_type
                                                ?? $document->type
                                                ?? $document->category
                                                ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $document->created_at?->format('Y-m-d') ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection