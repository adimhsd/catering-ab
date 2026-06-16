<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Riwayat Pembelian</h1>
            <p class="page-subtitle">Daftar seluruh transaksi pembelian bahan makanan</p>
        </div>
        @if(auth()->user()->isAdminDapur())
        <a href="{{ route('purchases.create') }}" class="btn-primary" id="btn-input-pembelian">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Input Pembelian
        </a>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text"
                           placeholder="No. transaksi..." class="form-input pl-9" id="input-search-transaksi">
                </div>
                <select wire:model.live="filterSupplier" class="form-select" id="filter-supplier-transaksi">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
                <div>
                    <input wire:model.live="tanggalDari" type="date" class="form-input" id="filter-tanggal-dari">
                </div>
                <div>
                    <input wire:model.live="tanggalSampai" type="date" class="form-input" id="filter-tanggal-sampai">
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-3 px-1">
        <p class="text-sm text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ number_format($totalFiltered) }}</span> transaksi
        </p>
        <p class="text-sm text-gray-500">
            Total: <span class="font-bold text-primary-700">Rp {{ number_format($sumFiltered, 0, ',', '.') }}</span>
        </p>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Input Oleh</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Nota</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr wire:key="purchase-{{ $purchase->id }}">
                        <td>
                            <a href="{{ route('purchases.show', $purchase) }}"
                               class="font-mono text-xs font-semibold text-primary-700 hover:text-primary-900">
                                {{ $purchase->nomor_transaksi }}
                            </a>
                        </td>
                        <td class="text-sm text-gray-500">{{ $purchase->tanggal->format('d/m/Y') }}</td>
                        <td class="text-sm font-medium">{{ $purchase->supplier->nama_supplier }}</td>
                        <td class="text-xs text-gray-500">{{ $purchase->user->nama_lengkap }}</td>
                        <td class="text-right font-semibold text-sm">{{ $purchase->total_rupiah }}</td>
                        <td class="text-center">
                            @if($purchase->attachments->isNotEmpty())
                            <span title="{{ $purchase->attachments->count() }} lampiran" class="text-teal-600">
                                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </span>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('purchases.show', $purchase) }}"
                                   title="Detail" class="btn-icon text-gray-600 hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(auth()->user()->isAdminDapur() && $purchase->user_id === auth()->id())
                                <a href="{{ route('purchases.edit', $purchase) }}"
                                   title="Edit" class="btn-icon text-blue-600 hover:bg-blue-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button wire:click="confirmDelete({{ $purchase->id }})"
                                   title="Hapus" class="btn-icon text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-14">
                            <div class="empty-state">
                                <div class="empty-state-icon">📋</div>
                                <p class="empty-state-title">Belum ada transaksi</p>
                                <p class="empty-state-desc">
                                    @if($search || $filterSupplier || $tanggalDari)
                                        Tidak ada transaksi yang cocok dengan filter ini.
                                    @else
                                        Mulai input transaksi pembelian pertama.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <div class="card-footer">{{ $purchases->links() }}</div>
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    @if($showDeleteConfirm)
    <div class="modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 animate-fade-up">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Hapus Transaksi?</h3>
                    <p class="text-sm text-gray-500">Semua detail dan lampiran akan ikut terhapus.</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('showDeleteConfirm', false)" class="btn-secondary btn-sm">Batal</button>
                <button wire:click="delete" wire:loading.attr="disabled" class="btn-danger btn-sm" id="btn-konfirmasi-hapus-transaksi">
                    <span wire:loading.remove>Ya, Hapus</span>
                    <span wire:loading>Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
