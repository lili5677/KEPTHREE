<?php

namespace App\Services;

use App\Models\SkeDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SkeGeneratorService
{
    /**
     * Nama bulan dalam Bahasa Indonesia untuk format tanggal surat.
     */
    private function formatTanggalIndo(?Carbon $date): string
    {
        if (!$date) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $date->day . ' ' . $bulan[$date->month] . ' ' . $date->year;
    }

    /**
     * Generate dokumen SKE dalam bentuk PDF dari blade view.
     * Mengembalikan path file (relatif terhadap storage/app/public) hasil generate.
     *
     * @param SkeDocument $ske
     * @param string|null $signaturePath path absolut gambar tanda tangan (jika sudah ada / sedang proses TTD)
     */
    public function generate(SkeDocument $ske, ?string $signaturePath = null): string
    {
        $protocol = $ske->protocol()->with('user')->first();

        $signatureBase64 = null;
        if ($signaturePath && file_exists($signaturePath)) {
            $signatureBase64 = base64_encode(file_get_contents($signaturePath));
        }

        $data = [
            'ske'              => $ske,
            'protocol'         => $protocol,
            'tanggalMulai'     => $this->formatTanggalIndo($protocol->tanggalMulai()),
            'tanggalSelesai'   => $this->formatTanggalIndo($protocol->tanggalSelesai()),
            'tanggalTerbit'    => $this->formatTanggalIndo(
                $ske->tanggal_terbit ? Carbon::parse($ske->tanggal_terbit) : now()
            ),
            'signatureBase64'  => $signatureBase64,
        ];

        $pdf = Pdf::loadView('admin.ethical-clearance.ske-pdf', $data)
            ->setPaper('a4', 'portrait');

        $fileName = 'ske/' . str_replace(['/', ' '], '_', $ske->nomor_surat) . '_' . uniqid() . '.pdf';
        $fullPath = Storage::disk('public')->path($fileName);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $pdf->output());

        return $fileName;
    }

    /**
     * Generate ulang dokumen SKE dengan tanda tangan ketua terlampir.
     * Dipanggil saat ketua menandatangani SKE secara digital.
     *
     * @param SkeDocument $ske
     * @param string $signatureImagePath path absolut gambar tanda tangan (png/jpg)
     */
    public function attachSignature(SkeDocument $ske, string $signatureImagePath): string
    {
        if (!file_exists($signatureImagePath)) {
            throw new \RuntimeException('File tanda tangan tidak ditemukan.');
        }

        // Generate ulang dokumen lengkap dengan tanda tangan terisi
        $signedFileName = $this->generate($ske, $signatureImagePath);

        // Rename agar jelas ini versi signed (opsional, tapi membantu penamaan)
        $renamedPath = str_replace('.pdf', '', $signedFileName) . '_signed.pdf';

        Storage::disk('public')->move($signedFileName, $renamedPath);

        return $renamedPath;
    }
}