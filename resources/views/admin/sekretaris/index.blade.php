@extends('layouts.admin')

@section('title', 'Assign Sekretaris')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Assign Sekretaris</h1>
        <p class="text-sm text-slate-500 mt-0.5">Lihat detail proposal dan tetapkan sekretaris penanggungjawab.</p>
    </div>

    {{-- Layout 2 kolom --}}
    <div class="flex gap-5 items-start">

        {{-- ============================================================
             KOLOM KIRI
        ============================================================ --}}
        <div class="w-72 flex-shrink-0 space-y-4">

            {{-- Daftar Proposal Baru --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-800">Proposal Baru</h2>
                    <p class="text-xs text-slate-400 mt-0.5">2 proposal menunggu</p>
                </div>

                <div class="divide-y divide-slate-50">

                    {{-- Item 1 — aktif --}}
                    <div id="item-proposal1"
                         onclick="showDetail('proposal1')"
                         class="px-4 py-3 cursor-pointer bg-indigo-50 border-l-[3px] border-indigo-500 transition">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[11px] font-semibold text-slate-500">PRO-156</span>
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full">New</span>
                        </div>
                        <p class="text-xs font-semibold text-slate-700 leading-snug">
                            Penelitian Efektivitas Obat Antiviral Baru
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Dr. Ahmad Santoso</p>
                        <p class="text-[11px] text-slate-400">Diajukan: 2026-04-25</p>
                    </div>

                    {{-- Item 2 --}}
                    <div id="item-proposal2"
                         onclick="showDetail('proposal2')"
                         class="px-4 py-3 cursor-pointer hover:bg-slate-50 border-l-[3px] border-transparent transition">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[11px] font-semibold text-slate-500">PRO-155</span>
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full">New</span>
                        </div>
                        <p class="text-xs font-semibold text-slate-700 leading-snug">
                            Studi Kualitas Tidur Mahasiswa Kedokteran
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Dr. Maria Indah</p>
                        <p class="text-[11px] text-slate-400">Diajukan: 2026-04-25</p>
                    </div>

                </div>
            </div>

            {{-- Beban Kerja Sekretaris --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-800">Beban Kerja Sekretaris</h2>
                </div>
                <div class="px-4 py-4 space-y-3.5">
                    <div>
                        <div class="flex justify-between mb-1.5">
                            <span class="text-xs font-medium text-slate-700">Dr. Sarah Wijaya</span>
                            <span class="text-xs font-semibold text-slate-500">5</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-amber-400" style="width:50%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1.5">
                            <span class="text-xs font-medium text-slate-700">Dr. Rina Sari</span>
                            <span class="text-xs font-semibold text-slate-500">8</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-red-400" style="width:80%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1.5">
                            <span class="text-xs font-medium text-slate-700">Dr. Doni Hermawan</span>
                            <span class="text-xs font-semibold text-slate-500">3</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-indigo-500" style="width:30%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- end kolom kiri --}}


        {{-- ============================================================
             KOLOM KANAN
        ============================================================ --}}
        <div class="flex-1 min-w-0">

            {{-- ===== DETAIL PROPOSAL 1 (default tampil) ===== --}}
            <div id="detail-proposal1" class="bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-400">PRO-156</span>
                    <span class="text-[11px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">New Proposal</span>
                </div>
                <div class="px-5 py-5 space-y-6">

                    <h2 class="text-lg font-bold text-slate-800">Penelitian Efektivitas Obat Antiviral Baru</h2>

                    <div>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Informasi Peneliti</h3>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                            <div>
                                <p class="text-[11px] text-slate-400">Nama Peneliti</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">Dr. Ahmad Santoso</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400">Institusi</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">Universitas Indonesia</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400">Tanggal Pengajuan</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">2026-04-25</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400">Program Studi</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">Ilmu Kedokteran</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ringkasan Penelitian</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Penelitian ini bertujuan untuk mengevaluasi efektivitas obat antiviral baru
                            terhadap virus influenza dengan metode randomized controlled trial.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Dokumen yang Diunggah</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['Formulir Pengajuan','Ringkasan Protokol','Surat Pengantar','Proposal Lengkap'] as $d)
                            <div class="flex items-center gap-2.5 bg-green-50 border border-green-100 rounded-xl px-3 py-2.5">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs font-medium text-slate-700">{{ $d }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4">Assign Sekretaris</h3>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pilih Sekretaris <span class="text-red-500">*</span>
                        </label>
                        <select class="sekretaris-select w-full border border-slate-200 rounded-xl px-3.5 py-2.5
                                       text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       focus:border-transparent mb-2 bg-white">
                            <option value="">-- Pilih Sekretaris --</option>
                            <option>Dr. Sarah Wijaya (Workload: 5 proposal)</option>
                            <option>Dr. Rina Sari (Workload: 8 proposal)</option>
                            <option>Dr. Doni Hermawan (Workload: 3 proposal)</option>
                        </select>
                        <p class="text-[11px] text-slate-400 mb-4">Sekretaris akan menerima notifikasi setelah ditugaskan.</p>
                        <button type="button" onclick="handleAssign(this)"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 rounded-xl transition shadow-sm">
                            Assign Sekretaris
                        </button>
                    </div>

                </div>
            </div>

            {{-- ===== DETAIL PROPOSAL 2 (hidden) ===== --}}
            <div id="detail-proposal2" class="hidden bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-400">PRO-155</span>
                    <span class="text-[11px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">New Proposal</span>
                </div>
                <div class="px-5 py-5 space-y-6">

                    <h2 class="text-lg font-bold text-slate-800">Studi Kualitas Tidur Mahasiswa Kedokteran</h2>

                    <div>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Informasi Peneliti</h3>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                            <div>
                                <p class="text-[11px] text-slate-400">Nama Peneliti</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">Dr. Maria Indah</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400">Institusi</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">Universitas Gadjah Mada</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400">Tanggal Pengajuan</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">2026-04-25</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400">Program Studi</p>
                                <p class="text-sm font-semibold text-slate-700 mt-0.5">Kedokteran Umum</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ringkasan Penelitian</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Penelitian ini menganalisis pola dan kualitas tidur mahasiswa kedokteran
                            selama masa klinik menggunakan instrumen Pittsburgh Sleep Quality Index (PSQI).
                        </p>
                    </div>

                    <div>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Dokumen yang Diunggah</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['Formulir Pengajuan','Ringkasan Protokol','Surat Pengantar'] as $d)
                            <div class="flex items-center gap-2.5 bg-green-50 border border-green-100 rounded-xl px-3 py-2.5">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs font-medium text-slate-700">{{ $d }}</span>
                            </div>
                            @endforeach
                            {{-- Proposal Lengkap belum ada --}}
                            <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                                <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-medium text-slate-400">Proposal Lengkap</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4">Assign Sekretaris</h3>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pilih Sekretaris <span class="text-red-500">*</span>
                        </label>
                        <select class="sekretaris-select w-full border border-slate-200 rounded-xl px-3.5 py-2.5
                                       text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       focus:border-transparent mb-2 bg-white">
                            <option value="">-- Pilih Sekretaris --</option>
                            <option>Dr. Sarah Wijaya (Workload: 5 proposal)</option>
                            <option>Dr. Rina Sari (Workload: 8 proposal)</option>
                            <option>Dr. Doni Hermawan (Workload: 3 proposal)</option>
                        </select>
                        <p class="text-[11px] text-slate-400 mb-4">Sekretaris akan menerima notifikasi setelah ditugaskan.</p>
                        <button type="button" onclick="handleAssign(this)"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 rounded-xl transition shadow-sm">
                            Assign Sekretaris
                        </button>
                    </div>

                </div>
            </div>

        </div>
        {{-- end kolom kanan --}}

    </div>

    {{-- Toast --}}
    <div id="toast"
         class="fixed bottom-6 right-6 bg-green-600 text-white text-sm font-semibold
                px-5 py-3 rounded-xl shadow-lg flex items-center gap-2.5 z-50
                opacity-0 translate-y-4 transition-all duration-300 pointer-events-none">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span id="toast-msg">Sekretaris berhasil ditugaskan.</span>
    </div>

