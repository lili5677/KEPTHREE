<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'action',
        'subject_type',
        'subject_id',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Subject polimorfik — bisa Protocol, SkeDocument, Document, dll
     * tergantung subject_type yang tercatat.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // ─── Helper tampilan ─────────────────────────────────────────────

    /**
     * Judul aktivitas yang ramah dibaca, berdasarkan kombinasi type.
     * Fallback ke ucfirst(type) jika kombinasi tidak dikenali,
     * supaya tipe baru di masa depan tidak menyebabkan tampilan kosong/error.
     */
    public function title(): string
    {
        return match ($this->type) {
            'login'      => 'Login Sistem',
            'pengajuan'  => 'Submit Pengajuan Baru',
            'upload'     => 'Upload Dokumen',
            'review'     => 'Submit Review',
            'verifikasi' => 'Verifikasi Dokumen',
            'penugasan'  => 'Penugasan',
            'revisi'     => 'Permintaan Revisi',
            'keputusan'  => 'Keputusan Sekretaris',
            default      => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Nama ikon (key) yang dipakai blade untuk memilih SVG & warna.
     * Fallback ke 'default' jika type tidak dikenali.
     */
    public function iconKey(): string
    {
        return match ($this->type) {
            'login'      => 'login',
            'pengajuan'  => 'pengajuan',
            'upload'     => 'upload',
            'review'     => 'review',
            'verifikasi' => 'verifikasi',
            'penugasan'  => 'penugasan',
            'revisi'     => 'revisi',
            'keputusan'  => 'keputusan',
            default      => 'default',
        };
    }

    /**
     * Role label dari user yang melakukan aktivitas (untuk badge kecil di log).
     */
    public function userRoleLabel(): string
    {
        $role = $this->user?->getRoleNames()->first();
        return $role ? ucfirst($role) : '-';
    }
}