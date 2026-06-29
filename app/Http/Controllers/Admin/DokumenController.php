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
        $documents = $protocol->documents;

        if ($documents->isEmpty()) {
            return back()->with('error', 'Tidak ada dokumen untuk diunduh.');
        }

        // Jika hanya 1 dokumen, langsung download
        if ($documents->count() === 1) {
            $doc = $documents->first();

            if (!Storage::disk('local')->exists($doc->file_path)) {
                return back()->with('error', 'File tidak ditemukan.');
            }

            return Storage::disk('local')->download($doc->file_path, $doc->name);
        }

        // Lebih dari 1: buat zip
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $kodeAman = preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '-',
            $protocol->nomor_registrasi ?? 'PRO-' . $protocol->id
        );
        $kodeAman = trim($kodeAman, '-');

        $baseName = 'dokumen_' . $kodeAman . '.zip';
        // Nama file fisik dibuat unik supaya tidak collision saat ada download bersamaan
        $zipFileName = 'dokumen_' . $kodeAman . '_' . uniqid() . '.zip';
        $zipPath = $tempDir . '/' . $zipFileName;

        $zip = new \ZipArchive();
        $opened = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($opened !== true) {
            // $opened berupa kode error ZipArchive (integer) kalau gagal,
            // dicatat ke log supaya gampang didiagnosis kalau masih gagal lagi.
            \Illuminate\Support\Facades\Log::error('Gagal membuka ZipArchive', [
                'protocol_id' => $protocol->id,
                'zip_path'    => $zipPath,
                'error_code'  => $opened,
            ]);

            return back()->with('error', 'Gagal membuat file zip (kode: ' . $opened . '). Coba lagi.');
        }

        $addedCount = 0;
        $usedNames  = [];

        foreach ($documents as $doc) {
            if (!Storage::disk('local')->exists($doc->file_path)) {
                continue;
            }

            $fullPath = Storage::disk('local')->path($doc->file_path);

            // Hindari nama file ganda di dalam zip (mis. 2 dokumen bernama "Surat.pdf")
            $entryName = $doc->name;
            if (isset($usedNames[$entryName])) {
                $usedNames[$entryName]++;
                $ext  = pathinfo($entryName, PATHINFO_EXTENSION);
                $base = pathinfo($entryName, PATHINFO_FILENAME);
                $entryName = $base . ' (' . $usedNames[$entryName] . ')' . ($ext ? '.' . $ext : '');
            } else {
                $usedNames[$entryName] = 0;
            }

            $zip->addFile($fullPath, $entryName);
            $addedCount++;
        }

        $zip->close();

        // Kalau ternyata tidak ada satupun file yang berhasil ditambahkan
        if ($addedCount === 0) {
            @unlink($zipPath);
            return back()->with('error', 'File dokumen tidak ditemukan di server.');
        }

        return response()->download($zipPath, $baseName)->deleteFileAfterSend(true);
    }
}