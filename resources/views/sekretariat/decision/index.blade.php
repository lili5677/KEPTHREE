@extends('layouts.sekretariat')

@section('title', 'Secretary Decision – Sistem KEP')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Secretary Decision</h1>
    <p class="text-gray-500">
        Kelola keputusan sekretariat berdasarkan hasil review reviewer
    </p>
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

{{-- FILTER --}}
<form method="GET"
      action="{{ route('sekretariat.decision.index') }}"
      class="bg-white rounded-xl shadow-sm p-4 mb-5">

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Jalur Review
            </label>
            <select name="review_type" class="w-full border rounded-lg p-2">
                <option value="">Semua Jalur</option>
                <option value="expedited" {{ request('review_type') == 'expedited' ? 'selected' : '' }}>
                    Expedited
                </option>
                <option value="full_board" {{ request('review_type') == 'full_board' ? 'selected' : '' }}>
                    Full Board
                </option>
                <option value="exempted" {{ request('review_type') == 'exempted' ? 'selected' : '' }}>
                    Exempted
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Keputusan Sekretariat
            </label>
            <select name="decision_status" class="w-full border rounded-lg p-2">
                <option value="">Semua Keputusan</option>
                <option value="undecided" {{ request('decision_status') == 'undecided' ? 'selected' : '' }}>
                    Belum Diputuskan
                </option>
                <option value="decided" {{ request('decision_status') == 'decided' ? 'selected' : '' }}>
                    Sudah Diputuskan
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Progress Reviewer
            </label>
            <select name="progress_status" class="w-full border rounded-lg p-2">
                <option value="">Semua Progress</option>
                <option value="complete" {{ request('progress_status') == 'complete' ? 'selected' : '' }}>
                    Review Lengkap
                </option>
                <option value="incomplete" {{ request('progress_status') == 'incomplete' ? 'selected' : '' }}>
                    Belum Lengkap
                </option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-filter mr-1"></i>
                Filter
            </button>

            <a href="{{ route('sekretariat.decision.index') }}"
               class="w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- DESKTOP TABLE --}}
