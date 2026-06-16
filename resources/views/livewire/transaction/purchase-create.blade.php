<div x-data>
    <div class="page-header">
        <div>
            <h1 class="page-title">Input Transaksi Pembelian</h1>
            <p class="page-subtitle">Catat nota pembelian bahan makanan dari supplier</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== FORM UTAMA (2/3) ===== --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Header Transaksi --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Header Transaksi</h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Supplier --}}
                        <div class="form-group sm:col-span-1">
                            <label class="form-label">Supplier <span class="text-red-500">*</span></label>
                            <select wire:model="supplier_id" class="form-select" id="select-supplier">
                                <option value="">— Pilih supplier aktif —</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="form-group sm:col-span-1">
                            <label class="form-label">Tanggal Transaksi <span class="text-red-500">*</span></label>
                            <input wire:model="tanggal" type="date" class="form-input" id="input-tanggal">
                            @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Catatan --}}
                        <div class="form-group sm:col-span-2">
                            <label class="form-label">Catatan <span class="text-gray-400 text-xs">(opsional)</span></label>
                            <textarea wire:model="catatan" class="form-textarea" rows="2"
                                      placeholder="Catatan tambahan untuk transaksi ini..." id="input-catatan"></textarea>
                            @error('catatan') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Items --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="font-semibold text-gray-800">Detail Item</h2>
                        <p class="text-xs text-gray-500 mt-0.5">{{ count($items) }} baris item</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-5/12">Produk</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-2/12">Qty</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-2/12">Harga Satuan</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-2/12">Subtotal</th>
                                <th class="px-4 py-2.5 w-1/12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($items as $i => $item)
                            <tr wire:key="item-row-{{ $i }}">
                                <td class="px-3 py-2">
                                    <select wire:model.live="items.{{ $i }}.product_id"
                                            class="form-select text-sm" id="select-produk-{{ $i }}">
                                        <option value="">— Pilih produk —</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}">
                                            {{ $p->nama_produk }} ({{ $p->unit->nama_satuan }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error("items.{$i}.product_id") <p class="form-error">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input wire:model.live="items.{{ $i }}.qty"
                                           type="number" step="0.01" min="0.01"
                                           class="form-input text-sm text-center"
                                           placeholder="0"
                                           id="input-qty-{{ $i }}">
                                    @error("items.{$i}.qty") <p class="form-error">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input wire:model.live="items.{{ $i }}.harga"
                                           type="number" step="100" min="0"
                                           class="form-input text-sm text-right"
                                           placeholder="0"
                                           id="input-harga-{{ $i }}">
                                    @error("items.{$i}.harga") <p class="form-error">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <span class="text-sm font-semibold text-gray-700" id="subtotal-{{ $i }}">
                                        Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if(count($items) > 1)
                                    <button wire:click="hapusBaris({{ $i }})"
                                            title="Hapus baris"
                                            class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"/>
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
                            class="text-sm text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1.5"
                            id="btn-tambah-baris">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Baris Item
                    </button>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total Keseluruhan</p>
                        <p class="text-xl font-bold text-primary-700" id="total-keseluruhan">
                            Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @error('items') <p class="px-4 pb-3 form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ===== SIDEBAR (1/3) ===== --}}
        <div class="space-y-4">

            {{-- Upload Nota --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Lampiran Nota</h2>
                    <span class="text-xs text-gray-400">Opsional</span>
                </div>
                <div class="card-body">
                    {{-- Drop zone --}}
                    <label for="input-file-nota"
                           class="flex flex-col items-center justify-center w-full h-36 rounded-xl
                                  border-2 border-dashed border-gray-300 cursor-pointer
                                  hover:border-primary-400 hover:bg-primary-50 transition-colors">
                        @if($fileNota)
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto text-primary-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-primary-700">{{ $fileNota->getClientOriginalName() }}</p>
                            <p class="text-xs text-gray-400">{{ round($fileNota->getSize() / 1024, 1) }} KB</p>
                        </div>
                        @else
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-600">Klik untuk upload foto nota</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau PDF (max 5MB)</p>
                        </div>
                        @endif
                    </label>
                    <input wire:model="fileNota" type="file" id="input-file-nota"
                           accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                    @error('fileNota') <p class="form-error mt-2">{{ $message }}</p> @enderror

                    {{-- Preview gambar --}}
                    @if($fileNota && $fileNota->getMimeType() && str_starts_with($fileNota->getMimeType(), 'image/'))
                    <div class="mt-3 rounded-lg overflow-hidden border border-gray-200">
                        <img src="{{ $fileNota->temporaryUrl() }}" alt="Preview nota"
                             class="w-full h-40 object-cover">
                    </div>
                    @endif
                </div>
            </div>

            {{-- Summary & Simpan --}}
            <div class="card">
                <div class="card-body space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Jumlah item:</span>
                        <span class="font-semibold">{{ count($items) }} baris</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total pembelian:</span>
                        <span class="font-bold text-lg text-primary-700">
                            Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                        </span>
                    </div>
                    <hr class="border-gray-100">
                    <button wire:click="simpan" wire:loading.attr="disabled"
                            class="btn-primary w-full btn-lg" id="btn-simpan-transaksi">
                        <span wire:loading.remove class="flex items-center gap-2 justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan Transaksi
                        </span>
                        <span wire:loading class="flex items-center gap-2 justify-center">
                            <span class="spinner w-5 h-5"></span>
                            Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('purchases.index') }}" class="btn-secondary w-full text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
