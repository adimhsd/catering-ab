<?php

namespace App\Livewire\Transaction;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Riwayat Pembelian')]
class PurchaseList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterSupplier = '';
    public string $tanggalDari = '';
    public string $tanggalSampai = '';

    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterSupplier(): void { $this->resetPage(); }
    public function updatingTanggalDari(): void { $this->resetPage(); }
    public function updatingTanggalSampai(): void { $this->resetPage(); }

    public function confirmDelete(int $id): void
    {
        // Admin dapur hanya bisa hapus miliknya sendiri
        $purchase = Purchase::findOrFail($id);
        if ($purchase->user_id !== auth()->id() || !auth()->user()->isAdminDapur()) {
            abort(403);
        }
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        $purchase = Purchase::findOrFail($this->deleteId);

        if ($purchase->user_id !== auth()->id()) {
            abort(403);
        }

        app(PurchaseService::class)->hapus($purchase);
        session()->flash('success', 'Transaksi berhasil dihapus.');
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function render(): \Illuminate\View\View
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->when($this->search, fn($q) =>
                $q->where('nomor_transaksi', 'like', "%{$this->search}%")
            )
            ->when($this->filterSupplier, fn($q) =>
                $q->where('supplier_id', $this->filterSupplier)
            )
            ->when($this->tanggalDari, fn($q) =>
                $q->where('tanggal', '>=', $this->tanggalDari)
            )
            ->when($this->tanggalSampai, fn($q) =>
                $q->where('tanggal', '<=', $this->tanggalSampai)
            )
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20);

        $suppliers = Supplier::aktif()->orderBy('nama_supplier')->get();
        $totalFiltered = $purchases->total();
        $sumFiltered = Purchase::query()
            ->when($this->search, fn($q) =>
                $q->where('nomor_transaksi', 'like', "%{$this->search}%")
            )
            ->when($this->filterSupplier, fn($q) =>
                $q->where('supplier_id', $this->filterSupplier)
            )
            ->when($this->tanggalDari, fn($q) =>
                $q->where('tanggal', '>=', $this->tanggalDari)
            )
            ->when($this->tanggalSampai, fn($q) =>
                $q->where('tanggal', '<=', $this->tanggalSampai)
            )
            ->sum('total');

        return view('livewire.transaction.purchase-list',
            compact('purchases', 'suppliers', 'totalFiltered', 'sumFiltered')
        );
    }
}
