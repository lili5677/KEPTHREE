<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Verifikasi Dokumen – Sistem KEP</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

<div class="flex flex-col md:flex-row min-h-screen">
    <!-- ======================= SIDEBAR KIRI (sama dengan dashboard) ======================= -->
    <aside class="w-full md:w-64 bg-white shadow-md flex flex-col justify-between">
        <div>
            <!-- Header sidebar dengan logo dari public -->
            <div class="p-5 border-b flex items-center gap-3">
                <img src="{{ asset('kepicon.ico') }}" alt="Logo KEP" class="w-8 h-8">
                <span class="text-xl font-bold text-indigo-700">Sistem KEP</span>
            </div>
            <nav class="mt-6 px-3 space-y-1">
                <!-- Dashboard: aktif hanya jika di route dashboard -->
                <a href="{{ route('sekretariat.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('sekretariat.dashboard') ? 'text-indigo-600 bg-indigo-50 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                </a>
                <!-- Verifikasi Dokumen: aktif jika di halaman verifikasi mana pun -->
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

        <!-- Profil dan Logout di kiri bawah -->
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
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Verifikasi Dokumen</h1>
            <p class="text-gray-500">Periksa kelengkapan dokumen pengajuan</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">{{ session('error') }}</div>
        @endif

        <!-- Daftar proposal dalam grid kartu (2 kolom responsif) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($protocols as $p)
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-bold text-indigo-700 text-lg">PRO-{{ $p->id }}</div>
                        <h3 class="font-semibold text-gray-800 mt-1">{{ $p->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Peneliti: {{ $p->user->name ?? '-' }}</p>
                    </div>
                    <a href="{{ route('sekretariat.verifikasi.show', $p->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition flex items-center gap-1">
                        Pilih <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-2 text-center py-12 bg-white rounded-xl shadow-sm">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Belum ada proposal yang perlu diverifikasi.</p>
            </div>
            @endforelse
        </div>
    </main>
</div>

</body>
</html>