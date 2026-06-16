<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">Analitik</h1>
            <p class="page-subtitle">Insight pengeluaran, top supplier, dan produk terlaris</p>
        </div>
        {{-- Pilihan Periode --}}
        <div class="flex items-center gap-2">
            @foreach([
                'bulan_ini'  => 'Bulan Ini',
                '3_bulan'    => '3 Bulan',
                '6_bulan'    => '6 Bulan',
                'tahun_ini'  => 'Tahun Ini',
            ] as $key => $label)
            <button wire:click="setPeriode('{{ $key }}')"
                    class="px-3 py-1.5 text-sm rounded-lg font-medium transition-colors
                           {{ $periode === $key
                               ? 'bg-primary-600 text-white'
                               : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Grafik Pengeluaran Bulanan --}}
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h2 class="font-semibold text-gray-800">Total Pengeluaran Bulanan</h2>
                <p class="text-xs text-gray-500 mt-0.5">12 bulan terakhir</p>
            </div>
        </div>
        <div class="card-body">
            <div class="h-64">
                <canvas id="chartPengeluaran"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Supplier & Top Produk --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top 5 Supplier --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="font-semibold text-gray-800">Top 5 Supplier</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Berdasarkan jumlah transaksi</p>
                </div>
            </div>
            <div class="card-body space-y-3">
                @forelse($topSupplier as $i => $row)
                @php $maxTrx = $topSupplier->max('jumlah_transaksi'); @endphp
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                                {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-800">{{ $row->supplier->nama_supplier }}</span>
                            <span class="text-xs text-gray-500">{{ $row->jumlah_transaksi }} transaksi</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-primary-500 h-1.5 rounded-full transition-all"
                                 style="width: {{ $maxTrx > 0 ? ($row->jumlah_transaksi / $maxTrx * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">Rp {{ number_format($row->total_pembelian, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <div class="empty-state py-8">
                    <div class="empty-state-icon">🏪</div>
                    <p class="empty-state-title">Belum ada data</p>
                    <p class="empty-state-desc">Tidak ada transaksi pada periode ini</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top 5 Produk --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="font-semibold text-gray-800">Top 5 Produk</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Berdasarkan frekuensi pembelian</p>
                </div>
            </div>
            <div class="card-body space-y-3">
                @forelse($topProduk as $i => $row)
                @php $maxFreq = $topProduk->max('frekuensi'); @endphp
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                                {{ $i === 0 ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-800">{{ $row->product->nama_produk }}</span>
                            <span class="text-xs text-gray-500">{{ $row->frekuensi }}× dibeli</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-teal-500 h-1.5 rounded-full transition-all"
                                 style="width: {{ $maxFreq > 0 ? ($row->frekuensi / $maxFreq * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Total: {{ number_format($row->total_qty, 2) }} {{ $row->product->unit->nama_satuan }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="empty-state py-8">
                    <div class="empty-state-icon">📦</div>
                    <p class="empty-state-title">Belum ada data</p>
                    <p class="empty-state-desc">Tidak ada pembelian produk pada periode ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('livewire:navigated', function() { initChart(); });
    document.addEventListener('DOMContentLoaded', function() { initChart(); });

    function initChart() {
        const ctx = document.getElementById('chartPengeluaran');
        if (!ctx) return;

        // Hapus chart lama jika ada (saat Livewire re-render)
        if (ctx._chartInstance) {
            ctx._chartInstance.destroy();
        }

        const labels = @json($grafikLabels);
        const data   = @json($grafikNilai);

        ctx._chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pengeluaran (Rp)',
                    data: data,
                    backgroundColor: 'rgba(15, 76, 53, 0.7)',
                    borderColor: '#0f4c35',
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: 'rgba(15, 76, 53, 0.9)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                        },
                        grid: { color: '#f3f4f6' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Re-init chart saat Livewire update
    document.addEventListener('livewire:update', function() {
        setTimeout(initChart, 100);
    });
    </script>
    @endpush
</div>
