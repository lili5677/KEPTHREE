@extends('layouts.sekretariat')

@section('title', 'Secretary Decision – Sistem KEP')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Secretary Decision</h1>
        <p class="text-gray-500">Tetapkan keputusan akhir untuk proposal yang telah selesai direview</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($protocols as $p)
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <div class="font-bold text-purple-700 text-lg">PRO-{{ $p->id }}</div>
                    <h3 class="font-semibold text-gray-800 mt-1">{{ $p->title }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Peneliti: {{ $p->user->name ?? '-' }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                            {{ strtoupper($p->review_type ?? 'unknown') }}
                        </span>
                        <span class="text-xs px-2 py-1 rounded-full {{ $p->is_complete ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $p->review_progress }} reviewer selesai
                        </span>
                    </div>
                </div>
                <a href="{{ route('sekretariat.decision.show', $p->id) }}" 
                   class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition flex items-center gap-1">
                    <i class="fas fa-gavel"></i> Putuskan
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-12 bg-white rounded-xl shadow-sm">
            <i class="fas fa-check-circle text-4xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Tidak ada proposal yang menunggu keputusan.</p>
        </div>
        @endforelse
    </div>
@endsection