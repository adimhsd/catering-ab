<?php

namespace App\Livewire\MasterData;

use App\Models\ProductCategory;
use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kategori & Satuan')]
class CategoryUnitManager extends Component
{
    // Kategori
    public string $namaKategori = '';
    public ?int $editKategoriId = null;
    public bool $showKategoriModal = false;
    public bool $showHapusKategoriConfirm = false;
    public ?int $hapusKategoriId = null;

    // Satuan
    public string $namaSatuan = '';
    public ?int $editSatuanId = null;
    public bool $showSatuanModal = false;
    public bool $showHapusSatuanConfirm = false;
    public ?int $hapusSatuanId = null;

    /* ===== KATEGORI ===== */

    public function buatKategori(): void
    {
        $this->editKategoriId = null;
        $this->namaKategori = '';
        $this->resetValidation();
        $this->showKategoriModal = true;
    }

    public function editKategori(int $id): void
    {
        $cat = ProductCategory::findOrFail($id);
        $this->editKategoriId = $cat->id;
        $this->namaKategori = $cat->nama_kategori;
        $this->resetValidation();
        $this->showKategoriModal = true;
    }

    public function simpanKategori(): void
    {
        $this->validate([
            'namaKategori' => 'required|string|max:100|unique:product_categories,nama_kategori,' . ($this->editKategoriId ?? 'NULL'),
        ], [
            'namaKategori.required' => 'Nama kategori wajib diisi.',
            'namaKategori.unique'   => 'Kategori dengan nama ini sudah ada.',
        ]);

        if ($this->editKategoriId) {
            ProductCategory::findOrFail($this->editKategoriId)->update(['nama_kategori' => $this->namaKategori]);
            session()->flash('success', 'Kategori berhasil diperbarui.');
        } else {
            ProductCategory::create(['nama_kategori' => $this->namaKategori]);
            session()->flash('success', 'Kategori baru berhasil ditambahkan.');
        }

        $this->showKategoriModal = false;
        $this->namaKategori = '';
    }

    public function konfirmasiHapusKategori(int $id): void
    {
        $this->hapusKategoriId = $id;
        $this->showHapusKategoriConfirm = true;
    }

    public function hapusKategori(): void
    {
        $cat = ProductCategory::findOrFail($this->hapusKategoriId);

        if ($cat->products()->exists()) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh produk.');
            $this->showHapusKategoriConfirm = false;
            return;
        }

        $cat->delete();
        session()->flash('success', 'Kategori berhasil dihapus.');
        $this->showHapusKategoriConfirm = false;
        $this->hapusKategoriId = null;
    }

    /* ===== SATUAN ===== */

    public function buatSatuan(): void
    {
        $this->editSatuanId = null;
        $this->namaSatuan = '';
        $this->resetValidation();
        $this->showSatuanModal = true;
    }

    public function editSatuan(int $id): void
    {
        $unit = Unit::findOrFail($id);
        $this->editSatuanId = $unit->id;
        $this->namaSatuan = $unit->nama_satuan;
        $this->resetValidation();
        $this->showSatuanModal = true;
    }

    public function simpanSatuan(): void
    {
        $this->validate([
            'namaSatuan' => 'required|string|max:50|unique:units,nama_satuan,' . ($this->editSatuanId ?? 'NULL'),
        ], [
            'namaSatuan.required' => 'Nama satuan wajib diisi.',
            'namaSatuan.unique'   => 'Satuan dengan nama ini sudah ada.',
        ]);

        if ($this->editSatuanId) {
            Unit::findOrFail($this->editSatuanId)->update(['nama_satuan' => $this->namaSatuan]);
            session()->flash('success', 'Satuan berhasil diperbarui.');
        } else {
            Unit::create(['nama_satuan' => $this->namaSatuan]);
            session()->flash('success', 'Satuan baru berhasil ditambahkan.');
        }

        $this->showSatuanModal = false;
        $this->namaSatuan = '';
    }

    public function konfirmasiHapusSatuan(int $id): void
    {
        $this->hapusSatuanId = $id;
        $this->showHapusSatuanConfirm = true;
    }

    public function hapusSatuan(): void
    {
        $unit = Unit::findOrFail($this->hapusSatuanId);

        if ($unit->products()->exists()) {
            session()->flash('error', 'Satuan tidak bisa dihapus karena masih digunakan oleh produk.');
            $this->showHapusSatuanConfirm = false;
            return;
        }

        $unit->delete();
        session()->flash('success', 'Satuan berhasil dihapus.');
        $this->showHapusSatuanConfirm = false;
        $this->hapusSatuanId = null;
    }

    public function render(): \Illuminate\View\View
    {
        $categories = ProductCategory::withCount('products')->orderBy('nama_kategori')->get();
        $units = Unit::withCount('products')->orderBy('nama_satuan')->get();

        return view('livewire.master-data.category-unit-manager', compact('categories', 'units'));
    }
}
