# 🧪 Tutorial Pengujian Lokal — Catering Al-Bahjah

> Ikuti panduan ini **satu per satu** sebelum melakukan deployment ke VPS.  
> Pastikan semua poin **✅ lulus** sebelum lanjut ke deploy.

---

## 📦 1. Persiapan Environment

### 1.1 Jalankan Server Laravel

Buka terminal di folder project, jalankan dua perintah di **dua terminal terpisah**:

**Terminal 1 — Backend (Laravel):**
```bash
cd /home/adi/Workspace/catering-ab
php artisan serve
```
> Server berjalan di: **http://localhost:8000**

**Terminal 2 — Frontend (Vite dev server, untuk hot-reload CSS/JS):**
```bash
cd /home/adi/Workspace/catering-ab
npm run dev
```
> Vite berjalan di: **http://localhost:5173** (otomatis terhubung ke Laravel)

### 1.2 Verifikasi Environment

Buka tab ketiga di terminal, jalankan:
```bash
cd /home/adi/Workspace/catering-ab

# Cek status database
php artisan migrate:status

# Cek symlink storage (harus ada)
ls -la public/storage

# Cek apakah seeder sudah jalan
php artisan tinker --execute="echo \App\Models\User::count() . ' users, ' . \App\Models\Supplier::count() . ' suppliers, ' . \App\Models\Product::count() . ' products';"
```

**Hasil yang diharapkan:**
- Semua migration: `Ran` ✅
- `public/storage` → symlink ke `storage/app/public` ✅
- Output: `2 users, 3 suppliers, 15 products` ✅

Jika seeder belum jalan:
```bash
php artisan migrate:fresh --seed
```

---

## 🔐 2. Pengujian Autentikasi

Buka browser → **http://localhost:8000**

### 2.1 Test Login sebagai Admin Dapur

| Field | Value |
|-------|-------|
| Email | `admin@catering.test` |
| Password | `password` |

**Yang harus dicek setelah login:**
- [ ] Redirect ke halaman **Dashboard** ✅
- [ ] Sidebar tampil dengan menu lengkap ✅
- [ ] Nama user muncul di pojok kanan atas ✅
- [ ] Menu **"Input Pembelian"** tampil di sidebar ✅

### 2.2 Test Login sebagai Kepala Divisi

Logout dulu → klik nama di pojok kanan atas → Log Out

| Field | Value |
|-------|-------|
| Email | `kepala@catering.test` |
| Password | `password` |

**Yang harus dicek:**
- [ ] Login berhasil masuk ke Dashboard ✅
- [ ] Menu **"Input Pembelian"** **TIDAK** tampil (sesuai RBAC) ✅
- [ ] Menu Laporan dan Analitik tetap tampil ✅

Logout dan login kembali sebagai **Admin Dapur** untuk test selanjutnya.

---

## 📊 3. Pengujian Dashboard

URL: **http://localhost:8000/dashboard**

- [ ] 4 stat cards tampil: Transaksi, Pengeluaran, Supplier Aktif, Total Produk ✅
- [ ] Tabel "Transaksi Terbaru" tampil (kosong jika belum ada transaksi, OK) ✅
- [ ] Tombol "Input Pembelian Baru" berfungsi → arahkan ke form ✅

---

## 🏪 4. Pengujian Master Data Supplier

URL: **http://localhost:8000/supplier**

### 4.1 Cek Data Seeder
- [ ] Tabel menampilkan minimal 3 supplier dari seeder ✅
- [ ] Kolom Nama, PIC, WA, Status tampil dengan benar ✅

### 4.2 Tambah Supplier Baru
Klik tombol **"Tambah Supplier"** → isi form:
- Nama Supplier: `CV. Test Lokal`
- PIC: `Budi Santoso`
- No. WhatsApp: `081234567890`
- Alamat: `Jl. Test No. 1, Cirebon`

Klik **Simpan**
- [ ] Muncul notifikasi sukses hijau di atas ✅
- [ ] Supplier baru tampil di tabel ✅

### 4.3 Edit Supplier
Klik ikon ✏️ pada supplier yang baru dibuat → ubah nama → Simpan
- [ ] Data berhasil berubah di tabel ✅

### 4.4 Toggle Status
Klik toggle **Aktif/Nonaktif** pada supplier
- [ ] Badge status berubah (Aktif ↔ Nonaktif) ✅

### 4.5 Hapus Supplier
Klik ikon 🗑️ → konfirmasi hapus
- [ ] Supplier terhapus dari tabel ✅

---

## 📦 5. Pengujian Master Data Produk

URL: **http://localhost:8000/produk**

### 5.1 Cek Data Seeder
- [ ] Minimal 15 produk tampil dari seeder ✅
- [ ] Filter kategori berfungsi → dropdown kategori → data ter-filter ✅

