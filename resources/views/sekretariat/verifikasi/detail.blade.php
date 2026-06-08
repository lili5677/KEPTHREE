<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Detail Verifikasi – Sistem KEP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

<div class="flex flex-col md:flex-row min-h-screen">
    <!-- ======================= SIDEBAR KIRI ======================= -->
    <aside class="w-full md:w-64 bg-white shadow-md flex flex-col justify-between">
        <div>
            <div class="p-5 border-b flex items-center gap-3">
                <img src="{{ asset('kepicon.ico') }}" alt="Logo KEP" class="w-8 h-8">
                <span class="text-xl font-bold text-indigo-700">Sistem KEP</span>
            </div>
            <nav class="mt-6 px-3 space-y-1">
                <a href="{{ route('sekretariat.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('sekretariat.dashboard') ? 'text-indigo-600 bg-indigo-50 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                </a>
                <a href="{{ route('sekretariat.verifikasi.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('sekretariat.verifikasi.*') ? 'text-indigo-600 bg-indigo-50 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-file-alt w-5"></i> Verifikasi Dokumen
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-users w-5"></i> Assignment Reviewer
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-gavel w-5"></i> Secretary Decision
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-history w-5"></i> Riwayat Proposal
                </a>
            </nav>
        </div>
        <div class="p-4 border-t mt-6">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Sarah Wijaya') }}&background=4F46E5&color=fff&rounded=true" class="w-10 h-10 rounded-full">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? 'Dr. Sarah Wijaya' }}</div>
                    <div class="text-sm text-gray-500 truncate">{{ auth()->user()->email ?? 'sarah@kep.ac.id' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 transition px-3 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- ======================= KONTEN UTAMA ======================= -->
    <main class="flex-1 p-4 md:p-6 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Detail Verifikasi - PRO-{{ $protocol->id }}</h1>
            <a href="{{ route('sekretariat.verifikasi.index') }}" class="text-indigo-600 hover:underline flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Info Proposal -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
            <p><strong>Judul:</strong> {{ $protocol->title }}</p>
            <p><strong>Peneliti:</strong> {{ $protocol->user->name ?? '-' }} ({{ $protocol->user->email ?? '-' }})</p>
            <p><strong>Status saat ini:</strong> <span class="text-yellow-600">{{ $protocol->status }}</span></p>
        </div>

        <form method="POST" action="{{ route('sekretariat.verifikasi.check', $protocol->id) }}">
            @csrf

            <!-- Kelengkapan Dokumen -->
            <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
                <h2 class="text-xl font-semibold mb-4">Kelengkapan Dokumen</h2>
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
                        <div class="flex items-center justify-between p-3 {{ $isWajib ? 'bg-indigo-50' : 'bg-gray-50' }} rounded-lg">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" 
                                       name="kelengkapan[{{ $doc->id }}]" 
                                       value="1" 
                                       class="w-5 h-5 text-indigo-600">
                                <span class="font-medium">
                                    {{ $label }}
                                    @if($isWajib)
                                        <span class="text-xs text-red-500 ml-1">(Wajib)</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-green-600">Tersedia</span>
                                <a href="{{ route('sekretariat.verifikasi.download', $doc->id) }}" class="text-indigo-600 text-sm hover:underline">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Belum ada dokumen yang diunggah.</p>
                    @endforelse
                </div>
                <p class="text-sm text-gray-500 mt-3 italic">* Centang semua dokumen yang diberi label (Wajib) sebelum melanjutkan verifikasi.</p>
                @error('kelengkapan')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Review -->
            <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
                <h2 class="text-xl font-semibold mb-4">Jenis Review</h2>
                <select name="review_type" class="w-full border rounded-lg p-2 focus:ring-indigo-500">
                    <option value="">Pilih Jenis Review</option>
                    <option value="exempted">Exempted (Auto Approve)</option>
                    <option value="expedited">Expedited Review (min. 3 reviewer)</option>
                    <option value="full_board">Full Board Review (min. 5 reviewer)</option>
                </select>
            </div>

            <!-- Catatan Verifikasi -->
            <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
                <h2 class="text-xl font-semibold mb-4">Catatan Verifikasi</h2>
                <textarea name="catatan" rows="3" class="w-full border rounded-lg p-2" placeholder="Tambahkan catatan atau instruksi khusus..."></textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-wrap gap-4">
                <button type="submit" name="action" value="tidak_lengkap" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow flex items-center gap-2">
                    <i class="fas fa-times"></i> Dokumen Tidak Lengkap
                </button>
                <button type="submit" name="action" value="lengkap" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow flex items-center gap-2">
                    <i class="fas fa-check"></i> Dokumen Lengkap - Lanjutkan
                </button>
            </div>
        </form>
    </main>
</div>

</body>
</html>