@extends('layouts.sekretariat')

@section('title', 'Verifikasi Dokumen – Sistem KEP')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800" style="font-family: 'Instrument Sans', sans-serif;">Verifikasi Dokumen</h1>
        <p class="text-gray-500" style="font-family: 'Instrument Sans', sans-serif;">Periksa kelengkapan dokumen pengajuan</p>
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
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 {{ $p->status === 'revision_required' ? 'border-orange-500' : 'border-purple-500' }} hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="font-bold text-purple-700 text-lg" style="font-family: 'Instrument Sans', sans-serif;">PRO-{{ $p->id }}</div>
                        @if($p->status === 'revision_required')
                            <span class="text-xs px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">
                                Revisi Diunggah Ulang
                            </span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800 mt-1" style="font-family: 'Instrument Sans', sans-serif;">{{ $p->title }}</h3>
                    <p class="text-sm text-gray-500 mt-1" style="font-family: 'Instrument Sans', sans-serif;">Peneliti: {{ $p->user->name ?? '-' }}</p>
                    @if($p->status === 'revision_required' && $p->latestRevision)
                        <p class="text-xs text-orange-600 mt-1">
                            <i class="fas fa-paperclip"></i>
                            Diunggah {{ $p->latestRevision->submitted_at?->translatedFormat('d M Y, H:i') }}
                        </p>
                    @endif
                </div>
                <a href="{{ route('sekretariat.verifikasi.show', $p->id) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition flex items-center gap-1" style="font-family: 'Instrument Sans', sans-serif;">
                    Pilih <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-12 bg-white rounded-xl shadow-sm">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
            <p class="text-gray-500" style="font-family: 'Instrument Sans', sans-serif;">Belum ada proposal yang perlu diverifikasi.</p>
        </div>
        @endforelse
    </div>
@endsection