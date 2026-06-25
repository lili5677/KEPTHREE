<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\User;
use App\Models\Notification;
use App\Models\SkeDocument;
use App\Services\SkeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EthicalClearanceController extends Controller
{
    private string $settingPath = 'ethical_clearance_setting.json';

    public function __construct(private SkeGeneratorService $skeGenerator)
    {
    }

    // ─── Private helpers ───────────────────────────────────────────

    private function getSetting(): array
    {
        if (Storage::exists($this->settingPath)) {
            return json_decode(Storage::get($this->settingPath), true) ?? [];
        }
        return [
            'kode_institusi'   => 'KEP',
            'tahun'            => date('Y'),
            'ketua_default_id' => null,
        ];
    }

    private function writeSettingToFile(array $data): void
    {
        Storage::put($this->settingPath, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function generateNomorSurat(): string
    {
        $setting = $this->getSetting();
        $kode    = $setting['kode_institusi'] ?? 'KEP';
        $tahun   = $setting['tahun'] ?? date('Y');

        $count = SkeDocument::where('nomor_surat', 'like', "{$kode}/{$tahun}/%")->count();
        $urut  = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return "{$kode}/{$tahun}/{$urut}";
    }

    // ─── Halaman utama ──────────────────────────────────────────────

    /**
     * Halaman utama Ethical Clearance — 4 tab:
     * 1. Perlu Diterbitkan (approved tapi belum ada SkeDocument)
     * 2. Menunggu Proses (sudah ada SkeDocument, status berjalan)
     * 3. Arsip (status terbit)
     * 4. Setting format & ketua default
     */
    public function index()
    {
        $setting = $this->getSetting();

        // Protokol approved yang BELUM punya SKE sama sekali → perlu diterbitkan
        $perluDiterbitkan = Protocol::with(['user', 'sekretariat'])
            ->where('status', 'approved')
            ->whereDoesntHave('skeDocument')
            ->orderByDesc('updated_at')
            ->get();

        // SKE yang sedang berjalan (belum terbit, belum draft murni)
        $sedangProses = SkeDocument::with(['protocol.user', 'ketua'])
            ->whereIn('status', ['menunggu_konfirmasi', 'revisi', 'menunggu_ttd', 'sudah_ttd'])
            ->orderByDesc('updated_at')
            ->get();

        // Arsip SKE yang sudah terbit
        $arsip = SkeDocument::with(['protocol.user', 'ketua'])
            ->where('status', 'terbit')
            ->orderByDesc('diterbitkan_at')
            ->paginate(10, ['*'], 'page_arsip');

        // Ketua yang BOLEH dipilih untuk TTD (wajib sudah input NIP sendiri)
        $ketuaList = User::role('ketua')->whereNotNull('nip')->where('nip', '!=', '')->get();

        // Semua ketua (termasuk yang belum isi NIP) — untuk ditampilkan di tab Setting
        // sebagai info siapa saja yang belum bisa ditugaskan TTD
        $semuaKetua = User::role('ketua')->get();
        $suggestNomor = $this->generateNomorSurat();

        return view('admin.ethical-clearance.index', compact(
            'setting',
            'perluDiterbitkan',
            'sedangProses',
            'arsip',
            'ketuaList',
            'semuaKetua',
            'suggestNomor'
        ));
    }

    // ─── Step 1: Admin menerbitkan SKE (nomor + ketua) ──────────────

    /**
     * Admin mengisi nomor surat & pilih ketua, sistem generate dokumen SKE
     * dari template, lalu kirim ke peneliti untuk dikonfirmasi.
     */
    public function terbitkanSke(Request $request, Protocol $protocol)
    {
        $request->validate([
            'nomor_surat' => ['required', 'string', 'max:100', 'unique:ske_documents,nomor_surat'],
            'ketua_id'    => ['required', 'exists:users,id'],
        ]);

        if ($protocol->status !== 'approved') {
            return back()->with('error', 'Protokol harus berstatus Approved untuk diterbitkan SKE.');
        }

        if ($protocol->skeDocument) {
            return back()->with('error', 'Protokol ini sudah memiliki SKE.');
        }

        $ketua = User::role('ketua')->findOrFail($request->ketua_id);

        if (empty($ketua->nip)) {
            return back()->with('error', "{$ketua->name} belum mengisi NIP. Ketua wajib melengkapi NIP sebelum bisa ditugaskan menandatangani SKE.");
        }

        $ske = DB::transaction(function () use ($protocol, $ketua, $request) {
            // Buat record SKE
            $ske = SkeDocument::create([
                'protocol_id'     => $protocol->id,
                'nomor_surat'     => $request->nomor_surat,
                'ketua_id'        => $ketua->id,
                'tanggal_terbit'  => now()->toDateString(),
                'status'          => 'draft',
            ]);

            // Sinkronkan nomor registrasi & ketua penandatangan ke tabel protocols
            // agar konsisten dengan data yang sudah ada (tanpa ubah migration)
            $protocol->update([
                'nomor_registrasi'       => $request->nomor_surat,
                'ketua_penandatangan_id' => $ketua->id,
            ]);

            return $ske;
        });

        // Generate file docx dari template
        try {
            $filePath = $this->skeGenerator->generate($ske);
            $ske->update(['file_path' => $filePath]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat dokumen SKE: ' . $e->getMessage());
        }

        // Kirim ke peneliti untuk konfirmasi
        DB::transaction(function () use ($ske, $protocol) {
            $ske->update([
                'status'                 => 'menunggu_konfirmasi',
                'dikirim_ke_peneliti_at' => now(),
            ]);

            Notification::create([
                'user_id' => $protocol->user_id,
                'message' => "SKE untuk protokol \"{$protocol->title}\" telah diterbitkan dengan nomor {$ske->nomor_surat}. Mohon periksa kebenaran data sebelum dilanjutkan ke tahap tanda tangan.",
            ]);
        });

        return redirect()->route('admin.ethical-clearance.index')
            ->with('success', "SKE {$ske->nomor_surat} berhasil dibuat dan dikirim ke peneliti untuk konfirmasi.");
    }

    // ─── Step 2: Proses revisi (dari catatan peneliti) ──────────────

    /**
     * Dipanggil ketika admin menerima notifikasi revisi dari peneliti
     * dan meminta sistem memproses ulang SKE dengan catatan tersebut.
     * (Catatan revisi sendiri diasumsikan sudah tersimpan oleh role peneliti
     * ke kolom `catatan_revisi` & status `revisi` — di luar scope ini.
     * Method ini menangani sisi admin: regenerate & kirim ke ketua.)
     */
    public function prosesRevisi(SkeDocument $ske)
    {
        if ($ske->status !== 'revisi') {
            return back()->with('error', 'SKE ini tidak dalam status revisi.');
        }

        // Regenerate ulang dokumen (data protokol bisa saja sudah diperbarui)
        try {
            $filePath = $this->skeGenerator->generate($ske);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses ulang dokumen SKE: ' . $e->getMessage());
        }

        DB::transaction(function () use ($ske, $filePath) {
            $ske->update([
                'file_path'           => $filePath,
                'status'              => 'menunggu_ttd',
                'direvisi_at'         => now(),
                'dikirim_ke_ketua_at' => now(),
            ]);

            Notification::create([
                'user_id' => $ske->ketua_id,
                'message' => "SKE {$ske->nomor_surat} (revisi) telah siap dan menunggu tanda tangan Anda.",
            ]);
        });

        return redirect()->route('admin.ethical-clearance.index')
            ->with('success', "SKE {$ske->nomor_surat} berhasil direvisi dan dikirim ke ketua untuk tanda tangan.");
    }

    // ─── Step 2b: Tidak ada revisi → langsung kirim ke ketua ────────

    /**
     * Dipanggil ketika peneliti mengonfirmasi tidak ada kesalahan
     * (di sisi admin: tombol untuk meneruskan SKE ke ketua).
     */
    public function kirimKeKetua(SkeDocument $ske)
    {
        if ($ske->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'SKE ini belum dalam status menunggu konfirmasi peneliti.');
        }

        DB::transaction(function () use ($ske) {
            $ske->update([
                'status'              => 'menunggu_ttd',
                'dikirim_ke_ketua_at' => now(),
            ]);

            Notification::create([
                'user_id' => $ske->ketua_id,
                'message' => "SKE {$ske->nomor_surat} telah siap dan menunggu tanda tangan Anda.",
            ]);
        });

        return redirect()->route('admin.ethical-clearance.index')
            ->with('success', "SKE {$ske->nomor_surat} dikirim ke ketua untuk tanda tangan.");
    }

    // ─── Step 3: Terbitkan final ke peneliti (setelah ditandatangani) ──

    /**
     * Dipanggil setelah ketua menandatangani (status sudah_ttd),
     * admin menerbitkan SKE final ke peneliti.
     */
    public function publish(SkeDocument $ske)
    {
        if ($ske->status !== 'sudah_ttd') {
            return back()->with('error', 'SKE ini belum ditandatangani oleh ketua.');
        }

        DB::transaction(function () use ($ske) {
            $ske->update([
                'status'           => 'terbit',
                'diterbitkan_at'   => now(),
            ]);

            Notification::create([
                'user_id' => $ske->protocol->user_id,
                'message' => "SKE {$ske->nomor_surat} telah resmi diterbitkan dan dapat diunduh.",
            ]);
        });

        return redirect()->route('admin.ethical-clearance.index')
            ->with('success', "SKE {$ske->nomor_surat} berhasil diterbitkan ke peneliti.");
    }

    // ─── Download dokumen SKE (draft/signed) ────────────────────────

    public function downloadSke(SkeDocument $ske)
    {
        $path = $ske->signed_file_path ?? $ske->file_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File SKE tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $path,
            str_replace(['/', ' '], '_', $ske->nomor_surat) . '.pdf'
        );
    }

    /**
     * Tampilkan PDF SKE langsung di browser (inline), bukan paksa download.
     */
    public function previewSke(SkeDocument $ske)
    {
        $path = $ske->signed_file_path ?? $ske->file_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File SKE tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($path);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ─── Setting format & ketua default ─────────────────────────────

    public function saveSettingForm(Request $request)
    {
        $request->validate([
            'kode_institusi'   => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9]+$/i'],
            'tahun'            => ['required', 'digits:4'],
            'ketua_default_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($request->ketua_default_id) {
            $ketua = User::role('ketua')->findOrFail($request->ketua_default_id);

            if (empty($ketua->nip)) {
                return back()->with('error', "{$ketua->name} belum mengisi NIP dan tidak bisa dijadikan ketua default.");
            }
        }

        $this->writeSettingToFile([
            'kode_institusi'   => strtoupper($request->kode_institusi),
            'tahun'            => $request->tahun,
            'ketua_default_id' => $request->ketua_default_id ? (int) $request->ketua_default_id : null,
        ]);

        return redirect()->route('admin.ethical-clearance.index')
            ->with('success', 'Setting berhasil disimpan.');
    }
}