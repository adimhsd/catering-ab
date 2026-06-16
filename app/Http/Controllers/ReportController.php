<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseExport;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Export laporan pembelian ke Excel (.xlsx).
     * Menerima parameter filter dari query string.
     */
    public function exportExcel(Request $request)
    {
        $dari     = $request->query('dari');
        $sampai   = $request->query('sampai');
        $supplier = $request->query('supplier') ? (int) $request->query('supplier') : null;
        $produk   = $request->query('produk') ? (int) $request->query('produk') : null;

        $filename = 'laporan-pembelian-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new PurchaseExport($dari, $sampai, $supplier, $produk),
            $filename
        );
    }

    /**
     * Export laporan pembelian ke PDF.
     * Menggunakan DomPDF untuk generate file.
     */
    public function exportPdf(Request $request)
    {
        $dari     = $request->query('dari');
        $sampai   = $request->query('sampai');
        $supplierId = $request->query('supplier') ? (int) $request->query('supplier') : null;
        $produkId   = $request->query('produk') ? (int) $request->query('produk') : null;

        // Build query dengan filter
        $query = Purchase::with(['supplier', 'user', 'details.product.unit'])
            ->when($dari,       fn($q) => $q->where('tanggal', '>=', $dari))
            ->when($sampai,     fn($q) => $q->where('tanggal', '<=', $sampai))
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($produkId,   fn($q) =>
                $q->whereHas('details', fn($d) => $d->where('product_id', $produkId))
            )
            ->orderByDesc('tanggal');

        $purchases        = $query->paginate(1000); // Ambil semua untuk PDF
        $totalPengeluaran = $query->sum('total');

        // Label untuk tampil di header PDF
        $supplierLabel = $supplierId ? Supplier::find($supplierId)?->nama_supplier : null;
        $produkLabel   = $produkId ? Product::find($produkId)?->nama_produk : null;

        $pdf = Pdf::loadView('exports.purchase-pdf', compact(
            'purchases', 'totalPengeluaran', 'dari', 'sampai', 'supplierLabel', 'produkLabel'
        ))->setPaper('a4', 'landscape');

        $filename = 'laporan-pembelian-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Delete transaksi via HTTP DELETE (karena Livewire component tidak bisa handle ini langsung).
     * Hanya Admin Dapur yang bisa hapus miliknya sendiri.
     */
    public function deletePurchase(Request $request, Purchase $purchase)
    {
        // Cek ownership
        if ($purchase->user_id !== auth()->id() || !auth()->user()->isAdminDapur()) {
            abort(403);
        }

        app(\App\Services\PurchaseService::class)->hapus($purchase);

        return redirect()->route('purchases.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