<div class="hidden lg:block bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="decision-table w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 text-left">Kode</th>
                    <th class="p-3 text-left">Pengajuan</th>
                    <th class="p-3 text-left">Review Type</th>
                    <th class="p-3 text-left">Progress Reviewer</th>
                    <th class="p-3 text-left">Keputusan Terakhir</th>
                    <th class="p-3 text-left">Aksi Saat Ini</th>
                </tr>
            </thead>

            <tbody>
                @forelse($protocols as $p)
                    @php
                        $latestDecision = $p->latestSekretariatDecision ?? null;

                        $reviewType = $p->review_type_label ?? 'unknown';

                        $reviewTypeLabel = match ($reviewType) {
                            'expedited' => 'Expedited',
                            'full_board' => 'Full Board',
                            'exempted' => 'Exempted',
                            default => 'Unknown',
                        };

                        $reviewTypeClass = match ($reviewType) {
                            'expedited' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'full_board' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'exempted' => 'bg-green-100 text-green-700 border-green-200',
                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                        };

                        $reviewTypeNote = match ($reviewType) {
                            'expedited' => 'Jalur review cepat',
                            'full_board' => 'Jalur review penuh',
                            'exempted' => 'Dikecualikan',
                            default => 'Belum diketahui',
                        };

                        $decisionLabel = '-';
                        $decisionClass = 'bg-gray-100 text-gray-700';
                        $decisionNote = 'Belum ada keputusan sekretariat.';

                        if ($latestDecision) {
                            if ($latestDecision->keputusan === 'approved') {
                                $decisionLabel = 'Approved';
                                $decisionClass = 'bg-green-100 text-green-700';
                                $decisionNote = 'Keputusan final sudah disetujui.';
                            } elseif ($latestDecision->keputusan === 'approved_with_recommendation') {
                                $decisionLabel = 'Approved with Recommendation';
                                $decisionClass = 'bg-orange-100 text-orange-700';
                                $decisionNote = 'Sudah ada keputusan revisi/rekomendasi.';
                            } elseif ($latestDecision->keputusan === 'rejected') {
                                $decisionLabel = 'Disapproved';
                                $decisionClass = 'bg-red-100 text-red-700';
                                $decisionNote = 'Keputusan final ditolak.';
                            }
                        }

                        $currentRound = $latestDecision ? $latestDecision->round + 1 : 1;
                    @endphp

                    <tr class="border-t hover:bg-gray-50 align-top">
                        <td class="p-3 whitespace-nowrap">
                            <div class="font-bold text-purple-700">
                                PRO-{{ $p->id }}
                            </div>
                        </td>

                        <td class="p-3 min-w-[280px]">
                            <div class="font-semibold text-gray-800 leading-snug">
                                {{ $p->title }}
                            </div>

                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-user"></i>
                                {{ $p->user->name ?? '-' }}
                            </div>
                        </td>

                        <td class="p-3 min-w-[160px]">
                            <div class="review-type-card">
                                <span class="inline-flex w-fit px-2 py-1 rounded-full border text-xs font-semibold {{ $reviewTypeClass }}">
                                    {{ $reviewTypeLabel }}
                                </span>

                                <p class="text-xs text-gray-500 mt-2">
                                    {{ $reviewTypeNote }}
                                </p>
                            </div>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            <div class="flex flex-col gap-2">
                                <span class="inline-flex w-fit px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $p->is_complete ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $p->review_progress }} reviewer
                                </span>

                                @if($p->is_complete)
                                    <span class="text-xs text-green-700">
                                        Review lengkap
                                    </span>
                                @else
                                    <span class="text-xs text-orange-700">
                                        Menunggu reviewer
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="p-3 min-w-[230px]">
                            @if($latestDecision)
                                <div class="decision-card decided">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-semibold text-gray-500">
                                            Keputusan Babak {{ $latestDecision->round }}
                                        </span>

                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $decisionClass }}">
                                            {{ $decisionLabel }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                                        {{ $decisionNote }}
                                    </p>
                                </div>
                            @else
                                <div class="decision-card empty">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        <span class="text-xs font-semibold text-red-700">
                                            Belum Diputuskan
                                        </span>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                                        Menunggu keputusan sekretariat babak pertama.
                                    </p>
                                </div>
                            @endif
                        </td>

                        <td class="p-3 min-w-[220px]">
                            @if(!$latestDecision)
                                @if($p->is_complete)
                                    <div class="action-box action-primary">
                                        <div class="text-xs font-semibold text-purple-700 mb-2">
                                            Keputusan Babak 1
                                        </div>

                                        <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                                           class="action-button bg-purple-600 hover:bg-purple-700">
                                            <i class="fas fa-gavel"></i>
                                            Putuskan Babak 1
                                        </a>
                                    </div>
                                @else
                                    <div class="action-box action-waiting">
                                        <div class="text-xs font-semibold text-orange-700 mb-1">
                                            Belum Bisa Diputuskan
                                        </div>

                                        <p class="text-xs text-gray-500 mb-2">
                                            Review belum lengkap.
                                        </p>

                                        <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                                           class="action-button bg-gray-500 hover:bg-gray-600">
                                            <i class="fas fa-eye"></i>
                                            Lihat Detail
                                        </a>
                                    </div>
                                @endif

                            @elseif($latestDecision->keputusan === 'approved_with_recommendation')
                                <div class="action-box action-revision">
                                    <div class="text-xs font-semibold text-orange-700 mb-2">
                                        Lanjutan / Babak {{ $currentRound }}
                                    </div>

                                    <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                                       class="action-button bg-orange-600 hover:bg-orange-700">
                                        <i class="fas fa-arrow-right"></i>
                                        Buka Lanjutan
                                    </a>
                                </div>

                            @else
                                <div class="action-box action-final">
                                    <div class="text-xs font-semibold text-blue-700 mb-2">
                                        Keputusan Final
                                    </div>

                                    <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                                       class="action-button bg-blue-600 hover:bg-blue-700">
                                        <i class="fas fa-eye"></i>
                                        Lihat Hasil
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p>Tidak ada data review yang ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MOBILE CARD --}}
<div class="lg:hidden space-y-4">
    @forelse($protocols as $p)
        @php
            $latestDecision = $p->latestSekretariatDecision ?? null;

            $reviewType = $p->review_type_label ?? 'unknown';

            $reviewTypeLabel = match ($reviewType) {
                'expedited' => 'Expedited',
                'full_board' => 'Full Board',
                'exempted' => 'Exempted',
                default => 'Unknown',
            };

            $reviewTypeClass = match ($reviewType) {
                'expedited' => 'bg-purple-100 text-purple-700 border-purple-200',
                'full_board' => 'bg-blue-100 text-blue-700 border-blue-200',
                'exempted' => 'bg-green-100 text-green-700 border-green-200',
                default => 'bg-gray-100 text-gray-700 border-gray-200',
            };

            $reviewTypeNote = match ($reviewType) {
                'expedited' => 'Jalur review cepat',
                'full_board' => 'Jalur review penuh',
                'exempted' => 'Dikecualikan',
                default => 'Belum diketahui',
            };

            $decisionLabel = '-';
            $decisionClass = 'bg-gray-100 text-gray-700';
            $decisionNote = 'Belum ada keputusan sekretariat.';

            if ($latestDecision) {
                if ($latestDecision->keputusan === 'approved') {
                    $decisionLabel = 'Approved';
                    $decisionClass = 'bg-green-100 text-green-700';
                    $decisionNote = 'Keputusan final sudah disetujui.';
                } elseif ($latestDecision->keputusan === 'approved_with_recommendation') {
                    $decisionLabel = 'Approved with Recommendation';
                    $decisionClass = 'bg-orange-100 text-orange-700';
                    $decisionNote = 'Sudah ada keputusan revisi/rekomendasi.';
                } elseif ($latestDecision->keputusan === 'rejected') {
                    $decisionLabel = 'Disapproved';
                    $decisionClass = 'bg-red-100 text-red-700';
                    $decisionNote = 'Keputusan final ditolak.';
                }
            }

            $currentRound = $latestDecision ? $latestDecision->round + 1 : 1;
        @endphp

        <div class="mobile-decision-card">
            <div class="mobile-card-header">
                <div>
                    <div class="text-sm font-bold text-purple-700">
                        PRO-{{ $p->id }}
                    </div>

                    <div class="font-semibold text-gray-800 leading-snug mt-1">
                        {{ $p->title }}
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-user"></i>
                        {{ $p->user->name ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="mobile-section-grid">
                <div class="mobile-info-box">
                    <div class="mobile-label">Review Type</div>

                    <span class="inline-flex w-fit px-2 py-1 rounded-full border text-xs font-semibold {{ $reviewTypeClass }}">
                        {{ $reviewTypeLabel }}
                    </span>

                    <p class="text-xs text-gray-500 mt-2">
                        {{ $reviewTypeNote }}
                    </p>
                </div>

                <div class="mobile-info-box">
                    <div class="mobile-label">Progress Reviewer</div>

                    <span class="inline-flex w-fit px-2 py-1 rounded-full text-xs font-semibold
                        {{ $p->is_complete ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ $p->review_progress }} reviewer
                    </span>

                    <p class="text-xs mt-2 {{ $p->is_complete ? 'text-green-700' : 'text-orange-700' }}">
                        {{ $p->is_complete ? 'Review lengkap' : 'Menunggu reviewer' }}
                    </p>
                </div>
            </div>

            <div class="mobile-info-box mt-3">
                <div class="mobile-label">Keputusan Terakhir</div>

                @if($latestDecision)
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-gray-500">
                            Keputusan Babak {{ $latestDecision->round }}
                        </span>

                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $decisionClass }}">
                            {{ $decisionLabel }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                        {{ $decisionNote }}
                    </p>
                @else
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="text-xs font-semibold text-red-700">
                            Belum Diputuskan
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                        Menunggu keputusan sekretariat babak pertama.
                    </p>
                @endif
            </div>

            <div class="mt-3">
                @if(!$latestDecision)
                    @if($p->is_complete)
                        <div class="action-box action-primary">
                            <div class="text-xs font-semibold text-purple-700 mb-2">
                                Keputusan Babak 1
                            </div>

                            <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                               class="action-button bg-purple-600 hover:bg-purple-700">
                                <i class="fas fa-gavel"></i>
                                Putuskan Babak 1
                            </a>
                        </div>
                    @else
                        <div class="action-box action-waiting">
                            <div class="text-xs font-semibold text-orange-700 mb-1">
                                Belum Bisa Diputuskan
                            </div>

                            <p class="text-xs text-gray-500 mb-2">
                                Review belum lengkap.
                            </p>

                            <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                               class="action-button bg-gray-500 hover:bg-gray-600">
                                <i class="fas fa-eye"></i>
                                Lihat Detail
                            </a>
                        </div>
                    @endif

                @elseif($latestDecision->keputusan === 'approved_with_recommendation')
                    <div class="action-box action-revision">
                        <div class="text-xs font-semibold text-orange-700 mb-2">
                            Lanjutan / Babak {{ $currentRound }}
                        </div>

                        <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                           class="action-button bg-orange-600 hover:bg-orange-700">
                            <i class="fas fa-arrow-right"></i>
                            Buka Lanjutan
                        </a>
                    </div>

                @else
                    <div class="action-box action-final">
                        <div class="text-xs font-semibold text-blue-700 mb-2">
                            Keputusan Final
                        </div>

                        <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                           class="action-button bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-eye"></i>
                            Lihat Hasil
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
            <p>Tidak ada data review yang ditemukan.</p>
        </div>
    @endforelse
</div>

<style>
.decision-table {
    min-width: 1180px;
}


.decision-card,
.mobile-info-box {
    border-radius: 12px;
    padding: .75rem;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}

.decision-card.decided {
    background: #f9fafb;
}

.decision-card.empty {
    background: #fff7f7;
    border-color: #fecaca;
}

.action-box {
    border-radius: 12px;
    padding: .75rem;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}

.action-primary {
    background: #faf5ff;
    border-color: #ddd6fe;
}

.action-waiting {
    background: #fffbeb;
    border-color: #fed7aa;
}

.action-revision {
    background: #fff7ed;
    border-color: #fed7aa;
}

.action-final {
    background: #eff6ff;
    border-color: #bfdbfe;
}

.action-button {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    color: #fff;
    padding: .55rem .75rem;
    border-radius: 10px;
    font-size: .78rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
}

.action-button:hover {
    color: #fff;
}

.mobile-decision-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
    padding: 1rem;
}

.mobile-card-header {
    padding-bottom: .85rem;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: .85rem;
}

.mobile-section-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: .75rem;
}

.mobile-label {
    font-size: .72rem;
    font-weight: 800;
    color: #6b7280;
    margin-bottom: .45rem;
    text-transform: uppercase;
    letter-spacing: .03em;
}

@media (min-width: 640px) and (max-width: 1023px) {
    .mobile-section-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 480px) {
    .mobile-decision-card {
        padding: .85rem;
        border-radius: 14px;
    }

    .review-type-card,
    .decision-card,
    .mobile-info-box,
    .action-box {
        padding: .65rem;
        border-radius: 10px;
    }

    .action-button {
        white-space: normal;
        text-align: center;
        line-height: 1.3;
    }
}
</style>
@endsection