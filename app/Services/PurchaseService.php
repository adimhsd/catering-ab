<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseService
{
    /**
     * Generate nomor transaksi otomatis dengan format PB-YYYYMMDD-XXX.
     * XXX = counter urut berdasarkan jumlah transaksi di tanggal yang sama.
     *
     * Contoh: PB-20240616-001, PB-20240616-002, dst.
     *
     * @param  \Carbon\Carbon|null  $tanggal  Tanggal transaksi (default: hari ini)
     */
    public function generateNomorTransaksi(?Carbon $tanggal = null): string
    {
        $tanggal = $tanggal ?? Carbon::today();
        $prefix  = 'PB-' . $tanggal->format('Ymd') . '-';

        // Hitung jumlah transaksi yang sudah ada di tanggal yang sama
        $count = Purchase::where('nomor_transaksi', 'like', $prefix . '%')->count();

        // Format urutan 3 digit, mulai dari 001
        $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $urutan;
    }

    /**
     * Hitung total transaksi dari kumpulan item detail.
     * Total = jumlah semua subtotal (subtotal = qty × harga).
     *
     * @param  array  $items  Array of ['qty' => float, 'harga' => float]
     * @return float
     */
    public function hitungTotal(array $items): float
    {
        return collect($items)->sum(function ($item) {
            return (float) ($item['qty'] ?? 0) * (float) ($item['harga'] ?? 0);
        });
    }

    /**
     * Hitung subtotal untuk satu baris item.
     *
     * @param  float  $qty    Jumlah/kuantitas
     * @param  float  $harga  Harga satuan
     * @return float
     */
    public function hitungSubtotal(float $qty, float $harga): float
    {
        return $qty * $harga;
    }

    /**
     * Simpan transaksi pembelian beserta detail dan lampiran nota.
     * Seluruh operasi dibungkus dalam DB transaction untuk konsistensi data.
     *
     * @param  array              $header       Data header transaksi
     * @param  array              $items        Array detail item transaksi
     * @param  UploadedFile|null  $fileNota     File lampiran nota (opsional)
     * @return Purchase
     */
    public function simpan(array $header, array $items, ?UploadedFile $fileNota = null): Purchase
    {
        return DB::transaction(function () use ($header, $items, $fileNota) {
            // Hitung total dari semua item detail
            $total = $this->hitungTotal($items);

            // Buat header transaksi
            $purchase = Purchase::create([
                'nomor_transaksi' => $this->generateNomorTransaksi(
                    Carbon::parse($header['tanggal'])
                ),
                'supplier_id' => $header['supplier_id'],
                'user_id'     => auth()->id(),
                'tanggal'     => $header['tanggal'],
                'total'       => $total,
                'catatan'     => $header['catatan'] ?? null,
            ]);

            // Simpan setiap baris detail item
            foreach ($items as $item) {
                $subtotal = $this->hitungSubtotal(
                    (float) $item['qty'],
                    (float) $item['harga']
                );

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $item['product_id'],
                    'qty'         => $item['qty'],
                    'harga'       => $item['harga'],
                    'subtotal'    => $subtotal,
                ]);
            }

            // Upload lampiran nota jika ada
            if ($fileNota) {
                $path = $fileNota->store('nota', 'public');

                $purchase->attachments()->create([
                    'file_nota'     => $path,
                    'original_name' => $fileNota->getClientOriginalName(),
                ]);
            }

            return $purchase->load(['details.product', 'supplier', 'attachments']);
        });
    }

    /**
     * Update transaksi pembelian yang sudah ada.
     * Hapus semua detail lama, ganti dengan detail baru, recalculate total.
     *
     * @param  Purchase           $purchase  Transaksi yang akan diupdate
     * @param  array              $header    Data header baru
     * @param  array              $items     Detail item baru
     * @param  UploadedFile|null  $fileNota  File nota baru (opsional, null = tidak ganti)
     * @return Purchase
     */
    public function update(Purchase $purchase, array $header, array $items, ?UploadedFile $fileNota = null): Purchase
    {
        return DB::transaction(function () use ($purchase, $header, $items, $fileNota) {
            // Recalculate total dari item baru
            $total = $this->hitungTotal($items);

            // Update header
            $purchase->update([
                'supplier_id' => $header['supplier_id'],
                'tanggal'     => $header['tanggal'],
                'total'       => $total,
                'catatan'     => $header['catatan'] ?? null,
            ]);

            // Hapus detail lama (cascade tidak berlaku untuk update — hapus manual)
            $purchase->details()->delete();

            // Insert detail baru
            foreach ($items as $item) {
                $subtotal = $this->hitungSubtotal(
                    (float) $item['qty'],
                    (float) $item['harga']
                );

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $item['product_id'],
                    'qty'         => $item['qty'],
                    'harga'       => $item['harga'],
                    'subtotal'    => $subtotal,
                ]);
            }

            // Tambah lampiran nota baru jika ada (tidak menghapus yang lama)
            if ($fileNota) {
                $path = $fileNota->store('nota', 'public');

                $purchase->attachments()->create([
                    'file_nota'     => $path,
                    'original_name' => $fileNota->getClientOriginalName(),
                ]);
            }

            return $purchase->load(['details.product', 'supplier', 'attachments']);
        });
    }

    /**
     * Hapus transaksi beserta semua detail dan lampiran (file fisik juga dihapus).
     */
    public function hapus(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // Hapus file fisik lampiran dari storage
            foreach ($purchase->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_nota);
            }

            // Hard delete (cascade ke details & attachments via FK di migration)
            $purchase->delete();
        });
    }
}
