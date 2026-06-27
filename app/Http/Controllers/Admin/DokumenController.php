<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = Protocol::with(['user', 'documents'])
            ->whereNotNull('submitted_at');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search: kode, judul, atau nama peneliti
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_registrasi', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $protocols = $query->orderByDesc('submitted_at')->paginate(10)->withQueryString();

        $statusList = [
            'new_proposal'         => 'New Proposal',
            'waiting_verification' => 'Waiting Verification',
            'under_review'         => 'Under Review',
            'revision_required'    => 'Revision Required',
            'approved'             => 'Approved',
            'rejected'             => 'Rejected',
        ];

        return view('admin.dokumen.index', compact('protocols', 'statusList'));
    }

    public function show(Protocol $protocol)
    {
        $protocol->load(['user', 'documents', 'sekretariat']);

        return view('admin.dokumen.show', compact('protocol'));
    }

    public function download(Protocol $protocol)
    {
        // Zip semua dokumen protokol ini
        $documents = $protocol->documents;

        if ($documents->isEmpty()) {
            return back()->with('error', 'Tidak ada dokumen untuk diunduh.');
        }

        // Jika hanya 1 dokumen, langsung download
        if ($documents->count() === 1) {
            $doc = $documents->first();
            if (!Storage::disk('public')->exists($doc->file_path)) {
                return back()->with('error', 'File tidak ditemukan.');
            }
            return Storage::disk('public')->download($doc->file_path, $doc->name);
        }

        // Lebih dari 1: buat zip
        $zipName  = 'dokumen_' . ($protocol->nomor_registrasi ?? 'PRO-'.$protocol->id) . '.zip';
        $zipPath  = storage_path('app/temp/' . $zipName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($documents as $doc) {
            $fullPath = Storage::disk('public')->path($doc->file_path);
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, $doc->name);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }
}