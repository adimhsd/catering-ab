<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_supplier',
        'pic',
        'wa',
        'alamat',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /* ===== Scopes ===== */

    /** Hanya supplier yang aktif (dipakai di dropdown transaksi) */
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    /* ===== Relasi ===== */

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /* ===== Accessor ===== */

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Aktif' : 'Nonaktif';
    }
}
