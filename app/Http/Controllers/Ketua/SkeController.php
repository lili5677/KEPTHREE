<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SkeDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SkeController extends Controller
{
    private function authorizeKetua(SkeDocument $ske): void
    {
        if ((int) $ske->ketua_id !== (int) Auth::id()) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $skeList = SkeDocument::with(['protocol.user', 'ketua'])
            ->where('ketua_id', Auth::id())
            ->where('status', 'menunggu_ttd')
            ->latest('dikirim_ke_ketua_at')
            ->paginate(10);

        return view('ketua.ske-index', compact('skeList'));
    }

    public function show(SkeDocument $ske)
    {
        $this->authorizeKetua($ske);

        $ske->load(['protocol.user', 'ketua']);

        return view('ketua.ske-show', compact('ske'));
    }

    public function preview(SkeDocument $ske)
    {
        $this->authorizeKetua($ske);

        $path = $ske->status === 'menunggu_ttd'
            ? $ske->file_path
            : ($ske->signed_file_path ?: $ske->file_path);

        if (!$path || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'Dokumen SKE tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function uploadSigned(Request $request, SkeDocument $ske)
    {
        $this->authorizeKetua($ske);

        if ($ske->status !== 'menunggu_ttd') {
            return back()->with('error', 'SKE ini tidak sedang menunggu tanda tangan ketua.');
        }

        $request->validate([
            'signed_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'signed_file.required' => 'File SKE yang sudah ditandatangani wajib diunggah.',
            'signed_file.mimes'    => 'File SKE bertanda tangan harus berformat PDF.',
            'signed_file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $file = $request->file('signed_file');

        // Baca isi file
        $contents = file_get_contents($file->getPathname());

        // Tentukan nama dan lokasi file
        $path = 'ske/signed/SKE-TTD-' . $ske->id . '-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();

        // Simpan ke storage
        Storage::disk('public')->put($path, $contents);

        DB::transaction(function () use ($ske, $path) {
            $ske->update([
                'signed_file_path'  => $path,
                'status'            => 'sudah_ttd',
                'ditandatangani_at' => now(),
            ]);

            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'message' => "SKE {$ske->nomor_surat} telah ditandatangani oleh ketua dan siap diterbitkan ke peneliti.",
                    'is_read' => false,
                ]);
            }
        });

        return redirect()
            ->route('ketua.riwayat')
            ->with('success', "SKE {$ske->nomor_surat} berhasil diunggah sebagai dokumen bertanda tangan.");
    }

    public function history()
    {
        $history = SkeDocument::with(['protocol.user', 'ketua'])
            ->where('ketua_id', Auth::id())
            ->whereIn('status', ['sudah_ttd', 'terbit'])
            ->latest('ditandatangani_at')
            ->paginate(10);

        return view('ketua.riwayat', compact('history'));
    }

    public function historyShow(SkeDocument $ske)
    {
        $this->authorizeKetua($ske);

        $ske->load(['protocol.user', 'ketua']);

        return view('ketua.ske-show', compact('ske'));
    }

    public function downloadSigned(SkeDocument $ske)
    {
        $this->authorizeKetua($ske);

        $path = $ske->signed_file_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File SKE bertanda tangan tidak ditemukan.');
        }

        $fileName = 'SKE-TTD-' . str_replace(['/', '\\'], '-', $ske->nomor_surat) . '.pdf';

        return Storage::disk('public')->download($path, $fileName);
    }
}