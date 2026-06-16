<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Master Produk</h1>
            <p class="page-subtitle">Kelola data bahan makanan beserta kategori dan satuan</p>
        </div>
        @if(auth()->user()->isAdminDapur())
        <button wire:click="create" id="btn-tambah-produk" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Produk
        </button>
        @endif
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text"
                           placeholder="Cari nama produk..." class="form-input pl-9" id="input-search-produk">
                </div>
                <select wire:model.live="filterCategory" class="form-select sm:w-48" id="filter-kategori">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterStatus" class="form-select sm:w-36" id="filter-status-produk">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        @if(auth()->user()->isAdminDapur())
                        <th class="text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr wire:key="product-{{ $product->id }}">
                        <td class="font-medium text-gray-800">{{ $product->nama_produk }}</td>
                        <td>
                            <span class="badge-teal">{{ $product->category->nama_kategori }}</span>
                        </td>
                        <td class="text-sm text-gray-600">{{ $product->unit->nama_satuan }}</td>
                        <td>
                            @if($product->status)
                                <span class="badge-green">Aktif</span>
                            @else
                                <span class="badge-red">Nonaktif</span>
                            @endif
                        </td>
                        @if(auth()->user()->isAdminDapur())
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="toggleStatus({{ $product->id }})"
                                    title="{{ $product->status ? 'Nonaktifkan' : 'Aktifkan' }}"
                                    class="btn-icon {{ $product->status ? 'text-yellow-600 hover:bg-yellow-50' : 'text-primary-600 hover:bg-primary-50' }}">
                                    @if($product->status)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @endif
                                </button>
                                <button wire:click="edit({{ $product->id }})" title="Edit"
                                    class="btn-icon text-blue-600 hover:bg-blue-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $product->id }})" title="Hapus"
                                    class="btn-icon text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <p class="empty-state-title">Belum ada produk</p>
                                <p class="empty-state-desc">
                                    @if($search) Tidak ada produk yang cocok dengan "{{ $search }}"
                                    @else Tambahkan produk pertama
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>

    {{-- Modal Form --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal', false)">
        <div class="modal-box animate-fade-up">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">{{ $editId ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-4 space-y-4">
                <div class="form-group">
                    <label class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                    <input wire:model="nama_produk" type="text" class="form-input"
                           placeholder="cth: Beras Premium" id="input-nama-produk">
                    @error('nama_produk') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                        <select wire:model="category_id" class="form-select" id="select-kategori-produk">
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <select wire:model="unit_id" class="form-select" id="select-satuan-produk">
                            <option value="">Pilih satuan</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_satuan }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                        wire:click="$set('status', !{{ $status ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                               {{ $status ? 'bg-primary-600' : 'bg-gray-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                     {{ $status ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                    <span class="text-sm font-medium text-gray-700">Status: {{ $status ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </form>
            <div class="modal-footer">
                <button wire:click="$set('showModal', false)" class="btn-secondary">Batal</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-primary" id="btn-simpan-produk">
                    <span wire:loading.remove>{{ $editId ? 'Perbarui' : 'Simpan' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if($showDeleteConfirm)
    <div class="modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 animate-fade-up">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Hapus Produk?</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Produk yang sudah digunakan dalam transaksi tidak bisa dihapus.</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('showDeleteConfirm', false)" class="btn-secondary btn-sm">Batal</button>
                <button wire:click="delete" wire:loading.attr="disabled" class="btn-danger btn-sm" id="btn-konfirmasi-hapus-produk">
                    <span wire:loading.remove>Ya, Hapus</span>
                    <span wire:loading>Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
