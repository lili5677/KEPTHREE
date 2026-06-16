@extends('layouts.sekretariat')

@section('title', 'Detail Secretary Decision – Sistem KEP')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Secretary Decision - PRO-{{ $protocol->id }}</h1>
        <a href="{{ route('sekretariat.decision.index') }}" class="text-purple-600 hover:underline flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Info Proposal -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong>Judul:</strong> {{ $protocol->title }}</p>
                <p><strong>Peneliti:</strong> {{ $protocol->user->name ?? '-' }}</p>
                <p><strong>Review Type:</strong> 
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                        {{ strtoupper($protocol->review_type ?? 'unknown') }}
                    </span>
                </p>
            </div>
            <div>
                <p><strong>Status:</strong> <span class="text-yellow-600">{{ $protocol->status }}</span></p>
                <p><strong>Progress Review:</strong> {{ $completedReviews }}/{{ $totalReviewers }} reviewer selesai</p>
                <p><strong>Minimal Reviewer:</strong> {{ $minReviewer }} reviewer</p>
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
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">{{ $review->reviewer->name ?? 'Reviewer' }}</p>
                                <p class="text-sm text-gray-500">Submitted: {{ $review->submitted_at ? \Carbon\Carbon::parse($review->submitted_at)->format('d M Y H:i') : '-' }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full 
                                @if($review->keputusan == 'approved') bg-green-100 text-green-800
                                @elseif($review->keputusan == 'approved_with_recommendation') bg-blue-100 text-blue-800
                                @elseif($review->keputusan == 'minor_revision') bg-yellow-100 text-yellow-800
                                @elseif($review->keputusan == 'major_revision') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ strtoupper(str_replace('_', ' ', $review->keputusan ?? 'pending')) }}
                            </span>
                        </div>
                        @if($review->catatan)
                            <p class="text-gray-600 text-sm mt-2">{{ $review->catatan }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
            <strong>Status:</strong> 
            @if($isComplete)
                <span class="text-green-600">✅ Semua reviewer telah selesai. Silakan tetapkan keputusan.</span>
            @else
                <span class="text-red-600">⏳ Menunggu {{ $totalReviewers - $completedReviews }} reviewer lagi ({{ $completedReviews }}/{{ $totalReviewers }})</span>
            @endif
        </div>
    </div>

    <!-- Form Keputusan -->
    @if($isComplete)
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="text-xl font-semibold mb-4">Tetapkan Keputusan</h2>
        
        <form method="POST" action="{{ route('sekretariat.decision.store', $protocol->id) }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium mb-2">Keputusan <span class="text-red-500">*</span></label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="keputusan" value="approved" required>
                        <span class="font-medium">Approved</span>
                        <span class="text-sm text-gray-500">- Proposal disetujui</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="keputusan" value="approved_with_recommendation" required>
                        <span class="font-medium">Approved with Recommendation</span>
                        <span class="text-sm text-gray-500">- Disetujui dengan rekomendasi</span>
                    </label>
                    @if($protocol->review_type == 'full_board')
                    <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="keputusan" value="disapproved" required>
                        <span class="font-medium text-red-600">Disapproved</span>
                        <span class="text-sm text-gray-500">- Ditolak (khusus Full Board)</span>
                    </label>
                    @endif
                </div>
                @error('keputusan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1">Catatan (Opsional)</label>
                <textarea 
                    name="catatan" 
                    rows="3" 
                    class="w-full border rounded-lg p-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Tambahkan catatan untuk peneliti..."
                >{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg shadow flex items-center gap-2">
                <i class="fas fa-check"></i> Simpan Keputusan
            </button>
        </form>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
        <i class="fas fa-clock text-4xl text-yellow-500 mb-2"></i>
        <p class="text-gray-600">Menunggu semua reviewer menyelesaikan feedback sebelum keputusan dapat ditetapkan.</p>
        <p class="text-sm text-gray-500 mt-1">Progress: {{ $completedReviews }}/{{ $totalReviewers }} reviewer selesai</p>
    </div>
    @endif
@endsection