<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Tambah Transaksi Pembelian')]
class PurchaseCreate extends Component
{
    use WithFileUploads;

    // Header
    public string $supplier_id = '';
    public string $tanggal = '';
    public string $catatan = '';

    // Detail items — array of ['product_id', 'qty', 'harga', 'subtotal']
    public array $items = [];

    // Lampiran
    public $fileNota = null;

    // Computed total
    public float $totalKeseluruhan = 0;

    public function mount(): void
    {
        // Default tanggal hari ini
        $this->tanggal = Carbon::today()->format('Y-m-d');

        // Mulai dengan satu baris item kosong
        $this->tambahBaris();
    }

    /**
     * Tambah baris item detail baru.
     */
    public function tambahBaris(): void
    {
        $this->items[] = [
            'product_id' => '',
            'qty'        => '',
            'harga'      => '',
            'subtotal'   => 0,
        ];
    }

    /**
     * Hapus baris item pada indeks tertentu.
     * Minimal harus ada 1 baris.
     */
    public function hapusBaris(int $index): void
    {
        if (count($this->items) <= 1) {
            return; // Minimal 1 baris
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
        $this->hitungTotal();
    }

    /**
     * Hitung ulang subtotal baris dan total keseluruhan
     * setiap kali qty atau harga berubah.
     */
    public function updatedItems(): void
    {
        $this->hitungTotal();
    }

    /**
     * Kalkulasi subtotal per baris dan total keseluruhan.
     * Dipanggil setiap kali ada perubahan pada items.
     */
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

    /**
     * Simpan transaksi ke database.
     */
    public function simpan(PurchaseService $service): void
    {
        $this->validate([
            'supplier_id'                => 'required|exists:suppliers,id',
            'tanggal'                    => 'required|date',
            'catatan'                    => 'nullable|string|max:500',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.qty'                => 'required|numeric|min:0.01',
            'items.*.harga'              => 'required|numeric|min:0',
            'fileNota'                   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'supplier_id.required'            => 'Pilih supplier.',
            'tanggal.required'                => 'Tanggal transaksi wajib diisi.',
            'items.required'                  => 'Minimal satu item detail wajib diisi.',
            'items.*.product_id.required'     => 'Pilih produk untuk setiap baris.',
            'items.*.qty.required'            => 'Jumlah qty wajib diisi.',
            'items.*.qty.min'                 => 'Qty harus lebih dari 0.',
            'items.*.harga.required'          => 'Harga satuan wajib diisi.',
            'fileNota.mimes'                  => 'File nota harus berformat JPG, PNG, atau PDF.',
            'fileNota.max'                    => 'Ukuran file nota maksimal 5MB.',
        ]);

        // Recalculate total sebelum simpan (untuk keamanan)
        $this->hitungTotal();

        $service->simpan(
            header: [
                'supplier_id' => $this->supplier_id,
                'tanggal'     => $this->tanggal,
                'catatan'     => $this->catatan,
            ],
            items: $this->items,
            fileNota: $this->fileNota,
        );

        session()->flash('success', 'Transaksi pembelian berhasil disimpan.');
        $this->redirect(route('purchases.index'), navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        $suppliers = Supplier::aktif()->orderBy('nama_supplier')->get();
        $products  = Product::aktif()->with(['unit', 'category'])->orderBy('nama_produk')->get();

        return view('livewire.transaction.purchase-create',
            compact('suppliers', 'products')
        );
    }
}
