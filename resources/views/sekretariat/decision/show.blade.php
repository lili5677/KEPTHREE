@extends('layouts.sekretariat')

@section('title', 'Detail Secretary Decision – Sistem KEP')

@section('content')
@php
    $selectedDecision = old('keputusan', $existingDecision->keputusan ?? '');
    $catatanDecision = old('catatan', $existingDecision->catatan ?? '');
@endphp

<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Secretary Decision - PRO-{{ $protocol->id }}
        </h1>
        <p class="text-gray-500 text-sm">
            {{ $existingDecision ? 'Edit keputusan sekretariat' : 'Tetapkan keputusan akhir' }}
        </p>
    </div>

    <a href="{{ route('sekretariat.decision.index') }}"
       class="text-purple-600 hover:underline flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
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

<!-- Info Proposal -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p><strong>Judul:</strong> {{ $protocol->title }}</p>
            <p><strong>Peneliti:</strong> {{ $protocol->user->name ?? '-' }}</p>
            <p>
                <strong>Review Type:</strong>
                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                    {{ strtoupper($reviewType ?? 'unknown') }}
                </span>
            </p>
        </div>

        <div>
            <p>
                <strong>Status Protocol:</strong>
                <span class="text-yellow-600">{{ $protocol->status }}</span>
            </p>

            <p>
                <strong>Progress Review:</strong>
                {{ $completedReviews }}/{{ $totalReviewers }} reviewer selesai
            </p>

            <p>
                <strong>Status Keputusan:</strong>
                @if($existingDecision)
                    <span class="text-blue-600 font-semibold">Sudah Diputuskan</span>
                @else
                    <span class="text-red-600 font-semibold">Belum Diputuskan</span>
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Rangkuman Feedback Reviewer -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <h2 class="text-xl font-semibold mb-4">Rangkuman Reviewer Feedback</h2>

    @if($reviews->isEmpty())
        <p class="text-gray-500">Belum ada feedback dari reviewer.</p>
    @else
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <p class="font-medium">
                                {{ $review->reviewer->name ?? 'Reviewer' }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Submitted:
                                {{ $review->reviewed_at ? \Carbon\Carbon::parse($review->reviewed_at)->format('d M Y H:i') : '-' }}
                            </p>
                        </div>

                        <span class="text-xs px-2 py-1 rounded-full
                            @if($review->keputusan == 'approved') bg-green-100 text-green-800
                            @elseif($review->keputusan == 'approved_with_recommendation') bg-blue-100 text-blue-800
                            @elseif($review->keputusan == 'minor_revision') bg-yellow-100 text-yellow-800
                            @elseif($review->keputusan == 'major_revision') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ strtoupper(str_replace('_', ' ', $review->keputusan ?? 'pending')) }}
                        </span>
                    </div>

                    @if($review->catatan)
                        <p class="text-gray-600 text-sm mt-2">
                            {{ $review->catatan }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-4 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
        <strong>Status Review:</strong>

        @if($isComplete)
            <span class="text-green-600">
                ✅ Semua reviewer telah selesai. Silakan tetapkan keputusan.
            </span>
        @else
            <span class="text-red-600">
                ⏳ Menunggu {{ max($totalReviewers - $completedReviews, 0) }} reviewer lagi
                ({{ $completedReviews }}/{{ $totalReviewers }})
            </span>
        @endif
    </div>
</div>

<!-- Keputusan Sekretariat Sebelumnya -->
@if($existingDecision)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
        <h2 class="text-lg font-semibold text-blue-800 mb-2">
            Keputusan Saat Ini
        </h2>

        <p>
            <strong>Keputusan:</strong>
            {{ strtoupper(str_replace('_', ' ', $existingDecision->keputusan)) }}
        </p>

        <p class="mt-1">
            <strong>Catatan:</strong>
            {{ $existingDecision->catatan ?: '-' }}
        </p>
    </div>
@endif

<!-- Form Keputusan -->
@if($isComplete || $existingDecision)
<div class="bg-white rounded-xl shadow-sm p-5">
    <h2 class="text-xl font-semibold mb-4">
        {{ $existingDecision ? 'Edit Keputusan' : 'Tetapkan Keputusan' }}
    </h2>

    <form method="POST" action="{{ route('sekretariat.decision.store', $protocol->id) }}">
        @csrf

        <div class="mb-4">
            <label class="block font-medium mb-2">
                Keputusan <span class="text-red-500">*</span>
            </label>

            <div class="space-y-2">
                <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="keputusan" value="approved"
                        {{ $selectedDecision == 'approved' ? 'checked' : '' }}
                        required>
                    <span class="font-medium">Approved</span>
                    <span class="text-sm text-gray-500">- Disetujui final</span>
                </label>

                <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="keputusan" value="approved_with_recommendation"
                        {{ $selectedDecision == 'approved_with_recommendation' ? 'checked' : '' }}
                        required>
                    <span class="font-medium text-yellow-600">Approved with Recommendation</span>
                    <span class="text-sm text-gray-500">- Peneliti revisi, lalu diteruskan ke reviewer</span>
                </label>

                <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="keputusan" value="rejected"
                        {{ $selectedDecision == 'rejected' ? 'checked' : '' }}
                        required>
                    <span class="font-medium text-red-600">Disapproved</span>
                    <span class="text-sm text-gray-500">- Proposal ditolak</span>
                </label>

                @if($reviewType == 'full_board')
                    <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="keputusan" value="rejected"
                            {{ $selectedDecision == 'rejected' ? 'checked' : '' }}
                            required>
                        <span class="font-medium text-red-600">Rejected</span>
                        <span class="text-sm text-gray-500">- Ditolak</span>
                    </label>
                @endif
            </div>

            @error('keputusan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium mb-1">
                Catatan
            </label>

            <textarea
                name="catatan"
                rows="3"
                class="w-full border rounded-lg p-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Tambahkan catatan untuk peneliti..."
            >{{ $catatanDecision }}</textarea>

            @error('catatan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg shadow flex items-center gap-2">
            <i class="fas fa-check"></i>
            {{ $existingDecision ? 'Update Keputusan' : 'Simpan Keputusan' }}
        </button>
    </form>
</div>
@else
<div class="bg-white rounded-xl shadow-sm p-5 text-center">
    <i class="fas fa-clock text-4xl text-yellow-500 mb-2"></i>
    <p class="text-gray-600">
        Menunggu semua reviewer menyelesaikan feedback sebelum keputusan dapat ditetapkan.
    </p>
    <p class="text-sm text-gray-500 mt-1">
        Progress: {{ $completedReviews }}/{{ $totalReviewers }} reviewer selesai
    </p>
</div>
@endif

@endsection