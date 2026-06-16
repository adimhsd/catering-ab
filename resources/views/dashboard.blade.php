@php
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use Carbon\Carbon;

$bulanIni = Carbon::now()->startOfMonth();
$bulanLalu = Carbon::now()->subMonth()->startOfMonth();

// Statistik bulan ini
$totalTransaksiBulanIni = Purchase::whereMonth('tanggal', now()->month)
    ->whereYear('tanggal', now()->year)->count();
$totalPengeluaranBulanIni = Purchase::whereMonth('tanggal', now()->month)
    ->whereYear('tanggal', now()->year)->sum('total');

// Statistik bulan lalu (untuk perbandingan)
$totalTransaksiBulanLalu = Purchase::whereMonth('tanggal', now()->subMonth()->month)
    ->whereYear('tanggal', now()->subMonth()->year)->count();
$totalPengeluaranBulanLalu = Purchase::whereMonth('tanggal', now()->subMonth()->month)
    ->whereYear('tanggal', now()->subMonth()->year)->sum('total');

$jumlahSupplierAktif = Supplier::aktif()->count();
$jumlahProduk = Product::count();

// 5 transaksi terbaru
$transaksiTerbaru = Purchase::with(['supplier', 'user'])
    ->orderByDesc('tanggal')->orderByDesc('id')
    ->limit(5)->get();

// Hitung persentase perubahan
$pctTransaksi = $totalTransaksiBulanLalu > 0
    ? round((($totalTransaksiBulanIni - $totalTransaksiBulanLalu) / $totalTransaksiBulanLalu) * 100, 1)
    : 0;
$pctPengeluaran = $totalPengeluaranBulanLalu > 0
    ? round((($totalPengeluaranBulanIni - $totalPengeluaranBulanLalu) / $totalPengeluaranBulanLalu) * 100, 1)
    : 0;
@endphp

<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Salam --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Assalamu'alaikum, {{ auth()->user()->nama_lengkap }} 👋
        </h1>
        <p class="text-gray-500 mt-1">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} —
            Berikut ringkasan pengadaan bulan ini.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Total Transaksi --}}
        <div class="stat-card animate-fade-up">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Transaksi Bulan Ini</p>
                    <p class="stat-value">{{ number_format($totalTransaksiBulanIni) }}</p>
                    <div class="flex items-center gap-1 mt-2">
                        @if($pctTransaksi >= 0)
                            <span class="text-xs text-primary-600 font-medium">↑ {{ $pctTransaksi }}%</span>
                        @else
                            <span class="text-xs text-red-500 font-medium">↓ {{ abs($pctTransaksi) }}%</span>
                        @endif
                        <span class="text-xs text-gray-400">vs bulan lalu</span>
                    </div>
                </div>
                <div class="stat-card-icon bg-primary-100">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="stat-card animate-fade-up" style="animation-delay: 50ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Pengeluaran Bulan Ini</p>
                    <p class="stat-value text-xl">
                        Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1 mt-2">
                        @if($pctPengeluaran >= 0)
                            <span class="text-xs text-primary-600 font-medium">↑ {{ $pctPengeluaran }}%</span>
                        @else
                            <span class="text-xs text-red-500 font-medium">↓ {{ abs($pctPengeluaran) }}%</span>
                        @endif
                        <span class="text-xs text-gray-400">vs bulan lalu</span>
                    </div>
                </div>
                <div class="stat-card-icon bg-teal-100">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Supplier Aktif --}}
        <div class="stat-card animate-fade-up" style="animation-delay: 100ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Supplier Aktif</p>
                    <p class="stat-value">{{ number_format($jumlahSupplierAktif) }}</p>
                    <p class="text-xs text-gray-400 mt-2">Mitra pemasok terdaftar</p>
                </div>
                <div class="stat-card-icon bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Jumlah Produk --}}
        <div class="stat-card animate-fade-up" style="animation-delay: 150ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Total Produk</p>
                    <p class="stat-value">{{ number_format($jumlahProduk) }}</p>
                    <p class="text-xs text-gray-400 mt-2">Bahan makanan terdaftar</p>
                </div>
                <div class="stat-card-icon bg-orange-100">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid: Transaksi Terbaru + Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Transaksi Terbaru (2/3 width) --}}
        <div class="lg:col-span-2 card">
            <div class="card-header">
                <div>
                    <h2 class="font-semibold text-gray-800">Transaksi Terbaru</h2>
                    <p class="text-xs text-gray-500 mt-0.5">5 transaksi pembelian terakhir</p>
                </div>
                <a href="{{ route('purchases.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    Lihat semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                @if($transaksiTerbaru->isEmpty())
                <div class="empty-state py-10">
                    <div class="empty-state-icon">📋</div>
                    <p class="empty-state-title">Belum ada transaksi</p>
                    <p class="empty-state-desc">Transaksi pembelian akan muncul di sini</p>
                </div>
                @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksiTerbaru as $trx)
                        <tr>
                            <td>
                                <a href="{{ route('purchases.show', $trx) }}"
                                   class="font-mono text-xs text-primary-700 hover:text-primary-900 font-semibold">
                                    {{ $trx->nomor_transaksi }}
                                </a>
                            </td>
                            <td class="text-xs text-gray-500">
                                {{ $trx->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="text-sm">{{ $trx->supplier->nama_supplier }}</td>
                            <td class="text-right font-semibold text-sm">
                                {{ $trx->total_rupiah }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Quick Actions (1/3 width) --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Aksi Cepat</h2>
            </div>
            <div class="card-body space-y-3">
                @if(auth()->user()->isAdminDapur())
                <a href="{{ route('purchases.create') }}"
                   class="flex items-center gap-3 p-3 rounded-xl bg-primary-50 hover:bg-primary-100
                          border border-primary-200 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-primary-600 flex items-center justify-center group-hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-primary-800">Input Pembelian</p>
                        <p class="text-xs text-primary-600">Tambah transaksi baru</p>
                    </div>
                </a>
                @endif

                <a href="{{ route('reports.index') }}"
                   class="flex items-center gap-3 p-3 rounded-xl bg-teal-50 hover:bg-teal-100
                          border border-teal-200 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-teal-600 flex items-center justify-center group-hover:bg-teal-700 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-teal-800">Laporan</p>
                        <p class="text-xs text-teal-600">Lihat & export laporan</p>
                    </div>
                </a>

                <a href="{{ route('analytics.index') }}"
                   class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100
                          border border-blue-200 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center group-hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">Analitik</p>
                        <p class="text-xs text-blue-600">Grafik & top supplier</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
