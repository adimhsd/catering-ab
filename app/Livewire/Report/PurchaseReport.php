<?php

namespace App\Livewire\Report;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Laporan Pembelian')]
class PurchaseReport extends Component
{
    use WithPagination;

    public string $tanggalDari = '';
    public string $tanggalSampai = '';
    public string $filterSupplier = '';
    public string $filterProduct = '';

    public function mount(): void
    {
        // Default: bulan ini
        $this->tanggalDari   = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSampai = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingTanggalDari(): void { $this->resetPage(); }
    public function updatingTanggalSampai(): void { $this->resetPage(); }
    public function updatingFilterSupplier(): void { $this->resetPage(); }
    public function updatingFilterProduct(): void { $this->resetPage(); }

    /**
     * Build query dasar dengan semua filter yang aktif.
     */
    private function baseQuery()
    {
        return Purchase::with(['supplier', 'user', 'details.product'])
            ->when($this->tanggalDari, fn($q) => $q->where('tanggal', '>=', $this->tanggalDari))
            ->when($this->tanggalSampai, fn($q) => $q->where('tanggal', '<=', $this->tanggalSampai))
            ->when($this->filterSupplier, fn($q) => $q->where('supplier_id', $this->filterSupplier))
            ->when($this->filterProduct, fn($q) =>
                $q->whereHas('details', fn($d) => $d->where('product_id', $this->filterProduct))
            );
    }

    public function render(): \Illuminate\View\View
    {
        $purchases = $this->baseQuery()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20);

        // Ringkasan statistik untuk periode yang dipilih
        $totalTransaksi   = $this->baseQuery()->count();
        $totalPengeluaran = $this->baseQuery()->sum('total');

        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $products  = Product::orderBy('nama_produk')->get();

        return view('livewire.report.purchase-report',
            compact('purchases', 'totalTransaksi', 'totalPengeluaran', 'suppliers', 'products')
        );
    }
}
