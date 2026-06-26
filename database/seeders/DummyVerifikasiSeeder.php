<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Protocol;
use App\Models\Document;

class DummyVerifikasiSeeder extends Seeder
{
    public function run()
    {
        // Gunakan user peneliti yang sudah ada (misal id=2 dari log sebelumnya)
        // Jika tidak ada, Anda bisa mencari user dengan role peneliti, atau buat manual melalui phpMyAdmin.
        $penelitiId = 2; // Ganti dengan ID user peneliti yang valid di sistem Anda

        // Cek apakah user dengan id tersebut ada
        if (!\App\Models\User::find($penelitiId)) {
            $this->command->error('User peneliti dengan ID '.$penelitiId.' tidak ditemukan. Silakan buat user peneliti terlebih dahulu.');
            return;
        }

        // Buat proposal dengan ID 151, 154, 156
        $proposals = [
            151 => 'Studi Kesehatan Lansia',
            154 => 'Analisis Pola Makan Vegetarian',
            156 => 'Penelitian Efektivitas Obat Antiviral Baru'
        ];

        foreach ($proposals as $id => $title) {
            Protocol::updateOrCreate(
                ['id' => $id],
                [
                    'user_id' => $penelitiId,
                    'title' => $title,
                    'status' => 'new_proposal',
                    'submitted_at' => now(),
                    'program_studi' => 'Kedokteran',
                ]
            );
        }

        // Buat dokumen untuk proposal 156
        $docTypes = [
            'formulir_etik' => 'Formulir Pengajuan Telaah Etik Baru',
            'ringkasan_protokol' => 'Formulir Ringkasan Protokol Penelitian',
            'surat_pengantar' => 'Surat Pengantar',
            'proposal' => 'Proposal Penelitian',
            'icf' => 'ICF (Informed Consent Form)'
        ];

        foreach ($docTypes as $type => $label) {
            Document::updateOrCreate(
                [
                    'protocol_id' => 156,
                    'document_type' => $type,
                ],
                [
                    'file_path' => 'dummy/' . $type . '.pdf',
                    'original_name' => $label . '.pdf',
                    'uploaded_by' => $penelitiId,
                ]
            );
        }

        $this->command->info('Data dummy verifikasi berhasil dibuat.');
    }
}