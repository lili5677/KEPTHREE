<?php

namespace App\Http\Controllers\Peneliti;

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
    private function authorizeOwner(SkeDocument $ske): void
    {
        $ske->loadMissing('protocol');

        if (!$ske->protocol || $ske->protocol->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function show(SkeDocument $ske)
    {
        $this->authorizeOwner($ske);

        $ske->load([
            'protocol.user',
            'ketua',
        ]);

        return view('peneliti.ske.show', compact('ske'));
    }

    public function preview(SkeDocument $ske)
    {
        $this->authorizeOwner($ske);

        $path = $ske->signed_file_path ?: $ske->file_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File SKE tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function download(SkeDocument $ske)
    {
        $this->authorizeOwner($ske);

        if ($ske->status !== 'terbit') {
            return back()->with('error', 'SKE final belum diterbitkan dan belum dapat diunduh.');
        }

        $path = $ske->signed_file_path ?: $ske->file_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File SKE tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $path,
            str_replace(['/', ' '], '_', $ske->nomor_surat) . '.pdf'
        );
    }

    public function approve(SkeDocument $ske)
    {
        $this->authorizeOwner($ske);

        if ($ske->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'SKE ini tidak sedang menunggu konfirmasi peneliti.');
        }

        DB::transaction(function () use ($ske) {
            $ske->update([
                'status'              => 'menunggu_ttd',
                'catatan_revisi'      => null,
                'dikirim_ke_ketua_at' => now(),
            ]);

            if ($ske->ketua_id) {
                Notification::create([
                    'user_id' => $ske->ketua_id,
                    'message' => "SKE {$ske->nomor_surat} telah disetujui oleh peneliti dan menunggu tanda tangan Anda.",
                    'is_read' => false,
                ]);
            }
        });

        return redirect()
            ->route('peneliti.ske.show', $ske->id)
            ->with('success', 'SKE berhasil disetujui dan diteruskan ke Ketua untuk ditandatangani.');
    }

    public function reject(Request $request, SkeDocument $ske)
    {
        $this->authorizeOwner($ske);

        if ($ske->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'SKE ini tidak sedang menunggu konfirmasi peneliti.');
        }

        $request->validate([
            'catatan_revisi' => 'required|string|min:10',
        ], [
            'catatan_revisi.required' => 'Catatan perbaikan wajib diisi.',
            'catatan_revisi.min'      => 'Catatan perbaikan minimal 10 karakter.',
        ]);

        DB::transaction(function () use ($request, $ske) {
            $ske->update([
                'status'         => 'revisi',
                'catatan_revisi' => $request->catatan_revisi,
                'direvisi_at'    => now(),
            ]);

            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'message' => "Peneliti meminta perbaikan SKE {$ske->nomor_surat}. Catatan: {$request->catatan_revisi}",
                    'is_read' => false,
                ]);
            }
        });

        return redirect()
            ->route('peneliti.ske.show', $ske->id)
            ->with('success', 'Catatan perbaikan SKE berhasil dikirim ke Admin.');
    }
}