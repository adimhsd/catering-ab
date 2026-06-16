<?php

namespace App\Livewire\Analytics;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Analitik')]
class AnalyticsDashboard extends Component
{
    public string $periode = 'bulan_ini';
    public string $tanggalDari = '';
    public string $tanggalSampai = '';

    public function mount(): void
    {
        $this->setPeriode('bulan_ini');
    }

    public function setPeriode(string $periode): void
    {
        $this->periode = $periode;

        switch ($periode) {
            case 'bulan_ini':
                $this->tanggalDari   = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->tanggalSampai = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case '3_bulan':
                $this->tanggalDari   = Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                $this->tanggalSampai = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case '6_bulan':
                $this->tanggalDari   = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
                $this->tanggalSampai = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'tahun_ini':
                $this->tanggalDari   = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->tanggalSampai = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'kustom':
                // User set tanggal sendiri
                break;
        }
    }

    public function render(): \Illuminate\View\View
    {
        // === Top 5 Supplier ===
        $topSupplier = Purchase::query()
            ->select('supplier_id', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(total) as total_pembelian'))
            ->when($this->tanggalDari,   fn($q) => $q->where('tanggal', '>=', $this->tanggalDari))
            ->when($this->tanggalSampai, fn($q) => $q->where('tanggal', '<=', $this->tanggalSampai))
            ->groupBy('supplier_id')
            ->orderByDesc('jumlah_transaksi')
            ->with('supplier')
            ->limit(5)
            ->get();

        // === Top 5 Produk ===
        $topProduk = PurchaseDetail::query()
            ->select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('COUNT(*) as frekuensi'))
            ->whereHas('purchase', function ($q) {
                $q->when($this->tanggalDari,   fn($q) => $q->where('tanggal', '>=', $this->tanggalDari))
                  ->when($this->tanggalSampai, fn($q) => $q->where('tanggal', '<=', $this->tanggalSampai));
            })
            ->groupBy('product_id')
            ->orderByDesc('frekuensi')
            ->with(['product.unit'])
            ->limit(5)
            ->get();

        // === Grafik Pengeluaran Bulanan (12 bulan terakhir) ===
        $grafikData = Purchase::query()
            ->select(
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('SUM(total) as total')
            )
            ->where('tanggal', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Format untuk Chart.js
        $grafikLabels = $grafikData->map(fn($d) =>
            Carbon::create($d->tahun, $d->bulan, 1)->translatedFormat('M Y')
        )->toArray();

        $grafikNilai = $grafikData->pluck('total')->toArray();

        return view('livewire.analytics.analytics-dashboard',
            compact('topSupplier', 'topProduk', 'grafikLabels', 'grafikNilai')
        );
    }
}
