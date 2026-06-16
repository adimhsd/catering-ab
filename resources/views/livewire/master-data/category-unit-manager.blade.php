<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Kategori & Satuan</h1>
            <p class="page-subtitle">Kelola referensi kategori produk dan satuan ukuran</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ===== PANEL KATEGORI ===== --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="font-semibold text-gray-800">Kategori Produk</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $categories->count() }} kategori terdaftar</p>
                </div>
                <button wire:click="buatKategori" id="btn-tambah-kategori" class="btn-primary btn-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah
                </button>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($categories as $cat)
                <div wire:key="cat-{{ $cat->id }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 group">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $cat->nama_kategori }}</p>
                        <p class="text-xs text-gray-400">{{ $cat->products_count }} produk</p>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button wire:click="editKategori({{ $cat->id }})" title="Edit"
                            class="btn-icon text-blue-600 hover:bg-blue-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="konfirmasiHapusKategori({{ $cat->id }})" title="Hapus"
                            class="btn-icon text-red-600 hover:bg-red-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-gray-400 text-sm">Belum ada kategori</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ===== PANEL SATUAN ===== --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="font-semibold text-gray-800">Satuan Ukuran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $units->count() }} satuan terdaftar</p>
                </div>
                <button wire:click="buatSatuan" id="btn-tambah-satuan" class="btn-primary btn-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah
                </button>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($units as $unit)
                <div wire:key="unit-{{ $unit->id }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 group">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $unit->nama_satuan }}</p>
                        <p class="text-xs text-gray-400">{{ $unit->products_count }} produk</p>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button wire:click="editSatuan({{ $unit->id }})" title="Edit"
                            class="btn-icon text-blue-600 hover:bg-blue-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="konfirmasiHapusSatuan({{ $unit->id }})" title="Hapus"
                            class="btn-icon text-red-600 hover:bg-red-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-gray-400 text-sm">Belum ada satuan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal Kategori --}}
    @if($showKategoriModal)
    <div class="modal-overlay" wire:click.self="$set('showKategoriModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-fade-up">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-800">{{ $editKategoriId ? 'Edit' : 'Tambah' }} Kategori</h3>
                <button wire:click="$set('showKategoriModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                <input wire:model="namaKategori" type="text" class="form-input"
                       placeholder="cth: Sayuran, Daging, Bumbu..." id="input-nama-kategori"
                       wire:keydown.enter="simpanKategori">
                @error('namaKategori') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showKategoriModal', false)" class="btn-secondary">Batal</button>
                <button wire:click="simpanKategori" class="btn-primary" id="btn-simpan-kategori">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Satuan --}}
    @if($showSatuanModal)
    <div class="modal-overlay" wire:click.self="$set('showSatuanModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-fade-up">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-800">{{ $editSatuanId ? 'Edit' : 'Tambah' }} Satuan</h3>
                <button wire:click="$set('showSatuanModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <label class="form-label">Nama Satuan <span class="text-red-500">*</span></label>
                <input wire:model="namaSatuan" type="text" class="form-input"
                       placeholder="cth: Kg, Liter, Pack..." id="input-nama-satuan"
                       wire:keydown.enter="simpanSatuan">
                @error('namaSatuan') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showSatuanModal', false)" class="btn-secondary">Batal</button>
                <button wire:click="simpanSatuan" class="btn-primary" id="btn-simpan-satuan">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Konfirmasi Hapus Kategori --}}
    @if($showHapusKategoriConfirm)
    <div class="modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 animate-fade-up">
            <p class="font-semibold text-gray-900 mb-2">Hapus Kategori?</p>
            <p class="text-sm text-gray-500 mb-4">Kategori yang masih digunakan produk tidak bisa dihapus.</p>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('showHapusKategoriConfirm', false)" class="btn-secondary btn-sm">Batal</button>
                <button wire:click="hapusKategori" class="btn-danger btn-sm">Hapus</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Konfirmasi Hapus Satuan --}}
    @if($showHapusSatuanConfirm)
    <div class="modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 animate-fade-up">
            <p class="font-semibold text-gray-900 mb-2">Hapus Satuan?</p>
            <p class="text-sm text-gray-500 mb-4">Satuan yang masih digunakan produk tidak bisa dihapus.</p>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('showHapusSatuanConfirm', false)" class="btn-secondary btn-sm">Batal</button>
                <button wire:click="hapusSatuan" class="btn-danger btn-sm">Hapus</button>
            </div>
        </div>
    </div>
    @endif
</div>
