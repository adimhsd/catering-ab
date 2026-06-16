<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Detail Transaksi</h1>
            <p class="page-subtitle font-mono">{{ $purchase->nomor_transaksi }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdminDapur() && $purchase->user_id === auth()->id())
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @endif
            <a href="{{ route('purchases.index') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detail Utama (2/3) --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Info Transaksi --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Informasi Transaksi</h2>
                    <span class="badge-teal">{{ $purchase->tanggal->format('d F Y') }}</span>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</dt>
                            <dd class="mt-1 font-mono text-sm font-semibold text-gray-800">{{ $purchase->nomor_transaksi }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</dt>
                            <dd class="mt-1 text-sm text-gray-800">{{ $purchase->tanggal->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-800">{{ $purchase->supplier->nama_supplier }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Diinput Oleh</dt>
                            <dd class="mt-1 text-sm text-gray-700">{{ $purchase->user->nama_lengkap }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Input</dt>
                            <dd class="mt-1 text-sm text-gray-700">{{ $purchase->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        @if($purchase->catatan)
                        <div class="col-span-2 sm:col-span-3">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</dt>
                            <dd class="mt-1 text-sm text-gray-700">{{ $purchase->catatan }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Detail Item --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Detail Item Pembelian</h2>
                    <span class="text-sm text-gray-500">{{ $purchase->details->count() }} item</span>
                </div>
                <div class="table-container rounded-none border-none">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->details as $detail)
                            <tr>
                                <td>
                                    <div class="font-medium text-gray-800">{{ $detail->product->nama_produk }}</div>
                                    <div class="text-xs text-gray-400">{{ $detail->product->category->nama_kategori }}</div>
                                </td>
                                <td class="text-center text-sm">
                                    {{ number_format($detail->qty, 2) }}
                                    <span class="text-gray-400 text-xs">{{ $detail->product->unit->nama_satuan }}</span>
                                </td>
                                <td class="text-right text-sm">{{ $detail->harga_rupiah }}</td>
                                <td class="text-right font-semibold text-sm">{{ $detail->subtotal_rupiah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-700">TOTAL</td>
                                <td class="px-4 py-3 text-right font-bold text-lg text-primary-700">
                                    {{ $purchase->total_rupiah }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar: Lampiran --}}
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Lampiran Nota</h2>
                    <span class="badge-blue">{{ $purchase->attachments->count() }} file</span>
                </div>
                <div class="card-body space-y-3">
                    @forelse($purchase->attachments as $att)
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        @if($att->is_image)
                        <a href="{{ $att->url }}" target="_blank">
                            <img src="{{ $att->url }}" alt="Nota" class="w-full h-48 object-cover hover:opacity-90 transition-opacity">
                        </a>
                        @else
                        <div class="p-4 flex items-center gap-3 bg-gray-50">
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 truncate">
                                    {{ $att->original_name ?? basename($att->file_nota) }}
                                </p>
                                <a href="{{ $att->url }}" target="_blank"
                                   class="text-xs text-primary-600 hover:underline">Buka PDF</a>
                            </div>
                        </div>
                        @endif
                        <div class="px-3 py-2 bg-white border-t border-gray-100">
                            <p class="text-xs text-gray-400">{{ $att->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <p class="text-sm text-gray-400">Tidak ada lampiran nota</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
