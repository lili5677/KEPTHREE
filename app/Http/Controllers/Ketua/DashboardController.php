<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\SkeDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $ketuaId = Auth::id();

        $menungguTtd = SkeDocument::where('ketua_id', $ketuaId)
            ->where('status', 'menunggu_ttd')
            ->count();

        $sudahTtd = SkeDocument::where('ketua_id', $ketuaId)
            ->where('status', 'sudah_ttd')
            ->count();

        $terbit = SkeDocument::where('ketua_id', $ketuaId)
            ->where('status', 'terbit')
            ->count();

        $totalDitangani = SkeDocument::where('ketua_id', $ketuaId)
            ->whereIn('status', ['menunggu_ttd', 'sudah_ttd', 'terbit'])
            ->count();

        $skeMenunggu = SkeDocument::with(['protocol.user'])
            ->where('ketua_id', $ketuaId)
            ->where('status', 'menunggu_ttd')
            ->latest('dikirim_ke_ketua_at')
            ->take(5)
            ->get();

        $riwayatTerbaru = SkeDocument::with(['protocol.user'])
            ->where('ketua_id', $ketuaId)
            ->whereIn('status', ['sudah_ttd', 'terbit'])
            ->latest('ditandatangani_at')
            ->take(5)
            ->get();

        return view('dashboard.ketua', compact(
            'menungguTtd',
            'sudahTtd',
            'terbit',
            'totalDitangani',
            'skeMenunggu',
            'riwayatTerbaru'
        ));
    }

    public function updateNip(Request $request)
    {
        $request->validate([
            'nip' => ['required', 'string', 'max:50'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
        ]);

        $user = auth()->user();

        $user->update([
            'nip' => $request->nip,
        ]);

        return back()->with('success', 'NIP berhasil disimpan.');
    }
}