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

    // ── Relasi User ──────────────────────────────────────────

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

    // ── Relasi Dokumen ───────────────────────────────────────

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function dokumenWajib(): HasMany
    {
        return $this->hasMany(Document::class)
                    ->whereIn('type', ['formulir_pengajuan', 'formulir_ringkasan']);
    }

    public function skeDocument(): HasOne
    {
        return $this->hasOne(SkeDocument::class);
    }

    // ── Relasi Verifikasi ────────────────────────────────────

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class, 'protocol_id');
    }

    // ── Relasi Review ────────────────────────────────────────

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function protocolReviewers(): HasMany
    {
        return $this->hasMany(ProtocolReviewer::class, 'protocol_id');
    }

    // ── Relasi Keputusan Sekretariat ─────────────────────────

    public function sekretariatDecision(): HasOne
    {
        return $this->hasOne(SekretariatDecision::class, 'protocol_id');
    }

    public function sekretariatDecisions(): HasMany
    {
        return $this->hasMany(SekretariatDecision::class, 'protocol_id')
                    ->orderByDesc('round');
    }

    public function latestSekretariatDecision(): HasOne
    {
        return $this->hasOne(SekretariatDecision::class, 'protocol_id')
                    ->latestOfMany('round');
    }

    // ── Relasi Revisi ────────────────────────────────────────

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class, 'protocol_id');
    }

    public function latestRevision(): HasOne
    {
        return $this->hasOne(Revision::class, 'protocol_id')
                    ->latestOfMany();
    }

    // ── Helper ───────────────────────────────────────────────

    public static function generateNomorRegistrasi(): string
    {
        $count = self::count() + 1;

        return 'PRO-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new_proposal'         => 'bg-secondary',
            'waiting_verification' => 'bg-info text-dark',
            'under_review',
            'on_review'            => 'bg-warning text-dark',
            'revision_required'    => 'bg-danger',
            'approved'             => 'bg-success',
            'rejected'             => 'bg-dark',
            default                => 'bg-secondary',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new_proposal'         => 'New Proposal',
            'waiting_verification' => 'Waiting Verification',
            'under_review',
            'on_review'            => 'Under Review',
            'revision_required'    => 'Revision Required',
            'approved'             => 'Approved',
            'rejected'             => 'Rejected',
            default                => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'new_proposal'         => 'amber',
            'waiting_verification' => 'blue',
            'under_review',
            'on_review'            => 'indigo',
            'revision_required'    => 'orange',
            'approved'             => 'green',
            'rejected'             => 'red',
            default                => 'slate',
        };
    }

    // ── Helper Tanggal ───────────────────────────────────────

    /** Tanggal mulai penelitian */
    public function tanggalMulai()
    {
        return $this->submitted_at ?? $this->created_at;
    }

    /** Tanggal selesai penelitian (null-safe) */
    public function tanggalSelesai()
    {
        if (!$this->tanggalMulai() || !$this->durasi_penelitian) {
            return null;
        }

        return $this->tanggalMulai()->copy()->addMonths($this->durasi_penelitian);
    }

    /**
     * True jika peneliti sudah kirim revisi terbaru tapi sekretaris
     * belum sempat verifikasi ulang revisi tersebut.
     */
    public function getSudahKirimRevisiMenungguSekretarisAttribute(): bool
    {
        if ($this->status !== 'revision_required') {
            return false;
        }

        $verifiedAt = $this->verification?->verified_at;

        return $this->revisions
            ->when($verifiedAt, fn ($collection) => $collection->where('created_at', '>', $verifiedAt))
            ->isNotEmpty();
    }
}