<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'file_nota',
        'original_name',
    ];

    /* ===== Relasi ===== */

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /* ===== Accessor ===== */

    /** URL publik file lampiran */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_nota);
    }

    /** Apakah file adalah PDF? */
    public function getIsPdfAttribute(): bool
    {
        return str_ends_with(strtolower($this->file_nota), '.pdf');
    }

    /** Apakah file adalah gambar? */
    public function getIsImageAttribute(): bool
    {
        $ext = strtolower(pathinfo($this->file_nota, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
    }
}
