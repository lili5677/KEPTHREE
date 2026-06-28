@extends('layouts.sekretariat')

@section('title', 'Detail Verifikasi – Sistem KEP')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800" style="font-family: 'Instrument Sans', sans-serif;">Detail Verifikasi - PRO-{{ $protocol->id }}</h1>
        <a href="{{ route('sekretariat.verifikasi.index') }}" class="text-purple-600 hover:underline flex items-center gap-1" style="font-family: 'Instrument Sans', sans-serif;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Info Proposal -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <p style="font-family: 'Instrument Sans', sans-serif;"><strong>Judul:</strong> {{ $protocol->title }}</p>
        <p style="font-family: 'Instrument Sans', sans-serif;"><strong>Peneliti:</strong> {{ $protocol->user->name ?? '-' }} ({{ $protocol->user->email ?? '-' }})</p>
        <p style="font-family: 'Instrument Sans', sans-serif;"><strong>Status saat ini:</strong> <span class="text-yellow-600">{{ $protocol->status }}</span></p>
    </div>

    {{-- PB-17: Dokumen revisi yang diunggah Peneliti merespons status
         'Revision Required' (dokumen tidak lengkap) sebelumnya --}}
    @if(isset($riwayatRevisi) && $riwayatRevisi->isNotEmpty())
        <div class="bg-orange-50 border border-orange-200 rounded-xl shadow-sm p-5 mb-6">
            <h2 class="text-xl font-semibold mb-3 text-orange-800" style="font-family: 'Instrument Sans', sans-serif;">
                <i class="fas fa-paperclip"></i> Dokumen Pelengkap dari Peneliti
            </h2>

            <div class="space-y-3">
                @foreach($riwayatRevisi as $rev)
                    <div class="bg-white border border-orange-100 rounded-lg p-3 flex justify-between items-start gap-4">
                        <div>
                            <p class="font-medium text-gray-800" style="font-family: 'Instrument Sans', sans-serif;">
                                <i class="fas fa-file-alt text-orange-500"></i>
                                {{ $rev->original_filename ?: 'Dokumen Revisi' }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1" style="font-family: 'Instrument Sans', sans-serif;">
                                Diunggah: {{ $rev->submitted_at?->translatedFormat('d M Y, H:i') ?? $rev->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                            <p class="text-gray-700 mt-1" style="font-family: 'Instrument Sans', sans-serif;">{{ $rev->catatan_revisi }}</p>
                        </div>
                        <a href="{{ route('peneliti.revisi.download', $rev->id) }}"
                           class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-lg text-xs flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-download"></i> Unduh
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('sekretariat.verifikasi.check', $protocol->id) }}">
        @csrf

        <!-- Kelengkapan Dokumen -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
            <h2 class="text-xl font-semibold mb-4" style="font-family: 'Instrument Sans', sans-serif;">Kelengkapan Dokumen</h2>
            <div class="space-y-3">
                @php
                    $wajibTypes = ['formular_pengajuan', 'formular_ringkasan'];
                @endphp

                @forelse($documents as $doc)
                    @php
                        $isWajib = in_array($doc->type, $wajibTypes);
                        $label = match($doc->type) {
                            'formular_pengajuan' => 'Formulir Pengajuan Telaah Etik Baru',
                            'formular_ringkasan' => 'Formulir Ringkasan Protokol Penelitian',
                            'pendukung' => $doc->name ?? 'Dokumen Pendukung',
                            default => $doc->name ?? $doc->type ?? 'Dokumen'
                        };
                    @endphp
                    <div class="flex items-center justify-between p-3 {{ $isWajib ? 'bg-purple-50' : 'bg-gray-50' }} rounded-lg">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" 
                                   name="kelengkapan[{{ $doc->id }}]" 
                                   value="1" 
                                   class="w-5 h-5 text-purple-600">
                            <span class="font-medium" style="font-family: 'Instrument Sans', sans-serif;">
                                {{ $label }}
                                @if($isWajib)
                                    <span class="text-xs text-red-500 ml-1">(Wajib)</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-green-600" style="font-family: 'Instrument Sans', sans-serif;">Tersedia</span>
                            <a href="{{ route('sekretariat.verifikasi.download', $doc->id) }}" class="text-purple-600 text-sm hover:underline" style="font-family: 'Instrument Sans', sans-serif;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500" style="font-family: 'Instrument Sans', sans-serif;">Belum ada dokumen yang diunggah.</p>
                @endforelse
            </div>
            <p class="text-sm text-gray-500 mt-3 italic" style="font-family: 'Instrument Sans', sans-serif;">* Centang semua dokumen yang diberi label (Wajib) sebelum melanjutkan verifikasi.</p>
            @error('kelengkapan')
                <p class="text-red-500 text-sm mt-2" style="font-family: 'Instrument Sans', sans-serif;">{{ $message }}</p>
            @enderror

            <div class="mt-4">
                <label class="block font-medium mb-1" style="font-family: 'Instrument Sans', sans-serif;">
                    Catatan Kekurangan Dokumen
                    <span class="text-xs text-red-500">(wajib diisi jika memilih "Dokumen Tidak Lengkap")</span>
                </label>
                <textarea
                    name="catatan_kelengkapan"
                    rows="3"
                    class="w-full border rounded-lg p-2"
                    placeholder="Jelaskan dokumen apa saja yang kurang/perlu dilengkapi oleh Peneliti..."
                    style="font-family: 'Instrument Sans', sans-serif;"
                >{{ old('catatan_kelengkapan') }}</textarea>
                <p class="text-sm text-gray-500 mt-1 italic" style="font-family: 'Instrument Sans', sans-serif;">
                    Catatan ini akan ditampilkan kepada Peneliti agar tahu apa yang perlu diperbaiki.
                </p>
                @error('catatan_kelengkapan')
                    <p class="text-red-500 text-sm mt-2" style="font-family: 'Instrument Sans', sans-serif;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Jenis Review -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
            <h2 class="text-xl font-semibold mb-4" style="font-family: 'Instrument Sans', sans-serif;">Jenis Review</h2>

            <select id="review_type" name="review_type" class="w-full border rounded-lg p-2 focus:ring-purple-500" style="font-family: 'Instrument Sans', sans-serif;">
                <option value="">Pilih Jenis Review</option>
                <option value="exempted">Exempted (Auto Approve)</option>
                <option value="expedited">Expedited Review (min. 3 reviewer)</option>
                <option value="full_board">Full Board Review (min. 5 reviewer)</option>
            </select>
        </div>

        <!-- Alasan Exempted -->
        <div id="exempted_reason_container" class="bg-white rounded-xl shadow-sm p-5 mb-6" style="display: none;">
            <h2 class="text-xl font-semibold mb-4" style="font-family: 'Instrument Sans', sans-serif;">Alasan Exempted / Dasar Pertimbangan</h2>

            <textarea 
                name="exempted_reason" 
                rows="3" 
                class="w-full border rounded-lg p-2" 
                placeholder="Jelaskan alasan penelitian ini masuk kategori exempted..."
                style="font-family: 'Instrument Sans', sans-serif;"
            ></textarea>

            <p class="text-sm text-gray-500 mt-2 italic" style="font-family: 'Instrument Sans', sans-serif;">
                Jika memilih Exempted, proposal tidak dikirim ke reviewer dan akan dikembalikan ke admin untuk keputusan lanjutan.
            </p>
        </div>

        <!-- Pilih Reviewer -->
        <div id="reviewer_container" class="bg-white rounded-xl shadow-sm p-5 mb-6" style="display: none;">
            <h2 id="reviewer_title" class="text-xl font-semibold mb-4" style="font-family: 'Instrument Sans', sans-serif;">Pilih Reviewer</h2>

            <div class="mb-4 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm" style="font-family: 'Instrument Sans', sans-serif;">
                <strong>Catatan:</strong> Reviewer tidak boleh dipilih double. Setiap pilihan reviewer harus berbeda.
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1" style="font-family: 'Instrument Sans', sans-serif;">Deadline Review</label>
                <input 
                    type="date" 
                    id="review_deadline" 
                    name="review_deadline" 
                    value="{{ old('review_deadline') }}"
                    min="{{ now()->toDateString() }}"
                    class="w-full border rounded-lg p-2"
                    style="font-family: 'Instrument Sans', sans-serif;"
                >
                <p class="text-sm text-gray-500 mt-1 italic" style="font-family: 'Instrument Sans', sans-serif;">
                    Deadline ini akan berlaku untuk semua reviewer yang dipilih.
                </p>

                @error('review_deadline')
                    <p class="text-red-500 text-sm mt-2" style="font-family: 'Instrument Sans', sans-serif;">{{ $message }}</p>
                @enderror
            </div>

            <div id="reviewer_fields" class="space-y-3"></div>

            <p id="reviewer_info" class="text-sm text-gray-500 mt-2 italic" style="font-family: 'Instrument Sans', sans-serif;"></p>

            @error('reviewer_ids')
                <p class="text-red-500 text-sm mt-2" style="font-family: 'Instrument Sans', sans-serif;">{{ $message }}</p>
            @enderror

            @error('reviewer_ids.*')
                <p class="text-red-500 text-sm mt-2" style="font-family: 'Instrument Sans', sans-serif;">Reviewer tidak boleh sama/double.</p>
            @enderror

            <div class="mt-4">
                <label class="block font-medium mb-1" style="font-family: 'Instrument Sans', sans-serif;">
                    Catatan untuk Reviewer
                    <span class="text-xs text-gray-400">(opsional)</span>
                </label>
                <textarea
                    name="catatan_reviewer"
                    rows="3"
                    class="w-full border rounded-lg p-2"
                    placeholder="Catatan/instruksi tambahan untuk reviewer terkait penugasan ini..."
                    style="font-family: 'Instrument Sans', sans-serif;"
                >{{ old('catatan_reviewer') }}</textarea>
                @error('catatan_reviewer')
                    <p class="text-red-500 text-sm mt-2" style="font-family: 'Instrument Sans', sans-serif;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex flex-wrap gap-4">
            <button type="submit" name="action" value="tidak_lengkap" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow flex items-center gap-2" style="font-family: 'Instrument Sans', sans-serif;">
                <i class="fas fa-times"></i> Dokumen Tidak Lengkap
            </button>
            <button type="submit" name="action" value="lengkap" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg shadow flex items-center gap-2" style="font-family: 'Instrument Sans', sans-serif;">
                <i class="fas fa-check"></i> Dokumen Lengkap - Lanjutkan
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const reviewType = document.getElementById('review_type');
    const exemptedContainer = document.getElementById('exempted_reason_container');
    const reviewerContainer = document.getElementById('reviewer_container');
    const reviewerTitle = document.getElementById('reviewer_title');
    const reviewerFields = document.getElementById('reviewer_fields');
    const reviewerInfo = document.getElementById('reviewer_info');

    function renderReviewers(total) {
        reviewerFields.innerHTML = '';

        for (let i = 1; i <= total; i++) {
            reviewerFields.innerHTML += `
                <div>
                    <label class="block font-medium mb-1" style="font-family: 'Instrument Sans', sans-serif;">Reviewer ${i}</label>
                    <select name="reviewer_ids[]" class="reviewer-select w-full border rounded-lg p-2" style="font-family: 'Instrument Sans', sans-serif;">
                        <option value="">Pilih Reviewer ${i}</option>
                        @foreach($reviewers ?? [] as $reviewer)
                            <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                        @endforeach
                    </select>
                </div>
            `;
        }

        const selects = document.querySelectorAll('.reviewer-select');

        function updateReviewerOptions() {
            const selectedValues = Array.from(selects)
                .map(select => select.value)
                .filter(value => value !== '');

            selects.forEach(select => {
                const currentValue = select.value;

                Array.from(select.options).forEach(option => {
                    if (option.value === '') return;

                    option.disabled = selectedValues.includes(option.value) && option.value !== currentValue;
                });
            });
        }

        selects.forEach(select => {
            select.addEventListener('change', updateReviewerOptions);
        });
    }

    reviewType.addEventListener('change', function () {
        exemptedContainer.style.display = 'none';
        reviewerContainer.style.display = 'none';
        reviewerFields.innerHTML = '';
        reviewerInfo.innerText = '';

        if (this.value === 'exempted') {
            exemptedContainer.style.display = 'block';
        }

        if (this.value === 'expedited') {
            reviewerContainer.style.display = 'block';
            reviewerTitle.innerText = 'Pilih 3 Reviewer';
            reviewerInfo.innerText = 'Expedited Review wajib memilih 3 reviewer.';
            renderReviewers(3);
        }

        if (this.value === 'full_board') {
            reviewerContainer.style.display = 'block';
            reviewerTitle.innerText = 'Pilih 5 Reviewer';
            reviewerInfo.innerText = 'Full Board Review wajib memilih 5 reviewer.';
            renderReviewers(5);
        }
    });

    // Validasi catatan kekurangan dokumen jika memilih "Dokumen Tidak Lengkap"
    const formVerifikasi = document.querySelector('form');
    const catatanKelengkapan = document.querySelector('[name="catatan_kelengkapan"]');

    formVerifikasi.addEventListener('submit', function (e) {
        const submitter = e.submitter;

        if (submitter && submitter.value === 'tidak_lengkap') {
            if (!catatanKelengkapan.value.trim()) {
                e.preventDefault();
                catatanKelengkapan.focus();
                catatanKelengkapan.classList.add('border-red-500');
                alert('Mohon isi catatan kekurangan dokumen sebelum menyatakan dokumen tidak lengkap.');
            }
        }
    });
});
</script>
@endpush