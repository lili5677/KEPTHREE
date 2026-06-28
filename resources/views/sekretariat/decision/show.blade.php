@extends('layouts.sekretariat')

@section('title', 'Detail Secretary Decision – Sistem KEP')

@section('content')
@php
    $selectedDecision = old('keputusan', '');
    $catatanDecision = old('catatan', '');

    $orderedDecisions = $decisionHistory->sortByDesc('round')->values();
    $latestDecisionUi = $orderedDecisions->first();

    $isFinalDecisionUi = $latestDecisionUi
        && in_array($latestDecisionUi->keputusan, ['approved', 'rejected']);

    $nextRoundUi = $latestDecisionUi ? $latestDecisionUi->round + 1 : 1;

    $decisionLabels = [
        'approved' => 'Approved',
        'approved_with_recommendation' => 'Approved with Recommendation',
        'rejected' => 'Disapproved',
    ];
@endphp

<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Secretary Decision - PRO-{{ $protocol->id }}
        </h1>
        <p class="text-gray-500 text-sm">
            {{ $latestDecisionUi ? 'Lihat histori dan status keputusan sekretariat' : 'Tetapkan keputusan sekretariat' }}
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


{{-- INFO PROPOSAL --}}
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
            <p>
                <strong>Judul:</strong> {{ $protocol->title }}
            </p>

            <p>
                <strong>Peneliti:</strong> {{ $protocol->user->name ?? '-' }}
            </p>

            <p>
                <strong>Review Type:</strong>
                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                    {{ strtoupper($reviewType ?? 'unknown') }}
                </span>
            </p>
        </div>

        <div class="space-y-2">
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

                @if(!$latestDecisionUi)
                    <span class="text-red-600 font-semibold">
                        Belum Diputuskan
                    </span>

                @elseif($latestDecisionUi->keputusan === 'approved')
                    <span class="text-green-600 font-semibold">
                        Keputusan Final: Approved
                    </span>

                @elseif($latestDecisionUi->keputusan === 'rejected')
                    <span class="text-red-600 font-semibold">
                        Keputusan Final: Disapproved
                    </span>

                @elseif($latestDecisionUi->keputusan === 'approved_with_recommendation' && $menungguRevisiPeneliti)
                    <span class="text-orange-600 font-semibold">
                        Menunggu Revisi Peneliti - Babak {{ $latestDecisionUi->round }}
                    </span>

                @elseif($latestDecisionUi->keputusan === 'approved_with_recommendation' && $isComplete)
                    <span class="text-purple-600 font-semibold">
                        Siap Ditetapkan Keputusan Babak {{ $latestDecisionUi->round + 1 }}
                    </span>

                @elseif($latestDecisionUi->keputusan === 'approved_with_recommendation')
                    <span class="text-blue-600 font-semibold">
                        Menunggu Review Ulang Babak {{ $latestDecisionUi->round + 1 }}
                    </span>

                @else
                    <span class="text-blue-600 font-semibold">
                        Sudah Diputuskan
                    </span>
                @endif
            </p>
        </div>
    </div>
</div>


