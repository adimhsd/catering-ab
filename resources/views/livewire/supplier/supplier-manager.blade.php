<div>
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Master Supplier</h1>
            <p class="page-subtitle">Kelola data mitra pemasok bahan makanan</p>
        </div>
        @if(auth()->user()->isAdminDapur())
        <button wire:click="create" id="btn-tambah-supplier" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Supplier
        </button>
        @endif
    </div>

    {{-- Filter Bar --}}
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
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Cari nama supplier atau PIC..."
                        class="form-input pl-9"
                        id="input-search-supplier"
                    >
                </div>
                <select wire:model.live="filterStatus" class="form-select sm:w-40" id="filter-status-supplier">
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
                        <th>Nama Supplier</th>
                        <th>PIC</th>
                        <th>WhatsApp</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr wire:key="supplier-{{ $supplier->id }}">
                        <td>
                            <div class="font-semibold text-gray-800">{{ $supplier->nama_supplier }}</div>
                            @if($supplier->alamat)
                            <div class="text-xs text-gray-400 truncate max-w-xs">{{ $supplier->alamat }}</div>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">{{ $supplier->pic ?: '—' }}</td>
                        <td>
                            @if($supplier->wa)
                            <a href="https://wa.me/62{{ ltrim($supplier->wa, '0') }}"
                               target="_blank"
                               class="text-sm text-green-600 hover:text-green-800 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                {{ $supplier->wa }}
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td>
                            @if($supplier->status)
                                <span class="badge-green">Aktif</span>
                            @else
                                <span class="badge-red">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(auth()->user()->isAdminDapur())
                                {{-- Toggle Status --}}
                                <button
                                    wire:click="toggleStatus({{ $supplier->id }})"
                                    wire:loading.attr="disabled"
                                    title="{{ $supplier->status ? 'Nonaktifkan' : 'Aktifkan' }}"
                                    class="btn-icon {{ $supplier->status ? 'text-yellow-600 hover:bg-yellow-50' : 'text-primary-600 hover:bg-primary-50' }}">
                                    @if($supplier->status)
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

                                {{-- Edit --}}
                                <button
                                    wire:click="edit({{ $supplier->id }})"
                                    title="Edit"
                                    class="btn-icon text-blue-600 hover:bg-blue-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- Hapus --}}
                                <button
                                    wire:click="confirmDelete({{ $supplier->id }})"
                                    title="Hapus"
                                    class="btn-icon text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @else
                                <span class="text-xs text-gray-400">Lihat saja</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">🏪</div>
                                <p class="empty-state-title">Belum ada supplier</p>
                                <p class="empty-state-desc">
                                    @if($search) Tidak ada supplier yang cocok dengan pencarian "{{ $search }}"
                                    @else Tambahkan supplier pertama Anda
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="card-footer">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>

    {{-- ===== MODAL FORM SUPPLIER ===== --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal', false)">
        <div class="modal-box animate-fade-up">
            <div class="modal-header">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $editId ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-4 space-y-4">
                {{-- Nama Supplier --}}
                <div class="form-group">
                    <label class="form-label">Nama Supplier <span class="text-red-500">*</span></label>
                    <input wire:model="nama_supplier" type="text" class="form-input"
                           placeholder="cth: UD. Pasar Segar" id="input-nama-supplier">
                    @error('nama_supplier') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- PIC --}}
                <div class="form-group">
                    <label class="form-label">PIC (Penanggung Jawab)</label>
                    <input wire:model="pic" type="text" class="form-input"
                           placeholder="Nama kontak supplier" id="input-pic">
                    @error('pic') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- WhatsApp --}}
                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input wire:model="wa" type="text" class="form-input"
                           placeholder="cth: 081234567890" id="input-wa">
                    @error('wa') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Alamat --}}
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea wire:model="alamat" class="form-textarea" rows="2"
                              placeholder="Alamat lengkap supplier" id="input-alamat"></textarea>
                    @error('alamat') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3">
                    <button type="button"
                        wire:click="$set('status', !{{ $status ? 'false' : 'true' }})"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                               {{ $status ? 'bg-primary-600' : 'bg-gray-300' }}"
                        id="toggle-status">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                     {{ $status ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                    <span class="text-sm font-medium text-gray-700">
                        Status: {{ $status ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </form>

            <div class="modal-footer">
                <button wire:click="$set('showModal', false)" class="btn-secondary">Batal</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-primary" id="btn-simpan-supplier">
                    <span wire:loading.remove>{{ $editId ? 'Perbarui' : 'Simpan' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== MODAL KONFIRMASI HAPUS ===== --}}
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
                    <h3 class="font-semibold text-gray-900">Hapus Supplier?</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('showDeleteConfirm', false)" class="btn-secondary btn-sm">Batal</button>
                <button wire:click="delete" wire:loading.attr="disabled" class="btn-danger btn-sm" id="btn-konfirmasi-hapus-supplier">
                    <span wire:loading.remove>Ya, Hapus</span>
                    <span wire:loading>Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
