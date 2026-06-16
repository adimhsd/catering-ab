<div align="center">

<img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
<img src="https://img.shields.io/badge/Livewire-3-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" />
<img src="https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/PWA-Installable-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white" />

<br /><br />

<h1>🍽️ Sistem Informasi Pengadaan Catering Al-Bahjah</h1>

<p><strong>Digitalisasi nota pembelian bahan makanan Pondok Pesantren Al-Bahjah</strong></p>
<p>Aplikasi web berbasis PWA yang dapat diinstall di Android & desktop</p>

</div>

---

## 📋 Tentang Proyek

Sistem ini dibangun untuk mendigitalisasi proses pencatatan pengadaan bahan makanan Catering Pondok Pesantren Al-Bahjah. Menggantikan pencatatan manual di buku/kertas menjadi sistem digital yang terstruktur, dapat diakses dari smartphone, dan menghasilkan laporan otomatis.

### 🎯 Masalah yang Diselesaikan
- **Sebelumnya:** Nota pembelian dari supplier dicatat manual → rentan hilang, sulit direkap
- **Sesudahnya:** Admin Dapur input nota lewat HP → data tersimpan → Kepala Divisi bisa lihat laporan kapan saja

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 📝 **Input Nota** | Form multi-item dinamis, kalkulasi otomatis subtotal & total |
| 📸 **Upload Foto Nota** | Lampirkan foto/PDF nota dari supplier |
| 👥 **Manajemen Supplier** | Kelola data mitra pemasok beserta kontak WhatsApp |
| 📦 **Master Produk** | Daftar bahan makanan dengan kategori & satuan |
| 📊 **Laporan** | Filter per periode/supplier, export Excel & PDF |
| 📈 **Analitik** | Grafik pengeluaran bulanan, top supplier & produk terlaris |
| 📱 **PWA** | Installable di Android & desktop, bekerja offline |
| 🔐 **Akses Berbasis Peran** | Admin Dapur (CRUD milik sendiri) vs Kepala Divisi (view only) |

---

## 🛠️ Tech Stack

```
Backend  : Laravel 12 (PHP 8.2)
Frontend : Livewire 3 + Alpine.js + Tailwind CSS 3
Database : MySQL 8
Charts   : Chart.js 4
Export   : maatwebsite/excel + barryvdh/laravel-dompdf
PWA      : Web App Manifest + Service Worker
```

---

## 🚀 Instalasi Lokal

### Prasyarat
- PHP >= 8.2 dengan ekstensi: `mbstring`, `openssl`, `pdo_mysql`, `gd`
- Composer
- Node.js >= 18 & npm
- MySQL 8

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/adimhsd/catering-ab.git
cd catering-ab

# 2. Install dependensi PHP
composer install

# 3. Install dependensi JavaScript
npm install

# 4. Konfigurasi environment
cp .env.example .env
php artisan key:generate
```

Edit file `.env` — sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=catering_ab
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
# 5. Migrasi & seed database
php artisan migrate --seed

# 6. Buat symlink storage
php artisan storage:link

# 7. Build assets
npm run build

# 8. Jalankan server
php artisan serve
```

Buka **http://localhost:8000** di browser.

---

## 🔑 Akun Default

| Role | Email | Password |
|------|-------|----------|
| 👨‍🍳 Admin Dapur | `admin@catering.test` | `password` |
| 👔 Kepala Divisi | `kepala@catering.test` | `password` |

> ⚠️ Ganti password setelah login pertama kali di lingkungan produksi.

---

## 📸 Tampilan Aplikasi

### Halaman Login
- Desain split: panel branding islami (hijau) di kiri, form login di kanan
- Responsif untuk mobile

### Dashboard
- 4 stat cards: transaksi bulan ini, pengeluaran, supplier aktif, total produk
- Perbandingan persentase vs bulan lalu
- Tabel 5 transaksi terbaru
- Quick action buttons

### Form Input Transaksi
- Tabel item dinamis (tambah/hapus baris)
- Kalkulasi subtotal & total secara real-time
- Upload foto nota dengan preview
- Sidebar summary & tombol simpan

### Laporan & Export
- Filter: periode tanggal, supplier, produk
- Export Excel (.xlsx) dengan header berwarna
- Export PDF (A4 landscape) siap cetak

### Analitik
- Grafik bar pengeluaran 12 bulan terakhir
- Top 5 supplier & top 5 produk dengan progress bar visual
- Filter periode: bulan ini / 3 bulan / 6 bulan / tahun ini

---

## 🗂️ Struktur Database

```
users               — Admin Dapur & Kepala Divisi
suppliers           — Data mitra pemasok
product_categories  — Kategori bahan makanan
units               — Satuan ukuran (Kg, Liter, Pack, dll)
products            — Master bahan makanan
purchases           — Header transaksi pembelian
purchase_details    — Detail item per transaksi
attachments         — Lampiran foto/PDF nota
```

---

## 🧪 Menjalankan Tests

```bash
# Buat database test terlebih dahulu
mysql -u root -p -e "CREATE DATABASE catering_ab_test;"

# Jalankan unit tests
./vendor/bin/phpunit tests/Unit/PurchaseServiceTest.php --testdox
```

Hasil yang diharapkan: **10/10 tests passed** ✅

---

## 📦 Deployment ke VPS (CyberPanel)

```bash
# Di server VPS
cd /home/cateringalbahjah.qzz.io/public_html

composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run build

# Optimasi produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

> Set **Document Root** di CyberPanel ke `public_html/public`

---

## 📱 Instalasi PWA

1. Buka aplikasi di browser Chrome (Android/Desktop)
2. Akan muncul banner **"Tambahkan ke layar utama"** / **"Install app"**
3. Konfirmasi instalasi → aplikasi siap digunakan seperti app native

**Shortcuts tersedia:**
- Input Pembelian (langsung ke form transaksi baru)
- Laporan (langsung ke halaman laporan)

---

## 🔒 Hak Akses

| Fitur | Admin Dapur | Kepala Divisi |
|-------|:-----------:|:-------------:|
| Input transaksi baru | ✅ | ❌ |
| Edit/hapus transaksi sendiri | ✅ | ❌ |
| Lihat semua transaksi | ✅ | ✅ |
| Kelola supplier/produk | ✅ | ❌ |
| Lihat laporan & analitik | ✅ | ✅ |
| Export Excel/PDF | ✅ | ✅ |

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan internal **Pondok Pesantren Al-Bahjah**.  
Tidak untuk distribusi komersial.

---

<div align="center">

Dibuat dengan ❤️ untuk Pondok Pesantren Al-Bahjah

**🌿 بَارَكَ اللهُ فِيكُمْ 🌿**

</div>
