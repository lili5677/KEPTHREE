<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkeDocument extends Model
{
    protected $table = 'ske_documents';

    protected $fillable = [
        'protocol_id',
        'nomor_surat',
        'ketua_id',
        'tanggal_terbit',
        'file_path',
        'signed_file_path',
        'status',
        'catatan_revisi',
        'dikirim_ke_peneliti_at',
        'direvisi_at',
        'dikirim_ke_ketua_at',
        'ditandatangani_at',
        'diterbitkan_at',
    ];

    protected $casts = [
        'tanggal_terbit'         => 'date',
        'dikirim_ke_peneliti_at' => 'datetime',
        'direvisi_at'            => 'datetime',
        'dikirim_ke_ketua_at'    => 'datetime',
        'ditandatangani_at'      => 'datetime',
        'diterbitkan_at'         => 'datetime',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }

    public function ketua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

    // ─── Helper ────────────────────────────────────────────────────

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'                => 'Draft',
            'menunggu_konfirmasi'  => 'Menunggu Konfirmasi Peneliti',
            'revisi'               => 'Revisi Diminta',
            'menunggu_ttd'         => 'Menunggu TTD Ketua',
            'sudah_ttd'            => 'Sudah Ditandatangani',
            'terbit'               => 'Terbit',
            default                => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'draft'               => 'slate',
            'menunggu_konfirmasi' => 'blue',
            'revisi'              => 'orange',
            'menunggu_ttd'        => 'indigo',
            'sudah_ttd'           => 'green',
            'terbit'              => 'emerald',
            default               => 'slate',
        };
    }
}