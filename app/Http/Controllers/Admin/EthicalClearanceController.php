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
use Illuminate\Validation\Rule;

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

    public function prosesRevisi(SkeDocument $ske)
    {
        if ($ske->status !== 'revisi') {
            return back()->with('error', 'SKE ini tidak dalam status revisi.');
        }

        $ske->load(['protocol.user', 'ketua']);

        try {
            $filePath = $this->skeGenerator->generate($ske);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses ulang dokumen SKE: ' . $e->getMessage());
        }

        DB::transaction(function () use ($ske, $filePath) {
            $ske->update([
                'file_path'              => $filePath,
                'signed_file_path'       => null,
                'status'                 => 'menunggu_konfirmasi',
                'catatan_revisi'         => null,
                'direvisi_at'            => now(),
                'dikirim_ke_peneliti_at' => now(),
                'dikirim_ke_ketua_at'    => null,
                'ditandatangani_at'      => null,
                'diterbitkan_at'         => null,
            ]);

            Notification::create([
                'user_id' => $ske->protocol->user_id,
                'message' => "SKE {$ske->nomor_surat} telah diperbaiki oleh Admin. Mohon periksa kembali kebenaran data SKE.",
                'is_read' => false,
            ]);
        });

        return redirect()
            ->route('admin.ethical-clearance.index')
            ->with('success', "SKE {$ske->nomor_surat} berhasil diproses ulang dan dikirim kembali ke peneliti.");
    }

    // ─── Step 2b: Tidak ada revisi → langsung kirim ke ketua ────────

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

    public function editRevisi(SkeDocument $ske)
    {
        if ($ske->status !== 'revisi') {
            return redirect()
                ->route('admin.ethical-clearance.index')
                ->with('error', 'SKE ini tidak sedang dalam status revisi.');
        }

        $ske->load(['protocol.user', 'ketua']);

        $ketuaList = User::role('ketua')
            ->whereNotNull('nip')
            ->where('nip', '!=', '')
            ->get();

        return view('admin.ethical-clearance.revisi', compact('ske', 'ketuaList'));
    }

    public function updateRevisi(Request $request, SkeDocument $ske)
    {
        if ($ske->status !== 'revisi') {
            return redirect()
                ->route('admin.ethical-clearance.index')
                ->with('error', 'SKE ini tidak sedang dalam status revisi.');
        }

        $request->validate([
            'nomor_surat' => [
                'required',
                'string',
                'max:100',
                Rule::unique('ske_documents', 'nomor_surat')->ignore($ske->id),
            ],
            'ketua_id'           => ['required', 'exists:users,id'],
            'tanggal_terbit'    => ['required', 'date'],
            'title'              => ['required', 'string', 'max:255'],
            'program_studi'      => ['nullable', 'string', 'max:255'],
            'sumber_pendanaan'   => ['nullable', 'string', 'max:255'],
            'durasi_penelitian'  => ['nullable', 'integer', 'min:1', 'max:120'],
        ], [
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique'   => 'Nomor surat sudah digunakan.',
            'ketua_id.required'    => 'Ketua penandatangan wajib dipilih.',
            'title.required'       => 'Judul penelitian wajib diisi.',
        ]);

        $ketua = User::role('ketua')->findOrFail($request->ketua_id);

        if (empty($ketua->nip)) {
            return back()
                ->withInput()
                ->with('error', "{$ketua->name} belum mengisi NIP. Ketua wajib melengkapi NIP sebelum bisa ditugaskan menandatangani SKE.");
        }

        DB::transaction(function () use ($request, $ske, $ketua) {
            $protocol = $ske->protocol()->lockForUpdate()->firstOrFail();

            $protocol->update([
                'title'                    => $request->title,
                'program_studi'            => $request->program_studi,
                'sumber_pendanaan'         => $request->sumber_pendanaan,
                'durasi_penelitian'        => $request->durasi_penelitian,
                'nomor_registrasi'         => $request->nomor_surat,
                'ketua_penandatangan_id'   => $ketua->id,
            ]);

            $ske->update([
                'nomor_surat'    => $request->nomor_surat,
                'ketua_id'       => $ketua->id,
                'tanggal_terbit' => $request->tanggal_terbit,
            ]);
        });

        $ske->refresh()->load(['protocol.user', 'ketua']);

        try {
            $filePath = $this->skeGenerator->generate($ske);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal generate ulang dokumen SKE: ' . $e->getMessage());
        }

        DB::transaction(function () use ($ske, $filePath) {
            $ske->update([
                'file_path'               => $filePath,
                'signed_file_path'        => null,
                'status'                  => 'menunggu_konfirmasi',
                'catatan_revisi'          => null,
                'direvisi_at'             => now(),
                'dikirim_ke_peneliti_at'  => now(),
                'dikirim_ke_ketua_at'     => null,
                'ditandatangani_at'       => null,
                'diterbitkan_at'          => null,
            ]);

            Notification::create([
                'user_id' => $ske->protocol->user_id,
                'message' => "SKE {$ske->nomor_surat} telah diperbaiki oleh Admin. Mohon periksa kembali kebenaran data SKE.",
                'is_read' => false,
            ]);
        });

        return redirect()
            ->route('admin.ethical-clearance.index')
            ->with('success', "SKE {$ske->nomor_surat} berhasil diperbaiki dan dikirim kembali ke peneliti untuk konfirmasi ulang.");
    }
}