{{-- DOKUMEN AJUAN --}}
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <h2 class="text-xl font-semibold mb-4">Dokumen Ajuan</h2>

    @forelse($protocol->documents as $doc)
        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg mb-2">
            <span class="font-medium">
                {{ $doc->name ?? $doc->type ?? 'Dokumen' }}
            </span>

            <a href="{{ route('sekretariat.verifikasi.download', $doc->id) }}"
               class="text-purple-600 text-sm hover:underline">
                <i class="fas fa-download"></i> Download
            </a>
        </div>
    @empty
        <p class="text-gray-500">Belum ada dokumen yang diunggah.</p>
    @endforelse

    @if($protocol->revisions->isNotEmpty())
        <h3 class="font-semibold mt-4 mb-2 text-orange-700">
            Dokumen Revisi dari Peneliti
        </h3>

        @foreach($protocol->revisions as $rev)
            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg mb-2">
                <div>
                    <span class="font-medium">
                        {{ $rev->original_filename ?: 'Dokumen Revisi' }}
                    </span>

                    <p class="text-xs text-gray-500">
                        {{ $rev->submitted_at?->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>

                <a href="{{ route('peneliti.revisi.download', $rev->id) }}"
                   class="text-orange-700 text-sm hover:underline">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        @endforeach
    @endif
</div>


{{-- RANGKUMAN FEEDBACK REVIEWER --}}
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

    {{-- Status proses disembunyikan jika keputusan sudah final --}}
    @if(!($latestDecisionUi && in_array($latestDecisionUi->keputusan, ['approved', 'rejected'])))
        <div class="mt-4 p-3 rounded-lg border text-sm
            @if($latestDecisionUi && $latestDecisionUi->keputusan === 'approved_with_recommendation')
                bg-orange-50 border-orange-200 text-orange-700
            @elseif($isComplete)
                bg-purple-50 border-purple-200 text-purple-700
            @else
                bg-yellow-50 border-yellow-200 text-yellow-800
            @endif">

            <strong>Status Proses:</strong>

            @if($latestDecisionUi && $latestDecisionUi->keputusan === 'approved_with_recommendation' && $menungguRevisiPeneliti)
                <span>
                    <i class="fas fa-clock mr-1"></i>
                    Menunggu peneliti mengunggah revisi berdasarkan rekomendasi sekretariat.
                </span>

            @elseif($latestDecisionUi && $latestDecisionUi->keputusan === 'approved_with_recommendation' && $isComplete)
                <span>
                    <i class="fas fa-check-circle mr-1"></i>
                    Review ulang telah selesai. Silakan tetapkan keputusan babak berikutnya.
                </span>

            @elseif($latestDecisionUi && $latestDecisionUi->keputusan === 'approved_with_recommendation')
                <span>
                    <i class="fas fa-clock mr-1"></i>
                    Menunggu proses review ulang selesai.
                    Progress: {{ $completedReviews }}/{{ $totalReviewers }} reviewer.
                </span>

            @elseif($isComplete)
                <span>
                    <i class="fas fa-check-circle mr-1"></i>
                    Semua reviewer telah selesai. Silakan tetapkan keputusan.
                </span>

            @else
                <span>
                    <i class="fas fa-clock mr-1"></i>
                    Menunggu {{ max($totalReviewers - $completedReviews, 0) }} reviewer lagi
                    ({{ $completedReviews }}/{{ $totalReviewers }})
                </span>
            @endif
        </div>
    @endif
</div>


{{-- HISTORI KEPUTUSAN SEKRETARIAT --}}
@if($decisionHistory->isNotEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
        <h2 class="text-lg font-semibold text-blue-800 mb-3">
            Histori Keputusan Sekretariat
        </h2>

        <div class="space-y-3">
            @foreach($orderedDecisions as $decision)
                <div class="bg-white rounded-lg p-3 border border-blue-100">
                    <div class="flex justify-between items-start gap-3">
                        <p class="font-semibold text-sm">
                            Babak {{ $decision->round }}

                            @if($latestDecisionUi && $decision->id === $latestDecisionUi->id)
                                <span class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded-full ml-1">
                                    Terbaru
                                </span>
                            @endif
                        </p>

                        <span class="text-xs px-2 py-1 rounded-full
                            @if($decision->keputusan == 'approved') bg-green-100 text-green-800
                            @elseif($decision->keputusan == 'approved_with_recommendation') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $decisionLabels[$decision->keputusan] ?? strtoupper(str_replace('_', ' ', $decision->keputusan)) }}
                        </span>
                    </div>

                    @if($decision->catatan)
                        <p class="text-gray-600 text-sm mt-1">
                            {{ $decision->catatan }}
                        </p>
                    @else
                        <p class="text-gray-400 text-sm mt-1 italic">
                            Tidak ada catatan.
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        @if($latestDecisionUi && $latestDecisionUi->keputusan === 'approved_with_recommendation' && $menungguRevisiPeneliti)
            <p class="text-sm text-orange-700 bg-orange-50 border border-orange-200 rounded-lg p-3 mt-3">
                <i class="fas fa-info-circle"></i>
                Pengajuan ini sedang menunggu Peneliti mengunggah revisi berdasarkan rekomendasi terbaru.
            </p>
        @endif
    </div>
@endif


{{-- FORM / STATUS KEPUTUSAN --}}
@if($isFinalDecisionUi)
    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
        @if($latestDecisionUi->keputusan === 'approved')
            <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>

            <h2 class="text-lg font-semibold text-gray-800">
                Keputusan Final: Approved
            </h2>

            <p class="text-gray-600 mt-1">
                Pengajuan ini sudah disetujui final oleh sekretariat.
            </p>
        @else
            <i class="fas fa-times-circle text-4xl text-red-500 mb-2"></i>

            <h2 class="text-lg font-semibold text-gray-800">
                Keputusan Final: Disapproved
            </h2>

            <p class="text-gray-600 mt-1">
                Pengajuan ini sudah ditolak final oleh sekretariat.
            </p>
        @endif

        <p class="text-sm text-gray-500 mt-2">
            Tidak ada keputusan lanjutan yang perlu ditetapkan.
        </p>
    </div>

@elseif($isComplete)
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="text-xl font-semibold mb-4">
            Tetapkan Keputusan Babak {{ $nextRoundUi }}
        </h2>

        @if($decisionHistory->isNotEmpty())
            <p class="text-sm text-gray-500 mb-4">
                Reviewer pada babak terbaru telah menyelesaikan telaah ulang.
                Silakan tetapkan keputusan untuk Babak {{ $nextRoundUi }}.
                Keputusan sebelumnya tetap tersimpan sebagai histori.
            </p>
        @endif

        <form method="POST" action="{{ route('sekretariat.decision.store', $protocol->id) }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium mb-2">
                    Keputusan <span class="text-red-500">*</span>
                </label>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio"
                               name="keputusan"
                               value="approved"
                               {{ $selectedDecision == 'approved' ? 'checked' : '' }}
                               required>

                        <span class="font-medium text-green-700">Approved</span>
                        <span class="text-sm text-gray-500">- Disetujui final</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio"
                               name="keputusan"
                               value="approved_with_recommendation"
                               {{ $selectedDecision == 'approved_with_recommendation' ? 'checked' : '' }}
                               required>

                        <span class="font-medium text-yellow-600">Approved with Recommendation</span>
                        <span class="text-sm text-gray-500">- Peneliti revisi, lalu diteruskan ke reviewer</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio"
                               name="keputusan"
                               value="rejected"
                               {{ $selectedDecision == 'rejected' ? 'checked' : '' }}
                               required>

                        <span class="font-medium text-red-600">Disapproved</span>
                        <span class="text-sm text-gray-500">- Proposal ditolak final</span>
                    </label>
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
                Simpan Keputusan
            </button>
        </form>
    </div>

@else
    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
        @if($menungguRevisiPeneliti)
            <i class="fas fa-hourglass-half text-4xl text-orange-500 mb-2"></i>

            <p class="text-gray-600">
                Pengajuan ini sedang dikembalikan ke Peneliti untuk diperbaiki
                melalui keputusan Approved with Recommendation.
            </p>

            <p class="text-sm text-gray-500 mt-1">
                Keputusan baru hanya dapat ditetapkan setelah Peneliti mengunggah revisi
                dan reviewer menyelesaikan telaah ulang.
            </p>
        @else
            <i class="fas fa-clock text-4xl text-yellow-500 mb-2"></i>

            <p class="text-gray-600">
                Menunggu semua reviewer menyelesaikan feedback sebelum keputusan dapat ditetapkan.
            </p>

            <p class="text-sm text-gray-500 mt-1">
                Progress: {{ $completedReviews }}/{{ $totalReviewers }} reviewer selesai
            </p>
        @endif
    </div>
@endif

@endsection