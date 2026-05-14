<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'description',
        'versi',
        'file_path',
        'uploaded_by',
        'is_active',
        'replaced_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'replaced_at' => 'datetime',
    ];

    /* ── Relasi ── */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /* ── Scope ── */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRiwayat($query)
    {
        return $query->where('is_active', false)->orderByDesc('replaced_at');
    }
}