@endsection

@push('scripts')
<script>
    // Tampilkan detail proposal di kolom kanan
    function showDetail(id) {
        // Sembunyikan semua panel detail
        document.querySelectorAll('[id^="detail-"]').forEach(el => el.classList.add('hidden'));
        // Tampilkan panel yang dipilih
        document.getElementById('detail-' + id).classList.remove('hidden');

        // Reset semua item kiri
        document.querySelectorAll('[id^="item-"]').forEach(el => {
            el.classList.remove('bg-indigo-50', 'border-indigo-500');
            el.classList.add('border-transparent');
        });
        // Aktifkan item yang diklik
        document.getElementById('item-' + id).classList.add('bg-indigo-50', 'border-indigo-500');
        document.getElementById('item-' + id).classList.remove('border-transparent');
    }

    // Handle klik tombol Assign (validasi + toast)
    function handleAssign(btn) {
        const card    = btn.closest('[id^="detail-"]');
        const select  = card.querySelector('.sekretaris-select');

        if (!select.value) {
            // Highlight merah jika belum pilih
            select.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            setTimeout(() => select.classList.remove('border-red-400', 'ring-2', 'ring-red-100'), 2000);
            return;
        }

        // Ambil nama sekretaris (sebelum kurung)
        const nama = select.value.split(' (')[0];

        // Tampilkan toast
        document.getElementById('toast-msg').textContent = nama + ' berhasil ditugaskan.';
        const toast = document.getElementById('toast');
        toast.classList.remove('opacity-0', 'translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-4');
            toast.classList.remove('opacity-100', 'translate-y-0');
        }, 3000);
    }
</script>
@endpush