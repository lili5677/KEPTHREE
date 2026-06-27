<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function dokumenWajib()
    {
        return $this->hasMany(Document::class)
                    ->whereIn('type', ['formulir_pengajuan', 'formulir_ringkasan']);
    }

    // Relasi ke tabel reviews (untuk assign reviewer)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relasi ke tabel protocol_reviewers (assignment reviewer per babak)
    public function protocolReviewers()
    {
        return $this->hasMany(\App\Models\ProtocolReviewer::class, 'protocol_id');
    }

    // ── Helper ──────────────────────────────────────────

    /**
     * Generate nomor registrasi unik: PRO-001, PRO-002, ...
     */
    public static function generateNomorRegistrasi(): string
    {
        $count = self::count() + 1;
        return 'PRO-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Class badge Bootstrap sesuai status
     */
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

    public function verification()
    {
        return $this->hasOne(\App\Models\Verification::class, 'protocol_id');
    }
    public function sekretariatDecision()
    {
        return $this->hasOne(\App\Models\SekretariatDecision::class, 'protocol_id');
    }

    // Histori seluruh keputusan sekretariat (semua babak), terbaru duluan
    public function sekretariatDecisions()
    {
        return $this->hasMany(\App\Models\SekretariatDecision::class, 'protocol_id')->orderByDesc('round');
    }

    public function latestSekretariatDecision()
    {
        return $this->hasOne(\App\Models\SekretariatDecision::class, 'protocol_id')->latestOfMany('round');
    }

    // Relasi ke tabel revisions (riwayat upload revisi oleh Peneliti)
    public function revisions()
    {
        return $this->hasMany(\App\Models\Revision::class, 'protocol_id');
    }

    public function latestRevision()
    {
        return $this->hasOne(\App\Models\Revision::class, 'protocol_id')->latestOfMany();
    }

    // Apakah Peneliti sudah mengunggah revisi terbaru setelah Sekretaris menandai dokumen tidak lengkap?
    public function getSudahKirimRevisiMenungguSekretarisAttribute(): bool
    {
        if ($this->status !== 'revision_required') {
            return false;
        }

        $verifiedAt = $this->verification?->verified_at;

        return $this->revisions
            ->when($verifiedAt, fn($collection) => $collection->where('created_at', '>', $verifiedAt))
            ->isNotEmpty();
    }
}