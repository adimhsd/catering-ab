<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'category_id',
        'unit_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /* ===== Relasi ===== */

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /* ===== Scopes ===== */

    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    /* ===== Accessor ===== */

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Aktif' : 'Nonaktif';
    }
}
