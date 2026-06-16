<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Master Produk')]
class ProductManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';
    public string $filterStatus = '';

    public ?int $editId = null;
    public string $nama_produk = '';
    public string $category_id = '';
    public string $unit_id = '';
    public bool $status = true;

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editId = $product->id;
        $this->nama_produk = $product->nama_produk;
        $this->category_id = $product->category_id;
        $this->unit_id = $product->unit_id;
        $this->status = $product->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'unit_id'     => 'required|exists:units,id',
            'status'      => 'boolean',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Pilih kategori produk.',
            'unit_id.required'     => 'Pilih satuan produk.',
        ]);

        $data = [
            'nama_produk' => $this->nama_produk,
            'category_id' => $this->category_id,
            'unit_id'     => $this->unit_id,
            'status'      => $this->status,
        ];

        if ($this->editId) {
            Product::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Data produk berhasil diperbarui.');
        } else {
            Product::create($data);
            session()->flash('success', 'Produk baru berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleStatus(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => !$product->status]);
        $label = $product->fresh()->status ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Produk berhasil {$label}.");
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        $product = Product::findOrFail($this->deleteId);

        if ($product->purchaseDetails()->exists()) {
            session()->flash('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.');
            $this->showDeleteConfirm = false;
            return;
        }

        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus.');
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama_produk = '';
        $this->category_id = '';
        $this->unit_id = '';
        $this->status = true;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $products = Product::with(['category', 'unit'])
            ->when($this->search, fn($q) =>
                $q->where('nama_produk', 'like', "%{$this->search}%")
            )
            ->when($this->filterCategory, fn($q) =>
                $q->where('category_id', $this->filterCategory)
            )
            ->when($this->filterStatus !== '', fn($q) =>
                $q->where('status', $this->filterStatus === '1')
            )
            ->orderBy('nama_produk')
            ->paginate(15);

        $categories = ProductCategory::orderBy('nama_kategori')->get();
        $units = Unit::orderBy('nama_satuan')->get();

        return view('livewire.product.product-manager', compact('products', 'categories', 'units'));
    }
}
