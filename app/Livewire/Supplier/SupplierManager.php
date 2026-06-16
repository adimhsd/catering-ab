<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Master Supplier')]
class SupplierManager extends Component
{
    use WithPagination;

    // Filter & search
    public string $search = '';
    public string $filterStatus = '';

    // Form fields
    public ?int $editId = null;
    public string $nama_supplier = '';
    public string $pic = '';
    public string $wa = '';
    public string $alamat = '';
    public bool $status = true;

    // UI state
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    /**
     * Reset halaman pagination saat filter berubah.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Buka modal tambah supplier baru.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->editId = null;
        $this->showModal = true;
    }

    /**
     * Buka modal edit supplier yang sudah ada.
     */
    public function edit(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $this->editId = $supplier->id;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->pic = $supplier->pic ?? '';
        $this->wa = $supplier->wa ?? '';
        $this->alamat = $supplier->alamat ?? '';
        $this->status = $supplier->status;
        $this->showModal = true;
    }

    /**
     * Simpan supplier (create atau update).
     */
    public function save(): void
    {
        $validated = $this->validate([
            'nama_supplier' => 'required|string|max:255',
            'pic'           => 'nullable|string|max:255',
            'wa'            => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'status'        => 'boolean',
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_supplier.max'      => 'Nama supplier maksimal 255 karakter.',
        ]);

        if ($this->editId) {
            Supplier::findOrFail($this->editId)->update($validated);
            session()->flash('success', 'Data supplier berhasil diperbarui.');
        } else {
            Supplier::create($validated);
            session()->flash('success', 'Supplier baru berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Toggle status aktif/nonaktif supplier.
     */
    public function toggleStatus(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['status' => !$supplier->status]);

        $label = $supplier->fresh()->status ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Supplier berhasil {$label}.");
    }

    /**
     * Konfirmasi hapus supplier.
     */
    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    /**
     * Eksekusi hapus supplier setelah konfirmasi.
     */
    public function delete(): void
    {
        $supplier = Supplier::findOrFail($this->deleteId);

        // Cek apakah supplier punya transaksi yang terkait
        if ($supplier->purchases()->exists()) {
            session()->flash('error', 'Supplier tidak dapat dihapus karena memiliki riwayat transaksi.');
            $this->showDeleteConfirm = false;
            return;
        }

        $supplier->delete();
        session()->flash('success', 'Supplier berhasil dihapus.');
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    /**
     * Reset form ke nilai awal.
     */
    private function resetForm(): void
    {
        $this->nama_supplier = '';
        $this->pic = '';
        $this->wa = '';
        $this->alamat = '';
        $this->status = true;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $suppliers = Supplier::query()
            ->when($this->search, fn($q) =>
                $q->where('nama_supplier', 'like', "%{$this->search}%")
                  ->orWhere('pic', 'like', "%{$this->search}%")
            )
            ->when($this->filterStatus !== '', fn($q) =>
                $q->where('status', $this->filterStatus === '1')
            )
            ->orderBy('nama_supplier')
            ->paginate(15);

        return view('livewire.supplier.supplier-manager', compact('suppliers'));
    }
}
