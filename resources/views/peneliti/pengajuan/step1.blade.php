@extends('layouts.peneliti')
@section('title', 'Pengajuan Baru — Langkah 1')

@section('content')

<div class="page-header">
    <h1>Pengajuan Baru</h1>
    <p>Ajukan protokol penelitian Anda untuk mendapatkan ethical clearance</p>
</div>

{{-- Step Indicator --}}
<div class="step-indicator mb-4">
    <div class="step-wrap">
        <div class="step-circle active">1</div>
        <div class="step-label active">Informasi Dasar</div>
    </div>
    <div class="step-connector"></div>
    <div class="step-wrap">
        <div class="step-circle">2</div>
        <div class="step-label">Dokumen</div>
    </div>
    <div class="step-connector"></div>
    <div class="step-wrap">
        <div class="step-circle">3</div>
        <div class="step-label">Konfirmasi</div>
    </div>
</div>

<div class="kep-card">
    <div class="kep-card-title">
        <i class="bi bi-clipboard2-data"></i>
        Informasi Dasar Penelitian
    </div>

    @if($errors->any())
        <div class="kep-alert danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:.25rem;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('peneliti.pengajuan.step1') }}" method="POST" class="kep-form">
        @csrf

        {{-- Judul --}}
        <div class="form-group">
            <label class="kep-label" for="title">
                Judul Penelitian <span class="req">*</span>
            </label>
            <input type="text" id="title" name="title"
                   class="kep-input @error('title') is-invalid @enderror"
                   placeholder="Masukkan judul penelitian secara lengkap"
                   value="{{ old('title', session('pengajuan_step1.title')) }}">
            @error('title')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Program Studi & Durasi --}}
        <div class="form-row">
            <div class="form-group" style="margin-bottom:0;">
                <label class="kep-label" for="program_studi">
                    Program Studi <span class="req">*</span>
                </label>
                <input type="text" id="program_studi" name="program_studi"
                       class="kep-input @error('program_studi') is-invalid @enderror"
                       placeholder="Contoh: Kedokteran, Keperawatan, Farmasi"
                       value="{{ old('program_studi', session('pengajuan_step1.program_studi')) }}">
                @error('program_studi')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="kep-label" for="durasi_penelitian">
                    Durasi Penelitian (bulan) <span class="req">*</span>
                </label>
                <input type="number" id="durasi_penelitian" name="durasi_penelitian"
                       class="kep-input @error('durasi_penelitian') is-invalid @enderror"
                       placeholder="12" min="1" max="120"
                       value="{{ old('durasi_penelitian', session('pengajuan_step1.durasi_penelitian', 12)) }}">
                @error('durasi_penelitian')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="form-group mt-3">
            <label class="kep-label" for="ringkasan_penelitian">
                Ringkasan Penelitian <span class="req">*</span>
            </label>
            <textarea id="ringkasan_penelitian" name="ringkasan_penelitian"
                      class="kep-textarea @error('ringkasan_penelitian') is-invalid @enderror"
                      rows="5"
                      placeholder="Deskripsikan latar belakang, tujuan, dan metodologi penelitian (minimal 50 karakter)">{{ old('ringkasan_penelitian', session('pengajuan_step1.ringkasan_penelitian')) }}</textarea>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.3rem;">
                @error('ringkasan_penelitian')
                    <div class="field-error">{{ $message }}</div>
                @else
                    <span></span>
                @enderror
                <span class="text-sm text-muted" id="charCount">0 karakter</span>
            </div>
        </div>

        {{-- Sumber Pendanaan --}}
        <div class="form-group">
            <label class="kep-label" for="sumber_pendanaan">
                Sumber Pendanaan
                <span style="font-size:.75rem;font-weight:400;color:var(--text-muted);">(Opsional)</span>
            </label>
            <input type="text" id="sumber_pendanaan" name="sumber_pendanaan"
                   class="kep-input"
                   placeholder="Nama institusi atau organisasi pendana"
                   value="{{ old('sumber_pendanaan', session('pengajuan_step1.sumber_pendanaan')) }}">
        </div>

        <div class="d-flex" style="justify-content:flex-end;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);">
            <button type="submit" class="btn-kep btn-primary">
                Selanjutnya <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    const textarea  = document.getElementById('ringkasan_penelitian');
    const charCount = document.getElementById('charCount');

    function updateCount() {
        const len = textarea.value.length;
        charCount.textContent = len + ' karakter';
        charCount.style.color = len < 50 ? 'var(--danger)' : 'var(--text-muted)';
    }

    textarea.addEventListener('input', updateCount);
    updateCount();
</script>
@endpush