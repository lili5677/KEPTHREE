@extends('layouts.sekretariat')

@section('title', 'Dashboard Sekretariat')

@section('content')
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
@endsection