### 5.2 Tambah Produk Baru
Klik **"Tambah Produk"** → isi:
- Nama Produk: `Bawang Putih Test`
- Kategori: `Bumbu`
- Satuan: `Kg`

Klik **Simpan**
- [ ] Produk baru tampil di tabel ✅

---

## 📝 6. Pengujian Transaksi Pembelian (CORE)

> Ini adalah fitur terpenting — uji dengan teliti!

URL: **http://localhost:8000/pembelian**

### 6.1 Halaman Daftar Transaksi
- [ ] Halaman tabel kosong tampil normal dengan empty state ✅
- [ ] Tombol "Input Pembelian" tampil (Admin Dapur) ✅

### 6.2 Buat Transaksi Baru

Klik **"Input Pembelian"** → URL: **http://localhost:8000/pembelian/buat**

**Isi form header:**
- Supplier: pilih salah satu dari dropdown
- Tanggal: hari ini (sudah terisi otomatis)
- Catatan: `Test transaksi pertama`

**Isi baris item pertama:**
- Produk: `Bawang Merah` (atau pilih dari dropdown)
- Qty: `5`
- Harga Satuan: `20000`

**Cek kalkulasi otomatis:**
- [ ] Subtotal baris = **Rp 100.000** (5 × 20.000) — tanpa klik apapun ✅

**Tambah baris ke-2:**
Klik **"Tambah Baris Item"**
- Produk: pilih produk lain
- Qty: `2`
- Harga: `50000`

- [ ] Subtotal baris ke-2 = **Rp 100.000** ✅
- [ ] Total Keseluruhan (sidebar) = **Rp 200.000** ✅

**Upload foto nota (opsional):**
Klik area upload → pilih file gambar (JPG/PNG) atau PDF di komputer
- [ ] Preview gambar muncul setelah dipilih ✅

**Simpan:**
Klik **"Simpan Transaksi"**
- [ ] Loading spinner muncul saat menyimpan ✅
- [ ] Redirect ke halaman daftar transaksi ✅
- [ ] Notifikasi sukses muncul ✅
- [ ] Nomor transaksi format `PB-YYYYMMDD-001` tampil di tabel ✅

### 6.3 Lihat Detail Transaksi

Klik nomor transaksi atau ikon 👁️
- [ ] Halaman detail tampil dengan info header ✅
- [ ] Tabel detail item dengan subtotal per baris ✅
- [ ] Total keseluruhan di footer tabel ✅
- [ ] Lampiran foto nota tampil (jika diupload) ✅

### 6.4 Edit Transaksi

Klik ikon ✏️ dari daftar atau halaman detail
- [ ] Form terisi dengan data yang sudah ada ✅
- [ ] Bisa tambah/hapus baris item ✅
- [ ] Total otomatis terupdate ✅
- [ ] Lampiran lama tampil dengan tombol hapus ✅
- [ ] Klik **"Simpan Perubahan"** → berhasil ✅

### 6.5 Buat Transaksi Ke-2

Ulangi langkah 6.2 dengan data berbeda untuk keperluan laporan.

### 6.6 Test RBAC — Logout & Login Kepala Divisi

Login sebagai `kepala@catering.test`:
- [ ] Tombol **Edit** dan **Hapus** **TIDAK** tampil di daftar ✅
- [ ] Halaman detail bisa diakses (view only) ✅
- [ ] Akses paksa ke `/pembelian/buat` → redirect atau 403 ✅

Login kembali sebagai Admin Dapur untuk lanjut.

---

## 📊 7. Pengujian Laporan

URL: **http://localhost:8000/laporan**

### 7.1 Filter Laporan
- [ ] Default filter: bulan ini — data transaksi yang tadi dibuat tampil ✅
- [ ] Ubah filter **Tanggal Dari** → data berubah ✅
- [ ] Ubah filter **Supplier** → data ter-filter ✅
- [ ] Ringkasan: "Total Transaksi" dan "Total Pengeluaran" update sesuai filter ✅

### 7.2 Export Excel
Klik tombol **"Export Excel"** (hijau tosca)
- [ ] File `.xlsx` terdownload otomatis ✅
- [ ] Buka file → data sesuai filter aktif ✅
- [ ] Header baris pertama berwarna hijau ✅

### 7.3 Export PDF
Klik tombol **"Export PDF"** (merah)
- [ ] File `.pdf` terdownload / terbuka di tab baru ✅
- [ ] Layout A4 landscape dengan header islami ✅
- [ ] Data sesuai filter aktif ✅
- [ ] Total di bagian bawah tabel ✅

---

## 📈 8. Pengujian Analitik

URL: **http://localhost:8000/analitik**

### 8.1 Grafik Pengeluaran
- [ ] Grafik bar Chart.js tampil (mungkin kosong jika baru ada sedikit data) ✅
- [ ] Label bulan di sumbu X ✅
- [ ] Hover tooltip menampilkan nilai Rupiah ✅

