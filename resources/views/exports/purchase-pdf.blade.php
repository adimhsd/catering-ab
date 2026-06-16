<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian — Catering Al-Bahjah</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; }

        .header { background: #0f4c35; color: white; padding: 16px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 9px; opacity: 0.8; margin-top: 2px; }

        .meta { display: flex; gap: 24px; margin: 0 20px 12px; padding: 10px 14px;
                background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; }
        .meta-item { }
        .meta-label { font-size: 8px; color: #15803d; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-value { font-size: 10px; font-weight: bold; color: #14532d; }

        .summary { display: flex; gap: 12px; margin: 0 20px 12px; }
        .summary-card { flex: 1; padding: 10px 12px; background: #0f4c35; color: white;
                        border-radius: 6px; text-align: center; }
        .summary-card .label { font-size: 8px; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-card .value { font-size: 13px; font-weight: bold; margin-top: 2px; }

        table { width: calc(100% - 40px); margin: 0 20px; border-collapse: collapse; }
        thead th { background: #0f4c35; color: white; padding: 7px 8px;
                   text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
        thead th.text-right { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody td { padding: 6px 8px; font-size: 9px; vertical-align: top; }
        tbody td.text-right { text-align: right; }
        tbody td.nomor { font-family: monospace; font-weight: bold; color: #0f4c35; font-size: 8.5px; }
        tbody td.produk-list { color: #6b7280; font-size: 8px; line-height: 1.5; }

        tfoot td { background: #f0fdf4; border-top: 2px solid #0f4c35;
                   font-weight: bold; padding: 8px; font-size: 10px; }
        tfoot td.text-right { text-align: right; color: #14532d; font-size: 12px; }

        .footer { margin: 16px 20px 0; padding-top: 8px; border-top: 1px solid #e5e7eb;
                  font-size: 8px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pembelian Bahan Makanan</h1>
        <p>Catering Pondok Pesantren Al-Bahjah &nbsp;·&nbsp; Dicetak: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }}</p>
    </div>

    {{-- Meta Filter --}}
    <div class="meta">
        <div class="meta-item">
            <div class="meta-label">Periode</div>
            <div class="meta-value">
                {{ $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Semua' }}
                — {{ $sampai ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Semua' }}
            </div>
        </div>
        @if($supplierLabel)
        <div class="meta-item">
            <div class="meta-label">Supplier</div>
            <div class="meta-value">{{ $supplierLabel }}</div>
        </div>
        @endif
        @if($produkLabel)
        <div class="meta-item">
            <div class="meta-label">Produk</div>
            <div class="meta-value">{{ $produkLabel }}</div>
        </div>
        @endif
    </div>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-card">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ number_format($purchases->total()) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Pengeluaran</div>
            <div class="value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Produk</th>
                <th class="text-right">Total</th>
                <th>Input Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
            <tr>
                <td class="nomor">{{ $purchase->nomor_transaksi }}</td>
                <td>{{ $purchase->tanggal->format('d/m/Y') }}</td>
                <td>{{ $purchase->supplier->nama_supplier }}</td>
                <td class="produk-list">
                    @foreach($purchase->details as $d)
                    {{ $d->product->nama_produk }} ({{ number_format($d->qty, 2) }} {{ $d->product->unit->nama_satuan }})@if(!$loop->last)<br>@endif
                    @endforeach
                </td>
                <td class="text-right">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                <td>{{ $purchase->user->nama_lengkap }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 20px; color: #9ca3af;">
                    Tidak ada data transaksi untuk filter ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Laporan ini dibuat secara otomatis oleh Sistem Informasi Pengadaan Catering Al-Bahjah
    </div>
</body>
</html>
