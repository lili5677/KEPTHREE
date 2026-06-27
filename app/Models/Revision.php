<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $table = 'revisions';

    protected $fillable = [
        'protocol_id',
        'catatan_revisi',
        'file_path',
        'original_filename',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }
}