### 8.2 Filter Periode
Klik tombol **"3 Bulan"**, **"6 Bulan"**, **"Tahun Ini"**
- [ ] Data berubah sesuai periode yang dipilih ✅

### 8.3 Top Supplier & Produk
- [ ] Card "Top 5 Supplier" menampilkan data ✅
- [ ] Progress bar proporsional ✅
- [ ] Card "Top 5 Produk" menampilkan data ✅

---

## 📱 9. Pengujian PWA

### 9.1 Cek Manifest
Buka DevTools (F12) → tab **Application** → **Manifest**
- [ ] App name: `Catering Al-Bahjah` ✅
- [ ] Theme color: `#0f4c35` (hijau gelap) ✅
- [ ] Icons 192×192 dan 512×512 tampil ✅
- [ ] Start URL: `/dashboard` ✅

### 9.2 Cek Service Worker
DevTools → **Application** → **Service Workers**
- [ ] `sw.js` terdaftar dan status **activated and is running** ✅

### 9.3 Installable
DevTools → **Application** → **Manifest** → scroll ke bawah
- [ ] Tidak ada error manifest ✅
- Di address bar Chrome: cek ikon install (⊕) muncul ✅

---

## 🧪 10. Jalankan Unit Tests

```bash
cd /home/adi/Workspace/catering-ab
./vendor/bin/phpunit tests/Unit/PurchaseServiceTest.php --testdox
```

**Hasil yang diharapkan — 10/10 lulus:**
```
Purchase Service (Tests\Unit\PurchaseService)
 ✔ Subtotal dihitung dengan benar
 ✔ Subtotal dengan harga nol adalah nol
 ✔ Total dihitung dari semua item
 ✔ Total dengan array kosong adalah nol
 ✔ Total mengabaikan item tanpa qty atau harga
 ✔ Nomor transaksi pertama berformat benar
 ✔ Nomor transaksi increment per tanggal
 ✔ Nomor transaksi tanggal berbeda mulai dari 001
 ✔ Simpan membuat transaksi dengan detail yang benar
 ✔ Hapus menghapus transaksi dan detail

OK, but there were issues!
Tests: 10, Assertions: 20
```

> Catatan: `PHPUnit Deprecations` di output adalah peringatan non-kritis, **bukan error**.

---

## 📱 11. Pengujian Responsif Mobile

Di browser Chrome, tekan **F12** → klik ikon 📱 (Toggle Device Toolbar) → pilih **iPhone 12** atau **Samsung Galaxy S20**

Cek halaman berikut di mode mobile:
- [ ] **Login** — form terpusat, mudah diisi ✅
- [ ] **Dashboard** — stat cards stack vertikal ✅
- [ ] **Daftar Transaksi** — tabel bisa di-scroll horizontal ✅
- [ ] **Form Input Transaksi** — tabel item bisa di-scroll, tombol simpan mudah dijangkau ✅
- [ ] **Sidebar** — collapse menjadi hamburger menu ✅, klik hamburger → sidebar muncul ✅

---

## ✅ Checklist Akhir Sebelum Deploy

Setelah semua pengujian di atas selesai, centang checklist ini:

```
FUNGSIONALITAS
[ ] Login/logout Admin Dapur dan Kepala Divisi berfungsi
[ ] RBAC: Kepala Divisi tidak bisa create/edit/delete
[ ] Master data: CRUD Supplier, Produk, Kategori berfungsi
[ ] Transaksi: Create dengan multi-item dan upload nota
[ ] Transaksi: Edit milik sendiri, tidak bisa edit milik orang lain
[ ] Laporan: Filter berubah, export Excel & PDF berhasil download
[ ] Analitik: Grafik tampil, filter periode berfungsi

KUALITAS
[ ] 10/10 unit tests passed
[ ] Tidak ada error merah di browser console (F12)
[ ] Responsive di mobile (Chrome DevTools)
[ ] PWA installable (manifest + service worker aktif)

KEAMANAN
[ ] File .env tidak ter-commit ke GitHub
[ ] storage/app/public dapat menyimpan file upload
[ ] Nomor transaksi auto-generate tidak duplikat
```

---

## ⚡ Quick Start (Jika Setup Baru / Setelah Clone)

```bash
# Clone repository
git clone https://github.com/adimhsd/catering-ab.git
cd catering-ab

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env — sesuaikan DB_*
nano .env

# Setup database
php artisan migrate --seed
php artisan storage:link

# Jalankan
npm run build          # untuk production assets
php artisan serve      # http://localhost:8000
```

---

> 📌 Jika semua checklist ✅, kode siap untuk di-deploy ke VPS!  
> Lanjut ke bagian **Deployment** di [walkthrough.md](file:///home/adi/.gemini/antigravity-ide/brain/944bce17-80db-4b4d-bbab-ad5f4810623d/walkthrough.md)
