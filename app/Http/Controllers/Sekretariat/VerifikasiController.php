<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\Document;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerifikasiController extends Controller
{
    public function index()
    {
        $protocols = Protocol::whereIn('status', ['new_proposal', 'assigned_to_secretary'])->with('user')->get();
        return view('sekretariat.verifikasi.index', compact('protocols'));
    }

    public function show(Protocol $protocol)
    {
        $documents = $protocol->documents;
        return view('sekretariat.verifikasi.detail', compact('protocol', 'documents'));
    }

    public function check(Request $request, Protocol $protocol)
    {
        $request->validate([
            'action' => 'required|in:lengkap,tidak_lengkap',
            'review_type' => 'required_if:action,lengkap|in:exempted,expedited,full_board',
            'catatan' => 'nullable|string',
        ]);

        // ========== VALIDASI DOKUMEN WAJIB (jika action = lengkap) ==========
        if ($request->action == 'lengkap') {
            // Cari dokumen wajib berdasarkan protocol_id dan type di database
            $dokumenWajib = Document::where('protocol_id', $protocol->id)
                ->whereIn('type', ['formular_pengajuan', 'formular_ringkasan'])
                ->get();
            
            $semuaWajibTercentang = true;
            foreach ($dokumenWajib as $doc) {
                if (!$request->has('kelengkapan.' . $doc->id)) {
                    $semuaWajibTercentang = false;
                    break;
                }
            }
            
            if (!$semuaWajibTercentang) {
                return back()->withErrors(['kelengkapan' => 'Harap centang semua dokumen wajib (Formulir Pengajuan dan Ringkasan Protokol).']);
            }
        }

        // ========== SIMPAN KE TABEL VERIFICATIONS ==========
        $verification = Verification::updateOrCreate(
    ['protocol_id' => $protocol->id],
    [
        'sekretariat_id' => Auth::id(),
        'verified_at' => now(),
        'notes' => $request->catatan,
        'exempted_reason' => $request->exempted_reason ?? null,
        'status' => $request->action,
        'review_type' => $request->action == 'lengkap' ? $request->review_type : null,
    ]
);


        // ========== UPDATE STATUS PROTOCOL ==========
        if ($request->action == 'lengkap') {
            if ($request->review_type == 'exempted') {
                $protocol->status = 'approved';
                 // TODO: Trigger pembuatan Surat Kelaikan Etik (PB-19)
                 // TODO: Kirim notifikasi ke peneliti (PB-29)
            } else {
                // Expedited atau Full Board
                $protocol->status = 'ready_for_reviewer_assignment';
                }
            } else {
                $protocol->status = 'revision_required';
            }
            $protocol->save();


        return redirect()->route('sekretariat.verifikasi.index')
            ->with('success', 'Verifikasi berhasil disimpan.');
    }

    public function download(Document $document)
    {
        // Tentukan nama file untuk download (prioritaskan original_name, lalu name, lalu fallback)
        $fileName = $document->original_name ?? $document->name ?? 'dokumen';
        
        // Cek di disk 'public' terlebih dahulu (storage/app/public)
        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $fileName);
        }
        
        // Cek di disk default (storage/app)
        if (Storage::exists($document->file_path)) {
            return Storage::download($document->file_path, $fileName);
        }
        
        // Jika file tidak ditemukan di kedua lokasi
        abort(404, 'File tidak ditemukan. Path: ' . $document->file_path);
    }
}