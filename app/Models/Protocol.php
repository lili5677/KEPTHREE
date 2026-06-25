<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Protocol extends Model
{
    protected $fillable = [
        'user_id',
        'sekretariat_id',
        'ketua_penandatangan_id',
        'title',
        'program_studi',
        'sumber_pendanaan',
        'durasi_penelitian',
        'ringkasan_penelitian',
        'status',
        'nomor_registrasi',
        'is_confirmed_peneliti',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at'          => 'datetime',
        'is_confirmed_peneliti' => 'boolean',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    /** Peneliti pemilik protokol */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Sekretariat yang di-assign */
    public function sekretariat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sekretariat_id');
    }

    /** Ketua penandatangan */
    public function ketuaPenandatangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_penandatangan_id');
    }

    /** Dokumen-dokumen yang diunggah peneliti */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /** Verifikasi oleh sekretariat */
    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    /** SKE yang terkait protokol ini (1 protokol = 1 SKE) */
    public function skeDocument(): HasOne
    {
        return $this->hasOne(SkeDocument::class);
    }

    // ─── Helper status ──────────────────────────────────────────────

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new_proposal'         => 'New Proposal',
            'waiting_verification' => 'Waiting Verification',
            'under_review'         => 'Under Review',
            'revision_required'    => 'Revision Required',
            'approved'             => 'Approved',
            'rejected'             => 'Rejected',
            default                => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'new_proposal'         => 'amber',
            'waiting_verification' => 'blue',
            'under_review'         => 'indigo',
            'revision_required'    => 'orange',
            'approved'             => 'green',
            'rejected'             => 'red',
            default                => 'slate',
        };
    }

    // ─── Helper tanggal (dipakai SkeGeneratorService) ───────────────

    /** Tanggal mulai penelitian (diambil dari submitted_at) */
    public function tanggalMulai()
    {
        return $this->submitted_at ?? $this->created_at;
    }

    /** Tanggal selesai penelitian (submitted_at + durasi_penelitian bulan) */
    public function tanggalSelesai()
    {
        return $this->tanggalMulai()?->copy()->addMonths($this->durasi_penelitian);
    }
}