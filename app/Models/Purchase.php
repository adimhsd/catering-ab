<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_transaksi',
        'supplier_id',
        'user_id',
        'tanggal',
        'total',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total'   => 'decimal:2',
    ];

    /* ===== Relasi ===== */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detail item transaksi (cascade delete diset di migration).
     */
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Lampiran nota foto (cascade delete diset di migration).
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    /* ===== Accessor ===== */

    /** Format total ke rupiah */
    public function getTotalRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /** Format tanggal ke bahasa Indonesia */
    public function getTanggalFormatAttribute(): string
    {
        return $this->tanggal->translatedFormat('d F Y');
    }
}
