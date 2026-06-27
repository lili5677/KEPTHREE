@extends('layouts.sekretariat')

@section('title', 'Secretary Decision – Sistem KEP')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Secretary Decision</h1>
    <p class="text-gray-500">
        Kelola keputusan akhir berdasarkan hasil review reviewer
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

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Review Type
            </label>
            <select name="review_type" class="w-full border rounded-lg p-2">
                <option value="">Semua</option>
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
                Status Keputusan
            </label>
            <select name="decision_status" class="w-full border rounded-lg p-2">
                <option value="">Semua</option>
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
                <option value="">Semua</option>
                <option value="complete" {{ request('progress_status') == 'complete' ? 'selected' : '' }}>
                    Semua Selesai
                </option>
                <option value="incomplete" {{ request('progress_status') == 'incomplete' ? 'selected' : '' }}>
                    Belum Lengkap
                </option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                Filter
            </button>

            <a href="{{ route('sekretariat.decision.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- TABLE --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 text-left">Kode</th>
                    <th class="p-3 text-left">Judul</th>
                    <th class="p-3 text-left">Peneliti</th>
                    <th class="p-3 text-left">Review Type</th>
                    <th class="p-3 text-left">Progress</th>
                    <th class="p-3 text-left">Status Keputusan</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($protocols as $p)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-semibold text-purple-700 whitespace-nowrap">
                            PRO-{{ $p->id }}
                        </td>

                        <td class="p-3 min-w-[250px]">
                            <div class="font-medium text-gray-800">
                                {{ $p->title }}
                            </div>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            {{ $p->user->name ?? '-' }}
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                {{ strtoupper($p->review_type_label ?? 'unknown') }}
                            </span>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $p->is_complete ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $p->review_progress }} reviewer
                            </span>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            @if($p->decision_status === 'Sudah Diputuskan')
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                    Sudah Diputuskan
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                    Belum Diputuskan
                                </span>
                            @endif
                        </td>

                        <td class="p-3 text-center whitespace-nowrap">
                            <a href="{{ route('sekretariat.decision.show', $p->id) }}"
                               class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-white text-xs
                               {{ $p->decision_status === 'Sudah Diputuskan' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }}">

                                @if($p->decision_status === 'Sudah Diputuskan')
                                    <i class="fas fa-eye"></i> Lihat
                                @else
                                    <i class="fas fa-gavel"></i> Putuskan
                                @endif
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p>Tidak ada data review yang ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection