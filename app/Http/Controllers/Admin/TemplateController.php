<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    /* ─────────────────────────────────────────
       INDEX
    ───────────────────────────────────────── */
    public function index()
    {
        return view('admin.template.index', [
            'templates' => Template::active()->latest()->get(),
            'riwayat'   => Template::riwayat()->get(),
        ]);
    }

    /* ─────────────────────────────────────────
       STORE — tambah template baru (TIDAK mengganggu template lain)
    ───────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'versi'         => 'nullable|string|max:20',
            'file_template' => 'required|file|mimes:doc,docx|max:5120',
        ]);

        $path = $request->file('file_template')
                        ->store('templates', 'public');

        Template::create([
            'name'        => $request->nama_template,
            'description' => $request->deskripsi,
            'versi'       => $request->versi ?? '1.0',
            'file_path'   => $path,
            'uploaded_by' => auth()->id(),
            'is_active'   => true,
        ]);

        return redirect()->route('admin.template.index')
            ->with('success', 'Template "' . $request->nama_template . '" berhasil ditambahkan.');
    }

    /* ─────────────────────────────────────────
       EDIT — form edit
    ───────────────────────────────────────── */
    public function edit(Template $template)
    {
        return view('admin.template.edit', compact('template'));
    }

    /* ─────────────────────────────────────────
       UPDATE — edit metadata atau ganti file
       Jika file baru diupload → snapshot versi lama disimpan
       sebagai entri riwayat, file lama dihapus dari storage.
    ───────────────────────────────────────── */
    public function update(Request $request, Template $template)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'versi'         => 'nullable|string|max:20',
            'file_template' => 'nullable|file|mimes:doc,docx|max:5120',
        ]);

        if ($request->hasFile('file_template')) {
            // Simpan snapshot versi lama sebagai riwayat
            Template::create([
                'name'        => $template->name,
                'description' => $template->description,
                'versi'       => $template->versi,
                'file_path'   => $template->file_path,
                'uploaded_by' => $template->uploaded_by,
                'is_active'   => false,
                'replaced_at' => now(),
            ]);

            // Upload file baru, hapus file lama
            Storage::disk('public')->delete($template->file_path);
            $template->file_path = $request->file('file_template')
                                           ->store('templates', 'public');
        }

        $template->name        = $request->nama_template;
        $template->description = $request->deskripsi;
        $template->versi       = $request->versi ?? $template->versi;
        $template->save();

        return redirect()->route('admin.template.index')
            ->with('success', 'Template "' . $template->name . '" berhasil diperbarui.');
    }

    /* ─────────────────────────────────────────
       DESTROY
    ───────────────────────────────────────── */
    public function destroy(Template $template)
    {
        Storage::disk('public')->delete($template->file_path);
        $nama = $template->name;
        $template->delete();

        return redirect()->route('admin.template.index')
            ->with('success', 'Template "' . $nama . '" berhasil dihapus.');
    }

    /* ─────────────────────────────────────────
       DOWNLOAD
    ───────────────────────────────────────── */
    public function download(Template $template)
    {
        if (!Storage::disk('public')->exists($template->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk('public')->download(
            $template->file_path,
            $template->name . '.docx'
        );
    }
}