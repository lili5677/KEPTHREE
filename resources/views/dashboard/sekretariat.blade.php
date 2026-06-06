<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard Sekretaris – Sistem KEP</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
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
                <a href="{{ route('sekretariat.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-indigo-600 bg-indigo-50 font-medium">
                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                </a>
                <a href="{{ route('sekretariat.verifikasi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
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
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Sekretaris</h1>
            <p class="text-gray-500">Kelola verifikasi dan proses review proposal</p>
        </div>

        <!-- Kartu Statistik (4 kartu) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500">
                <div class="text-gray-500 text-sm">Menunggu Verifikasi</div>
                <div class="text-3xl font-bold">{{ $menungguVerifikasi ?? 0 }}</div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500">
                <div class="text-gray-500 text-sm">Sedang On Review</div>
                <div class="text-3xl font-bold">{{ $sedangOnReview ?? 0 }}</div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500">
                <div class="text-gray-500 text-sm">Perlu Keputusan</div>
                <div class="text-3xl font-bold">{{ $perluKeputusan ?? 0 }}</div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500">
                <div class="text-gray-500 text-sm">Selesai Bulan Ini</div>
                <div class="text-3xl font-bold">{{ $selesaiBulanIni ?? 0 }}</div>
            </div>
        </div>

        <!-- Dua kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Proposal Prioritas -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 text-lg mb-4">Proposal Prioritas</h2>
                <div class="space-y-4">
                    @forelse($prioritas ?? [] as $proposal)
                        <div class="border rounded-lg p-4 hover:shadow transition">
                            <div class="flex flex-wrap justify-between items-start gap-2">
                                <div>
                                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">{{ $proposal->action_label }}</span>
                                    <h3 class="font-medium mt-2">{{ $proposal->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        ID: {{ $proposal->protocol_number ?? 'PRO-'.$proposal->id }}
                                        @if($proposal->deadline_display) • Deadline: {{ $proposal->deadline_display }} @endif
                                    </p>
                                </div>
                                @if($proposal->action_label == 'Verifikasi Dokumen')
                                    <a href="{{ route('sekretariat.verifikasi.show', $proposal->id) }}" class="text-indigo-600 text-sm font-medium flex items-center">
                                        Proses Sekarang <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @else
                                    <a href="#" class="text-indigo-600 text-sm font-medium flex items-center">
                                        Proses Sekarang <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Tidak ada proposal prioritas.</p>
                    @endforelse
                </div>
            </div>

            <!-- Review Progress -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 text-lg mb-4">Review Progress</h2>
                <div class="space-y-4">
                    @forelse($reviewProgress ?? [] as $item)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start flex-wrap gap-2">
                                <div>
                                    <div class="font-medium">{{ $item->judul }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $item->id }}</div>
                                    <div class="mt-1 text-sm text-gray-600">{{ $item->status_text }}</div>
                                </div>
                                <span class="text-sm font-semibold bg-gray-100 px-2 py-1 rounded-full">{{ $item->progress }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                                @php
                                    [$selesai, $total] = explode('/', $item->progress);
                                    $persen = $total > 0 ? ($selesai / $total) * 100 : 0;
                                @endphp
                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Tidak ada proposal dalam review.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>