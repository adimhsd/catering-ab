<div x-data>
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Transaksi</h1>
            <p class="page-subtitle font-mono text-xs text-gray-500">{{ $purchase->nomor_transaksi }}</p>
        </div>
        <a href="{{ route('purchases.show', $purchase) }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Batal
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            {{-- Header --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Header Transaksi</h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Supplier <span class="text-red-500">*</span></label>
                            <select wire:model="supplier_id" class="form-select" id="select-supplier-edit">
                                <option value="">— Pilih supplier —</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                            <input wire:model="tanggal" type="date" class="form-input" id="input-tanggal-edit">
                            @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group sm:col-span-2">
                            <label class="form-label">Catatan</label>
                            <textarea wire:model="catatan" class="form-textarea" rows="2"
                                      placeholder="Catatan tambahan..." id="input-catatan-edit"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Items --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Detail Item</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase w-5/12">Produk</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase w-2/12">Qty</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase w-2/12">Harga</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase w-2/12">Subtotal</th>
                                <th class="px-4 py-2.5 w-1/12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($items as $i => $item)
                            <tr wire:key="edit-item-{{ $i }}">
                                <td class="px-3 py-2">
                                    <select wire:model.live="items.{{ $i }}.product_id" class="form-select text-sm">
                                        <option value="">— Pilih produk —</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_produk }} ({{ $p->unit->nama_satuan }})</option>
                                        @endforeach
                                    </select>
                                    @error("items.{$i}.product_id") <p class="form-error">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input wire:model.live="items.{{ $i }}.qty" type="number" step="0.01"
                                           class="form-input text-sm text-center" placeholder="0">
                                </td>
                                <td class="px-3 py-2">
                                    <input wire:model.live="items.{{ $i }}.harga" type="number" step="100"
                                           class="form-input text-sm text-right" placeholder="0">
                                </td>
                                <td class="px-3 py-2 text-right text-sm font-semibold text-gray-700">
                                    Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if(count($items) > 1)
                                    <button wire:click="hapusBaris({{ $i }})" class="text-red-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <button wire:click="tambahBaris" type="button"
                            class="text-sm text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Baris
                    </button>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-xl font-bold text-primary-700">
                            Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Lampiran Existing --}}
            @if($purchase->attachments->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Lampiran Tersimpan</h2>
                </div>
                <div class="card-body space-y-2">
                    @foreach($purchase->attachments as $att)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg {{ $att->is_pdf ? 'bg-red-100' : 'bg-blue-100' }} flex items-center justify-center">
                                @if($att->is_pdf)
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                @else
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $att->original_name ?? basename($att->file_nota) }}</p>
                                <a href="{{ $att->url }}" target="_blank" class="text-xs text-primary-600 hover:underline">Lihat file</a>
                            </div>
                        </div>
                        <button wire:click="hapusLampiran({{ $att->id }})"
                                wire:confirm="Hapus lampiran ini?"
                                class="text-red-400 hover:text-red-600 btn-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Upload Nota Baru --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Tambah Lampiran</h2>
                    <span class="text-xs text-gray-400">Opsional</span>
                </div>
                <div class="card-body">
                    <label for="input-file-nota-edit"
                           class="flex flex-col items-center justify-center w-full h-28 rounded-xl
                                  border-2 border-dashed border-gray-300 cursor-pointer
                                  hover:border-primary-400 hover:bg-primary-50 transition-colors">
                        @if($fileNota)
                        <p class="text-sm font-medium text-primary-700">{{ $fileNota->getClientOriginalName() }}</p>
                        @else
                        <svg class="w-7 h-7 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs text-gray-500">Upload nota baru</p>
                        <p class="text-xs text-gray-400">JPG, PNG, PDF (max 5MB)</p>
                        @endif
                    </label>
                    <input wire:model="fileNota" type="file" id="input-file-nota-edit"
                           accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                    @error('fileNota') <p class="form-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Aksi --}}
            <div class="card">
                <div class="card-body space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-lg text-primary-700">
                            Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                        </span>
                    </div>
                    <hr class="border-gray-100">
                    <button wire:click="perbarui" wire:loading.attr="disabled"
                            class="btn-primary w-full btn-lg" id="btn-perbarui-transaksi">
                        <span wire:loading.remove>Simpan Perubahan</span>
                        <span wire:loading class="flex items-center gap-2 justify-center">
                            <span class="spinner w-5 h-5"></span> Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn-secondary w-full text-center">Batal</a>
                </div>
            </div>
        </div>
    </div>
</div>
