<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'protocol_id',
        'type',
        'name',
        'file_path',
    ];

    // Nilai ENUM yang valid sesuai DB
    const TYPE_FORMULIR_PENGAJUAN = 'formulir_pengajuan';
    const TYPE_FORMULIR_RINGKASAN = 'formulir_ringkasan';
    const TYPE_PENDUKUNG          = 'pendukung';

    // ── Relasi ──────────────────────────────────────────

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }

    // ── Helper ──────────────────────────────────────────

    public function getLabelAttribute(): string
    {
        return match($this->type) {
            'formulir_pengajuan' => 'Formulir Pengajuan Telaah Etik Baru',
            'formulir_ringkasan' => 'Formulir Ringkasan Protokol Penelitian',
            'pendukung'          => 'Dokumen Pendukung',
            default              => $this->type,
        };
    }
}