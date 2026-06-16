<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private ?string $dari     = null,
        private ?string $sampai   = null,
        private ?int    $supplier = null,
        private ?int    $produk   = null,
    ) {}

    /**
     * Query data yang akan diekspor.
     */
    public function query()
    {
        return Purchase::with(['supplier', 'user', 'details.product'])
            ->when($this->dari,     fn($q) => $q->where('tanggal', '>=', $this->dari))
            ->when($this->sampai,   fn($q) => $q->where('tanggal', '<=', $this->sampai))
            ->when($this->supplier, fn($q) => $q->where('supplier_id', $this->supplier))
            ->when($this->produk,   fn($q) =>
                $q->whereHas('details', fn($d) => $d->where('product_id', $this->produk))
            )
            ->orderByDesc('tanggal');
    }

    /**
     * Header kolom spreadsheet.
     */
    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tanggal',
            'Supplier',
            'Produk (Detail)',
            'Qty',
            'Satuan',
            'Harga Satuan',
            'Subtotal',
            'Total Transaksi',
            'Diinput Oleh',
            'Catatan',
        ];
    }

    /**
     * Map setiap transaksi ke baris spreadsheet.
     * Jika ada lebih dari 1 item, buat baris terpisah per item.
     */
    public function map($purchase): array
    {
        $rows = [];
        $first = true;

        foreach ($purchase->details as $detail) {
            $rows[] = [
                $first ? $purchase->nomor_transaksi : '',                      // No. Transaksi
                $first ? $purchase->tanggal->format('d/m/Y') : '',            // Tanggal
                $first ? $purchase->supplier->nama_supplier : '',             // Supplier
                $detail->product->nama_produk,                                // Produk
                (float) $detail->qty,                                         // Qty
                $detail->product->unit->nama_satuan,                          // Satuan
                (float) $detail->harga,                                       // Harga Satuan
                (float) $detail->subtotal,                                    // Subtotal
                $first ? (float) $purchase->total : '',                       // Total Transaksi
                $first ? $purchase->user->nama_lengkap : '',                 // Diinput Oleh
                $first ? ($purchase->catatan ?? '') : '',                    // Catatan
            ];
            $first = false;
        }

        return $rows;
    }

    /**
     * Styling header dan kolom mata uang.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Bold header row
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F4C35']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pembelian';
    }
}
