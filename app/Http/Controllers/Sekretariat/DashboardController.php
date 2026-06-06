<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Data dummy untuk tampilan
        $menungguVerifikasi = 5;
        $sedangOnReview = 8;
        $perluKeputusan = 3;
        $selesaiBulanIni = 12;

        $prioritas = collect([
            (object) [
                'id' => 156,
                'title' => 'Penelitian Efektivitas Obat Antiviral',
                'protocol_number' => 'PRO-156',
                'action_label' => 'Verifikasi Dokumen',
                'deadline_display' => 'Hari ini'
            ],
            (object) [
                'id' => 154,
                'title' => 'Analisis Pola Makan Vegetarian',
                'protocol_number' => 'PRO-154',
                'action_label' => 'Secretary Decision',
                'deadline_display' => 'Besok'
            ],
            (object) [
                'id' => 151,
                'title' => 'Studi Kesehatan Lansia',
                'protocol_number' => 'PRO-151',
                'action_label' => 'Assign Reviewer',
                'deadline_display' => '2 hari lagi'
            ]
        ]);

        $reviewProgress = collect([
            (object) [
                'id' => 150,
                'judul' => 'Penelitian Nutrisi Anak',
                'progress' => '2/3',
                'status_text' => '1 reviewer belum selesai'
            ],
            (object) [
                'id' => 149,
                'judul' => 'Studi HIV Prevention',
                'progress' => '3/5',
                'status_text' => '2 reviewer belum selesai'
            ],
            (object) [
                'id' => 148,
                'judul' => 'Analisis TB Treatment',
                'progress' => '3/3',
                'status_text' => 'Review lengkap - siap keputusan'
            ]
        ]);

        return view('dashboard.sekretariat', compact(
            'menungguVerifikasi',
            'sedangOnReview',
            'perluKeputusan',
            'selesaiBulanIni',
            'prioritas',
            'reviewProgress'
        ));
    }
}