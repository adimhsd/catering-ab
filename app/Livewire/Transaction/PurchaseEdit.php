<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Edit Transaksi Pembelian')]
class PurchaseEdit extends Component
{
    use WithFileUploads;

    public Purchase $purchase;

    public string $supplier_id = '';
    public string $tanggal = '';
    public string $catatan = '';
    public array $items = [];
    public $fileNota = null;
    public float $totalKeseluruhan = 0;

    public function mount(Purchase $purchase): void
    {
        // Admin dapur hanya bisa edit miliknya sendiri
        if ($purchase->user_id !== auth()->id()) {
            abort(403, 'Anda tidak dapat mengedit transaksi ini.');
        }

        $this->purchase = $purchase;
        $this->supplier_id = $purchase->supplier_id;
        $this->tanggal = $purchase->tanggal->format('Y-m-d');
        $this->catatan = $purchase->catatan ?? '';

        // Load detail items yang sudah ada
        $this->items = $purchase->details->map(fn($d) => [
            'product_id' => $d->product_id,
            'qty'        => $d->qty,
            'harga'      => $d->harga,
            'subtotal'   => $d->subtotal,
        ])->toArray();

        $this->hitungTotal();
    }

    public function tambahBaris(): void
    {
        $this->items[] = ['product_id' => '', 'qty' => '', 'harga' => '', 'subtotal' => 0];
    }

    public function hapusBaris(int $index): void
    {
        if (count($this->items) <= 1) return;
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->hitungTotal();
    }

    public function updatedItems(): void
    {
        $this->hitungTotal();
    }

    private function hitungTotal(): void
    {
        $total = 0;
        foreach ($this->items as $i => $item) {
            $qty   = (float) ($item['qty'] ?? 0);
            $harga = (float) ($item['harga'] ?? 0);
            $subtotal = $qty * $harga;
            $this->items[$i]['subtotal'] = $subtotal;
            $total += $subtotal;
        }
        $this->totalKeseluruhan = $total;
    }

    public function perbarui(PurchaseService $service): void
    {
        $this->validate([
            'supplier_id'            => 'required|exists:suppliers,id',
            'tanggal'                => 'required|date',
            'catatan'                => 'nullable|string|max:500',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.qty'            => 'required|numeric|min:0.01',
            'items.*.harga'          => 'required|numeric|min:0',
            'fileNota'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $this->hitungTotal();

        $service->update(
            purchase: $this->purchase,
            header: [
                'supplier_id' => $this->supplier_id,
                'tanggal'     => $this->tanggal,
                'catatan'     => $this->catatan,
            ],
            items: $this->items,
            fileNota: $this->fileNota,
        );

        session()->flash('success', 'Transaksi berhasil diperbarui.');
        $this->redirect(route('purchases.show', $this->purchase), navigate: true);
    }

    public function hapusLampiran(int $attachmentId): void
    {
        $attachment = $this->purchase->attachments()->findOrFail($attachmentId);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_nota);
        $attachment->delete();
        $this->purchase->refresh();
    }

    public function render(): \Illuminate\View\View
    {
        $suppliers = Supplier::aktif()->orderBy('nama_supplier')->get();
        $products  = Product::aktif()->with(['unit', 'category'])->orderBy('nama_produk')->get();

        return view('livewire.transaction.purchase-edit',
            compact('suppliers', 'products')
        );
    }
}
