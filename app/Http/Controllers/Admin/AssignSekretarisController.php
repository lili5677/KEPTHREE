<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class AssignSekretarisController extends Controller
{
    /**
     * Tampilkan halaman assign sekretaris.
     * Hanya tampilkan protokol dengan status new_proposal.
     */
    public function index()
    {
        // Reset Spatie Permission cache agar data role terbaca fresh
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Protokol yang belum di-assign sekretaris (status new_proposal)
        $protocols = Protocol::with(['user', 'documents'])
            ->whereNull('sekretariat_id')
            ->where('status', 'new_proposal')
            ->orderByDesc('submitted_at')
            ->get();

        // Semua user dengan role sekretariat beserta workload (jumlah protokol aktif)
        // Gunakan 'protocols.status' agar tidak ambigu dengan kolom 'status' di tabel users
        $sekretarisList = User::role('sekretariat')
            ->withCount([
                'handledProtocols as workload' => function ($q) {
                    $q->whereNotIn('protocols.status', ['approved', 'rejected']);
                }
            ])
            ->orderBy('workload')
            ->get();

        // Proposal pertama sebagai default tampilan
        $activeProtocol = $protocols->first();

        return view('admin.assign-sekretaris.index', compact(
            'protocols',
            'sekretarisList',
            'activeProtocol'
        ));
    }

    /**
     * Proses assign sekretaris ke protokol.
     */
    public function assign(Request $request, Protocol $protocol)
    {
        $request->validate([
            'sekretariat_id' => ['required', 'exists:users,id'],
        ]);

        // Pastikan sekretaris yang dipilih memang punya role sekretariat
        $sekretaris = User::role('sekretariat')->findOrFail($request->sekretariat_id);

        // Pastikan protokol belum punya sekretaris & masih new_proposal
        if ($protocol->sekretariat_id !== null) {
            return back()->with('error', 'Protokol ini sudah memiliki sekretaris.');
        }

        DB::transaction(function () use ($protocol, $sekretaris) {
            // Update protocol: set sekretaris & ubah status
            $protocol->update([
                'sekretariat_id' => $sekretaris->id,
                'status'        => 'waiting_verification',
            ]);

            // Keluarkan ?? ke variabel dulu agar valid di dalam string
            $nomorProtokol = $protocol->nomor_registrasi ?? $protocol->id;

            // Kirim notifikasi ke sekretaris
            Notification::create([
                'user_id' => $sekretaris->id,
                'message' => "Anda ditugaskan sebagai sekretaris untuk protokol \"{$protocol->title}\" (#{$nomorProtokol}).",
            ]);
        });

        // Jika request AJAX (dari fetch/axios), kembalikan JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$sekretaris->name} berhasil ditugaskan.",
            ]);
        }

        return redirect()->route('admin.sekretaris.index')
            ->with('success', "{$sekretaris->name} berhasil ditugaskan sebagai sekretaris.");
    }
}