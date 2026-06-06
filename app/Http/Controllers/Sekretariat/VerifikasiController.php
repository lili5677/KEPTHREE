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

        // Simpan ke tabel verifications (asumsikan tabel sudah ada)
        $verification = Verification::updateOrCreate(
            ['protocol_id' => $protocol->id],
            [
                'secretary_id' => Auth::id(),
                'verified_at' => now(),
                'notes' => $request->catatan,
                'status' => $request->action,
                'review_type' => $request->action == 'lengkap' ? $request->review_type : null,
            ]
        );

        // Update status protocol
        if ($request->action == 'lengkap') {
            $protocol->status = 'ready_for_reviewer_assignment';
        } else {
            $protocol->status = 'revision_required';
        }
        $protocol->save();

        return redirect()->route('sekretariat.verifikasi.index')
            ->with('success', 'Verifikasi berhasil disimpan.');
    }

    public function download(Document $document)
    {
        if (!Storage::exists($document->file_path)) {
            abort(404);
        }
        return Storage::download($document->file_path, $document->original_name ?? 'dokumen');
    }
}