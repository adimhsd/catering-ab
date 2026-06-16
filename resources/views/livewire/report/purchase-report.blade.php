<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Laporan Pembelian</h1>
            <p class="page-subtitle">Filter dan ekspor data transaksi pembelian</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.export.excel', [
                    'dari'     => $tanggalDari,
                    'sampai'   => $tanggalSampai,
                    'supplier' => $filterSupplier,
                    'produk'   => $filterProduct,
                ]) }}"
               id="btn-export-excel"
               class="btn-teal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('reports.export.pdf', [
                    'dari'     => $tanggalDari,
                    'sampai'   => $tanggalSampai,
                    'supplier' => $filterSupplier,
                    'produk'   => $filterProduct,
                ]) }}"
               target="_blank"
               id="btn-export-pdf"
               class="btn-danger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="font-semibold text-gray-700 text-sm">Filter Laporan</h2>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-group mb-0">
                    <label class="form-label">Tanggal Dari</label>
                    <input wire:model.live="tanggalDari" type="date" class="form-input" id="filter-laporan-dari">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Tanggal Sampai</label>
                    <input wire:model.live="tanggalSampai" type="date" class="form-input" id="filter-laporan-sampai">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Supplier</label>
                    <select wire:model.live="filterSupplier" class="form-select" id="filter-laporan-supplier">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Produk</label>
                    <select wire:model.live="filterProduct" class="form-select" id="filter-laporan-produk">
                        <option value="">Semua Produk</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Periode --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Total Transaksi</p>
                    <p class="stat-value">{{ number_format($totalTransaksi) }}</p>
                    <p class="text-xs text-gray-400 mt-1">pada periode yang dipilih</p>
                </div>
                <div class="stat-card-icon bg-primary-100">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Total Pengeluaran</p>
                    <p class="stat-value text-xl">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">pada periode yang dipilih</p>
                </div>
                <div class="stat-card-icon bg-teal-100">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Laporan --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Produk</th>
                        <th class="text-right">Total</th>
                        <th>Diinput Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr wire:key="laporan-{{ $purchase->id }}">
                        <td>
                            <a href="{{ route('purchases.show', $purchase) }}"
                               class="font-mono text-xs font-semibold text-primary-700 hover:text-primary-900">
                                {{ $purchase->nomor_transaksi }}
                            </a>
                        </td>
                        <td class="text-sm text-gray-500">{{ $purchase->tanggal->format('d/m/Y') }}</td>
                        <td class="text-sm font-medium">{{ $purchase->supplier->nama_supplier }}</td>
                        <td class="text-xs text-gray-500 max-w-xs">
                            {{ $purchase->details->pluck('product.nama_produk')->join(', ') }}
                        </td>
                        <td class="text-right font-semibold text-sm">{{ $purchase->total_rupiah }}</td>
                        <td class="text-xs text-gray-500">{{ $purchase->user->nama_lengkap }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">📊</div>
                                <p class="empty-state-title">Tidak ada data</p>
                                <p class="empty-state-desc">Coba ubah filter periode atau supplier</p>
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
</div>
