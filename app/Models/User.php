<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nama',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ===== Helper Role ===== */

    /** Apakah user adalah Admin Dapur? */
    public function isAdminDapur(): bool
    {
        return $this->role === 'admin_dapur';
    }

    /** Apakah user adalah Kepala Divisi? */
    public function isKepalaDivisi(): bool
    {
        return $this->role === 'kepala_divisi';
    }

    /** Nama tampilan — gunakan 'nama' jika ada, fallback ke 'name' */
    public function getNamaLengkapAttribute(): string
    {
        return $this->nama ?: $this->name;
    }

    /* ===== Relasi ===== */

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
