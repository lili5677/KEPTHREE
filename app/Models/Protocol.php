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
}