<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Protocol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    // ══════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════

    public function dashboard()
    {
        $this->clearWizardSession();

        $userId = auth()->id();

        $totalPengajuan   = Protocol::where('user_id', $userId)->count();
        
        $sedangDiproses   = Protocol::where('user_id', $userId)
                                ->whereIn('status', ['new_proposal', 'under_review'])
                                ->count();
        $disetujui        = Protocol::where('user_id', $userId)
                                ->where('status', 'approved')
                                ->count();
        $pengajuanTerbaru = Protocol::where('user_id', $userId)
                                ->latest()
                                ->take(5)
                                ->get();

        return view('peneliti.dashboard', compact(
            'totalPengajuan',
            'sedangDiproses',
            'disetujui',
            'pengajuanTerbaru'
        ));
    }

    // ══════════════════════════════════════════════════
    // COMING SOON — Riwayat & Template
    // ══════════════════════════════════════════════════

    public function riwayat()
    {
        $this->clearWizardSession();
        return view('peneliti.coming_soon', [
            'fitur'    => 'Riwayat Pengajuan',
            'deskripsi'=> 'Halaman riwayat pengajuan sedang dalam pengembangan. Anda akan segera dapat melihat seluruh riwayat pengajuan protokol ethical clearance Anda di sini.',
            'icon'     => 'bi-clock-history',
        ]);
    }

    public function template()
    {
        $this->clearWizardSession();
        return view('peneliti.coming_soon', [
            'fitur'    => 'Download Template',
            'deskripsi'=> 'Halaman download template sedang dalam pengembangan. Template formulir pengajuan, ringkasan protokol, informed consent, dan surat pengantar akan segera tersedia.',
            'icon'     => 'bi-download',
        ]);
    }

    // ══════════════════════════════════════════════════
    // STEP 1 — Informasi Dasar
    // ══════════════════════════════════════════════════

    public function create()
    {
        // Jika step1 ada tapi step2 belum di-inisialisasi, buat empty array
        if (session('pengajuan_step1') && !session()->has('pengajuan_step2')) {
            session(['pengajuan_step2' => []]);
        }
        
        // Halaman awal wizard tidak clear session agar back-navigation berfungsi
        return view('peneliti.pengajuan.step1');
    }

    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'program_studi'        => 'required|string|max:255',
            'ringkasan_penelitian' => 'required|string|min:50',
            'sumber_pendanaan'     => 'nullable|string|max:255',
            'durasi_penelitian'    => 'required|integer|min:1|max:120',
        ], [
            'title.required'                => 'Judul penelitian wajib diisi.',
            'program_studi.required'        => 'Program studi wajib diisi.',
            'ringkasan_penelitian.required' => 'Ringkasan penelitian wajib diisi.',
            'ringkasan_penelitian.min'      => 'Ringkasan penelitian minimal 50 karakter.',
            'durasi_penelitian.required'    => 'Durasi penelitian wajib diisi.',
        ]);

        session(['pengajuan_step1' => $validated]);

        return redirect()->route('peneliti.pengajuan.step2');
    }

    // ══════════════════════════════════════════════════
    // STEP 2 — Upload Dokumen
    // ══════════════════════════════════════════════════

    public function step2()
    {
        if (!session('pengajuan_step1')) {
            return redirect()->route('peneliti.pengajuan.create')
                ->with('error', 'Silakan isi informasi dasar terlebih dahulu.');
        }

        $existingDocs = session('pengajuan_step2', []);

        return view('peneliti.pengajuan.step2', compact('existingDocs'));
    }

    public function autoSaveStep2(Request $request)
    {
        if (!session('pengajuan_step1')) {
            return response()->json(['error' => 'Silakan isi informasi dasar terlebih dahulu.'], 400);
        }

        $maxBytes = 2 * 1024 * 1024; // 2 MB
        $existingDocs = session('pengajuan_step2', []);
        
        $safeExt = function ($file) {
            $ext = $file->getClientOriginalExtension() ?: $file->extension() ?: 'pdf';
            return strtolower($ext);
        };

        $fieldMap = [
            // Wajib
            'formulir_pengajuan' => ['type' => 'formulir_pengajuan', 'wajib' => true, 'label' => null],
            'formulir_ringkasan' => ['type' => 'formulir_ringkasan', 'wajib' => true, 'label' => null],
            // Opsional
            'pendukung_surat_pengantar'     => ['type' => 'pendukung', 'wajib' => false, 'label' => 'Surat Pengantar'],
            'pendukung_proposal_penelitian' => ['type' => 'pendukung', 'wajib' => false, 'label' => 'Proposal Penelitian Lengkap'],
            'pendukung_informed_consent'    => ['type' => 'pendukung', 'wajib' => false, 'label' => 'Informed Consent Form (ICF)'],
            'pendukung_kuesioner'           => ['type' => 'pendukung', 'wajib' => false, 'label' => 'Kuesioner/Instrumen Penelitian'],
        ];

        $updated = false;

        foreach ($fieldMap as $inputName => $config) {
            if (!$request->hasFile($inputName) || !$request->file($inputName)->isValid()) {
                continue;
            }

            $file = $request->file($inputName);
            
            if ($file->getSize() > $maxBytes) {
                return response()->json(['error' => "Ukuran file {$inputName} melebihi 2 MB."], 422);
            }

            $content = file_get_contents($file->getPathname());
            if (!$content) {
                return response()->json(['error' => "Gagal membaca file {$inputName}."], 500);
            }

            // Tentukan field name untuk session
            $sessionField = $config['wajib'] ? $inputName : str_replace('pendukung_', '', $inputName);
            
            $docData = [
                'field'     => $sessionField,
                'type'      => $config['type'],
                'extension' => $safeExt($file),
                'name'      => $config['label'] ? ($config['label'] . ' — ' . $file->getClientOriginalName()) : $file->getClientOriginalName(),
                'size'      => $file->getSize(),
                'content'   => base64_encode($content),
                'wajib'     => $config['wajib'],
            ];

            // Update atau tambah ke session array
            $idx = array_search($sessionField, array_column($existingDocs, 'field'));
            if ($idx !== false) {
                $existingDocs[$idx] = $docData;
            } else {
                $existingDocs[] = $docData;
            }
            $updated = true;
        }

        if ($updated) {
            session(['pengajuan_step2' => $existingDocs]);
        }

        return response()->json(['success' => true, 'files' => count($existingDocs)]);
    }

    public function storeStep2(Request $request)
    {
        if (!session('pengajuan_step1')) {
            return redirect()->route('peneliti.pengajuan.create');
        }

        $maxKb        = 2048; // 2 MB
        $existingDocs = session('pengajuan_step2', []);

        // Lookup by field name
        $existingByField = [];
        foreach ($existingDocs as $doc) {
            $existingByField[$doc['field']] = $doc;
        }

        // Cek file mana yang sudah dihapus pengguna via JS
        $removedFields = $request->input('removed_fields', []);
        if (is_string($removedFields)) {
            $removedFields = array_filter(explode(',', $removedFields));
        }
        $removedFields = array_map('trim', (array) $removedFields);

        // Hapus dari existingByField jika ada di daftar removed
        foreach ($removedFields as $rf) {
            unset($existingByField[$rf]);
        }

        // Validasi: file wajib harus ada (baru atau dari session yang belum dihapus)
        $wajibFields = ['formulir_pengajuan', 'formulir_ringkasan'];
        $validationRules    = [];
        $validationMessages = [];

        foreach ($wajibFields as $field) {
            $hasSavedFile = isset($existingByField[$field]);
            $hasNewFile   = $request->hasFile($field) && $request->file($field)->isValid();

            if (!$hasSavedFile && !$hasNewFile) {
                $validationRules[$field]    = "required|file|mimes:pdf,docx|max:{$maxKb}";
            } elseif ($hasNewFile) {
                $validationRules[$field]    = "nullable|file|mimes:pdf,docx|max:{$maxKb}";
            }

            $validationMessages["{$field}.required"] = match($field) {
                'formulir_pengajuan' => 'Formulir Pengajuan Telaah Etik Baru wajib diunggah.',
                'formulir_ringkasan' => 'Formulir Ringkasan Protokol Penelitian wajib diunggah.',
                default              => "File {$field} wajib diunggah.",
            };
            $validationMessages["{$field}.max"]   = "Ukuran file " . ucfirst(str_replace('_', ' ', $field)) . " maksimal 2 MB.";
            $validationMessages["{$field}.mimes"]  = 'Format file harus PDF atau DOCX.';
        }

        if (!empty($validationRules)) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $validationRules, $validationMessages);
            
            if ($validator->fails()) {
                // Update session dengan file yang sudah dihapus sebelum redirect back
                // Ini memastikan file yang dihapus tidak muncul lagi
                $updatedDocs = array_values($existingByField);
                session(['pengajuan_step2' => $updatedDocs]);
                
                return back()->withErrors($validator)->withInput();
            }
        }

        $safeExt = function ($file) {
            $ext = $file->getClientOriginalExtension();
            if (empty($ext)) $ext = $file->extension();
            return !empty($ext) ? strtolower($ext) : 'pdf';
        };

        $sessionFiles = [];

        // ── Dokumen wajib ──
        foreach ($wajibFields as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $file    = $request->file($field);
                $content = file_get_contents($file->getPathname());

                if ($content === false || $content === '') {
                    return back()->withInput()
                        ->with('error', "Gagal membaca file {$field}. Silakan coba lagi.");
                }

                $sessionFiles[] = [
                    'field'     => $field,
                    'type'      => $field,
                    'extension' => $safeExt($file),
                    'name'      => $file->getClientOriginalName(),
                    'size'      => $file->getSize(),
                    'content'   => base64_encode($content),
                    'wajib'     => true,
                ];
            } elseif (isset($existingByField[$field])) {
                $sessionFiles[] = $existingByField[$field];
            }
        }

        // ── Dokumen pendukung opsional ──
        $pendukungLabels = [
            'surat_pengantar'     => 'Surat Pengantar',
            'proposal_penelitian' => 'Proposal Penelitian Lengkap',
            'informed_consent'    => 'Informed Consent Form (ICF)',
            'kuesioner'           => 'Kuesioner/Instrumen Penelitian',
        ];

        foreach ($pendukungLabels as $key => $label) {
            $inputName = "pendukung_{$key}";
            if ($request->hasFile($inputName) && $request->file($inputName)->isValid()) {
                $file    = $request->file($inputName);
                $content = file_get_contents($file->getPathname());

                if ($content === false || $content === '') {
                    return back()->withInput()
                        ->with('error', "Gagal membaca file {$key}. Silakan coba lagi.");
                }

                $sessionFiles[] = [
                    'field'     => $key,
                    'type'      => 'pendukung',
                    'extension' => $safeExt($file),
                    'name'      => $label . ' — ' . $file->getClientOriginalName(),
                    'size'      => $file->getSize(),
                    'content'   => base64_encode($content),
                    'wajib'     => false,
                ];
            } elseif (isset($existingByField[$key]) && !in_array($key, $removedFields)) {
                $sessionFiles[] = $existingByField[$key];
            }
        }

        session(['pengajuan_step2' => $sessionFiles]);

        return redirect()->route('peneliti.pengajuan.step3');
    }

    // ══════════════════════════════════════════════════
    // STEP 3 — Konfirmasi & Submit
    // ══════════════════════════════════════════════════

    public function step3()
    {
        if (!session('pengajuan_step1') || !session('pengajuan_step2')) {
            return redirect()->route('peneliti.pengajuan.create')
                ->with('error', 'Silakan lengkapi langkah sebelumnya.');
        }

        // Validasi tambahan: Cek file wajib masih ada di session
        $step2 = session('pengajuan_step2', []);
        $wajibFields = ['formulir_pengajuan', 'formulir_ringkasan'];
        
        foreach ($wajibFields as $field) {
            $found = collect($step2)->firstWhere('field', $field);
            if (!$found) {
                return redirect()->route('peneliti.pengajuan.step2')
                    ->with('error', "File {$field} wajib diunggah.");
            }
        }

        $step1 = session('pengajuan_step1');

        $step2Raw = session('pengajuan_step2');
        $step2    = collect($step2Raw)->map(function ($doc) {
            return [
                'field'     => $doc['field'],
                'type'      => $doc['type'],
                'extension' => $doc['extension'] ?? 'pdf',
                'name'      => $doc['name'],
                'size'      => $doc['size'] ?? null,
                'wajib'     => $doc['wajib'],
            ];
        })->toArray();

        return view('peneliti.pengajuan.step3', compact('step1', 'step2'));
    }

    public function previewDoc(int $index)
    {
        $docs = session('pengajuan_step2', []);

        if (!isset($docs[$index])) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $doc = $docs[$index];
        $ext = strtolower($doc['extension'] ?? 'pdf');

        if ($ext !== 'pdf') {
            abort(415, 'Preview hanya tersedia untuk PDF.');
        }

        $binary = base64_decode($doc['content']);

        return response($binary, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($doc['name']) . '"',
            'Content-Length'      => strlen($binary),
            'Cache-Control'       => 'no-store',
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'konfirmasi' => 'accepted',
        ], [
            'konfirmasi.accepted' => 'Anda harus menyetujui pernyataan konfirmasi.',
        ]);

        if (!session('pengajuan_step1') || !session('pengajuan_step2')) {
            return redirect()->route('peneliti.pengajuan.create')
                ->with('error', 'Data tidak lengkap. Silakan mulai ulang.');
        }

        $step1 = session('pengajuan_step1');
        $step2 = session('pengajuan_step2');

        DB::transaction(function () use ($step1, $step2) {

            $protocol = Protocol::create([
                'user_id'               => auth()->id(),
                'title'                 => $step1['title'],
                'program_studi'         => $step1['program_studi'],
                'ringkasan_penelitian'  => $step1['ringkasan_penelitian'],
                'sumber_pendanaan'      => $step1['sumber_pendanaan'] ?? null,
                'durasi_penelitian'     => $step1['durasi_penelitian'],
                'status'                => 'new_proposal',
                'nomor_registrasi'      => Protocol::generateNomorRegistrasi(),
                'is_confirmed_peneliti' => false,
                'submitted_at'          => now(),
            ]);

            foreach ($step2 as $fileData) {
                $permanentPath = "protocols/{$protocol->id}/{$fileData['field']}_{$protocol->id}.{$fileData['extension']}";

                Storage::disk('local')->put(
                    $permanentPath,
                    base64_decode($fileData['content'])
                );

                Document::create([
                    'protocol_id' => $protocol->id,
                    'type'        => $fileData['type'],
                    'name'        => $fileData['name'],
                    'file_path'   => $permanentPath,
                ]);
            }
        });

        $this->clearWizardSession();

        return redirect()->route('peneliti.dashboard')
            ->with('success', 'Pengajuan Anda berhasil dikirim! Status: New Proposal. Kami akan segera memproses pengajuan Anda.');
    }

   // ══════════════════════════════════════════════════
    // HELPER
    // ══════════════════════════════════════════════════

    private function clearWizardSession(): void
    {
        session()->forget(['pengajuan_step1', 'pengajuan_step2']);
    }
}