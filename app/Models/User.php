<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relasi ────────────────────────────────────────────────────

    /** Protokol milik peneliti ini */
    public function protocols(): HasMany
    {
        return $this->hasMany(Protocol::class, 'user_id');
    }

    /**
     * Protokol yang ditangani user ini sebagai sekretaris.
     * Digunakan untuk menghitung workload sekretaris.
     */
    public function handledProtocols(): HasMany
    {
        return $this->hasMany(Protocol::class, 'sekretariat_id');
    }

    /** Notifikasi untuk user ini */
    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}