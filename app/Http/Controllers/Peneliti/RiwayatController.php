<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    /**
     * Daftar semua pengajuan milik peneliti yang sedang login.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Protocol::where('user_id', $user->id)
                        ->with(['documents', 'verification', 'revisions', 'skeDocument'])
                         ->latest('submitted_at')
                         ->latest('created_at');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear(
                DB::raw('COALESCE(submitted_at, created_at)'),
                $request->tahun
            );
        }

        $protocols = $query->paginate(10)->withQueryString();

        // Tahun tersedia untuk dropdown
        $availableYears = Protocol::where('user_id', $user->id)
            ->selectRaw('YEAR(COALESCE(submitted_at, created_at)) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->filter()
            ->values();

        return view('peneliti.riwayat', compact('protocols', 'availableYears'));
    }

    /**
     * Halaman detail satu pengajuan.
     */
    public function show(Protocol $protocol)
    {
        // Pastikan hanya pemilik yang bisa melihat
        if ($protocol->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $protocol->load(['documents', 'user', 'verification', 'revisions', 'skeDocument.ketua']);

        return view('peneliti.riwayat-detail', compact('protocol'));
    }

    /**
     * Download dokumen dari pengajuan.
     */
    public function downloadDokumen(Document $document)
    {
        if ($document->protocol->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->name
        );
    }
}