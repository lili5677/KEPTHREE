@extends('layouts.reviewer')
@section('title', 'Edit Riwayat Review')

@section('content')

@php
    $decisionOptions = [
        'approved' => 'Layak',
        'approved_with_recommendation' => 'Layak dengan Rekomendasi',
        'minor_revision' => 'Revisi Minor',
        'major_revision' => 'Revisi Mayor',
        'rejected' => 'Tidak Layak',
    ];
@endphp

<div class="page-header">
    <h1>Edit Riwayat Review</h1>
    <p>Perbarui keputusan dan catatan review yang sudah Anda submit</p>
</div>

<div class="kep-card">
    <div class="kep-card-title">
        <i class="bi bi-pencil-square"></i> Form Edit Review
    </div>

    <div class="review-info-box">
        <div>
            <span class="info-label">No. Registrasi</span>
            <strong>{{ $assignment->protocol->nomor_registrasi ?? 'PRO-' . $assignment->protocol->id }}</strong>
        </div>

        <div>
            <span class="info-label">Judul Penelitian</span>
            <strong>{{ $assignment->protocol->title ?? '-' }}</strong>
        </div>

        <div>
            <span class="info-label">Peneliti</span>
            <strong>{{ $assignment->protocol->user->name ?? '-' }}</strong>
            <small>{{ $assignment->protocol->user->email ?? '-' }}</small>
        </div>

        <div>
            <span class="info-label">Tanggal Review</span>
            <strong>
                @if($review->reviewed_at)
                    {{ $review->reviewed_at->translatedFormat('d M Y, H:i') }} WIB
                @else
                    -
                @endif
            </strong>
        </div>
    </div>

    <form action="{{ route('reviewer.riwayat.update', $assignment->id) }}" method="POST" class="review-edit-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="keputusan">Keputusan Review</label>
            <select name="keputusan" id="keputusan" class="form-control @error('keputusan') is-invalid @enderror" required>
                <option value="">-- Pilih Keputusan --</option>
                @foreach($decisionOptions as $value => $label)
                    <option value="{{ $value }}" {{ old('keputusan', $review->keputusan) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            @error('keputusan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="catatan">Catatan Review</label>
            <textarea
                name="catatan"
                id="catatan"
                rows="8"
                class="form-control @error('catatan') is-invalid @enderror"
                placeholder="Tuliskan catatan hasil review..."
                required>{{ old('catatan', $review->catatan) }}</textarea>

            @error('catatan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('reviewer.riwayat') }}" class="btn-kep btn-outline">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            <button type="submit" class="btn-kep btn-primary">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
.review-info-box {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    padding: 1rem;
    margin-bottom: 1.25rem;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: var(--blue-pale);
}

.review-info-box div {
    display: flex;
    flex-direction: column;
    gap: .25rem;
}

.info-label {
    font-size: .76rem;
    color: var(--text-muted);
    font-weight: 600;
}

.review-info-box strong {
    color: var(--navy-deep);
    font-size: .9rem;
    line-height: 1.35;
}

.review-info-box small {
    color: var(--text-muted);
    font-size: .78rem;
}

.review-edit-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: .45rem;
    font-size: .85rem;
    font-weight: 600;
    color: var(--navy-deep);
}

.form-control {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: .7rem .85rem;
    font-size: .9rem;
    color: var(--navy-deep);
    background: #fff;
    outline: none;
}

.form-control:focus {
    border-color: var(--blue-light);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
}

textarea.form-control {
    resize: vertical;
    min-height: 180px;
    line-height: 1.55;
}

.invalid-feedback {
    margin-top: .35rem;
    color: #b91c1c;
    font-size: .78rem;
}

.is-invalid {
    border-color: #dc2626;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    margin-top: .5rem;
}

@media (max-width: 768px) {
    .review-info-box {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .form-actions .btn-kep {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush