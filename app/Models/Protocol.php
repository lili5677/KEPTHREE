<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Protocol extends Model
{
    use HasFactory;

    protected $table = 'protocols';

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
        'is_confirmed_peneliti' => 'boolean',
        'submitted_at'          => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sekretariat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sekretariat_id');
    }

    public function ketuaPenandatangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_penandatangan_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function dokumenWajib(): HasMany
    {
        return $this->hasMany(Document::class)
                    ->whereIn('type', ['formulir_pengajuan', 'formulir_ringkasan']);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function skeDocument(): HasOne
    {
        return $this->hasOne(SkeDocument::class);
    }

    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class, 'protocol_id');
    }

    public function sekretariatDecision(): HasOne
    {
        return $this->hasOne(SekretariatDecision::class, 'protocol_id');
    }

    // ── Helper ──────────────────────────────────────────

    public static function generateNomorRegistrasi(): string
    {
        $count = self::count() + 1;
        return 'PRO-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'new_proposal'          => 'bg-secondary',
            'waiting_verification'  => 'bg-info text-dark',
            'under_review'          => 'bg-warning text-dark',
            'revision_required'     => 'bg-danger',
            'approved'              => 'bg-success',
            'rejected'              => 'bg-dark',
            default                 => 'bg-secondary',
        };
    }

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

    public function tanggalMulai()
    {
        return $this->submitted_at ?? $this->created_at;
    }

    public function tanggalSelesai()
    {
        return $this->tanggalMulai()?->copy()->addMonths($this->durasi_penelitian);
    }
}