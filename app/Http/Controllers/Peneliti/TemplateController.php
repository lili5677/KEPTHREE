<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    /* ─────────────────────────────────────────
       INDEX — Daftar template aktif untuk peneliti
    ───────────────────────────────────────── */
    public function index()
    {
        session()->forget(['pengajuan_step1', 'pengajuan_step2']); // ← tambahkan ini

        $templates = Template::active()
            ->with('uploader')
            ->latest()
            ->get();

        return view('peneliti.template', compact('templates'));
    }

    /* ─────────────────────────────────────────
       DOWNLOAD — Unduh file template
    ───────────────────────────────────────── */
    public function download(Template $template)
    {
        // Hanya template aktif yang bisa diunduh peneliti
        if (!$template->is_active) {
            abort(403, 'Template ini sudah tidak aktif.');
        }

        if (!Storage::disk('public')->exists($template->file_path)) {
            return back()->with('error', 'File template tidak ditemukan. Silakan hubungi administrator.');
        }

        return Storage::disk('public')->download(
            $template->file_path,
            $template->name . '.docx'
        );
    }
}