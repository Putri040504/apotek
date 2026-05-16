# Sistem Informasi Apotek Zema

Aplikasi web berbasis **Laravel 11** untuk manajemen apotek: stok obat, pembelian, penjualan (POS kasir), laporan, serta **algoritma FEFO (First Expired, First Out)** untuk prioritas pengeluaran obat yang paling cepat kedaluwarsa.

> Dokumen ini disusun sebagai dokumentasi proyek tugas kuliah.

---

## Daftar Isi

1. [Deskripsi Aplikasi](#deskripsi-aplikasi)
2. [Teknologi](#teknologi)
3. [Peran Pengguna](#peran-pengguna)
4. [Fitur Utama](#fitur-utama)
5. [Struktur Database](#struktur-database)
6. [Algoritma FEFO](#algoritma-fefo)
7. [Scan Barcode Kamera & Cetak Label](#scan-barcode-kamera--cetak-label)
8. [Instalasi & Menjalankan](#instalasi--menjalankan)
9. [Struktur Folder Penting](#struktur-folder-penting)

---

## Deskripsi Aplikasi

**Apotek Zema** adalah sistem informasi apotek yang membantu:

- **Admin** mengelola master data (obat, kategori, supplier, user), pembelian stok, dan melihat laporan penjualan/pembelian.
- **Kasir** melakukan transaksi penjualan melalui **layar POS (Point of Sale)** mirip minimarket: scan/cari obat, keranjang multi-item, bayar tunai atau **QRIS (Midtrans)**, cetak struk.

Salah satu aspek penting di bidang farmasi adalah pengelolaan obat berdasarkan **tanggal kedaluwarsa (expired)**. Aplikasi ini menerapkan konsep **FEFO** agar obat dengan tanggal expired terdekat diprioritaskan untuk dikeluarkan/dijual terlebih dahulu.

---

## Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.2+, Laravel 11 |
| Database | MySQL / SQLite |
| Frontend | Blade, Bootstrap 5, jQuery, DataTables |
| POS Kasir | CSS/JS khusus (`public/css/kasir-pos.css`, `public/js/kasir-pos.js`) |
| Scan barcode kamera | html5-qrcode (CDN) — `public/js/barcode-scanner.js` |
| Cetak label barcode | JsBarcode CODE128 (CDN) — `public/js/barcode-print.js` |
| Autentikasi | Laravel Breeze |
| Export laporan | Maatwebsite Excel, DomPDF |
| Pembayaran QRIS | Midtrans PHP SDK |

---

## Peran Pengguna

| Role | Akses setelah login |
|------|---------------------|
| `admin` | `/admin/dashboard` — kelola data & laporan |
| `kasir` | `/kasir/pos` — layar kasir (POS) |

---

## Fitur Utama

### Modul Admin

- Dashboard statistik (obat, supplier, transaksi, grafik bulanan)
- CRUD **Obat**, **Kategori**, **Supplier**, **User**
- **Scan barcode kamera** di data obat (cari obat) & pembelian (pilih obat otomatis)
- **Cetak label barcode** CODE128 dari `kode_obat` (untuk ditempel ke kemasan demo)
- **Pembelian** obat (keranjang + checkout, stok bertambah)
- **Laporan** penjualan & pembelian (filter, export Excel/PDF)
- Panel **Prioritas FEFO** (5 obat dengan expired terdekat)

### Modul Kasir

- **POS** (`/kasir/pos`): scan barcode (kamera HP/desktop, scanner USB, atau ketik manual), keranjang, checkout tunai & QRIS
- Dashboard monitoring (penjualan hari ini, stok menipis, obat hampir expired)
- Panel **Prioritas Pengeluaran Stok (FEFO)**
- **Riwayat penjualan** (filter bulan/tahun, detail, cetak struk, export)

### Aturan bisnis obat (POS)

- Obat **sudah expired** → tidak boleh dijual
- Obat **≤ 30 hari** sebelum expired → tidak boleh dijual (konstanta `Obat::MIN_DAYS_BEFORE_EXPIRY`)
- Stok dikurangi saat checkout **tunai** langsung; saat **QRIS** stok dikurangi setelah status pembayaran `paid`

---

## Struktur Database

| Tabel | Fungsi |
|-------|--------|
| `users` | Admin & kasir (`role`: admin / kasir) |
| `kategoris` | Kategori obat |
| `suppliers` | Data supplier |
| `obats` | Master produk (`kode_obat` unique, `nama_obat`, `harga_jual`, …) |
| `stok_batches` | Batch stok (`obat_id`, `jumlah`, `tanggal_exp`, `harga_beli`) |
| `detail_penjualan_batch` | Alokasi FEFO per baris detail penjualan |
| `pembelian`, `detail_pembelian` | Transaksi pembelian dari supplier |
| `penjualan`, `detail_penjualan` | Transaksi penjualan ke pelanggan |
| `keranjang_penjualan` | Keranjang sementara per kasir (sebelum checkout) |
| `keranjangs` | Keranjang pembelian admin |

---

## Algoritma FEFO

### Pengertian

**FEFO (First Expired, First Out)** = metode pengeluaran stok di mana barang dengan **tanggal kedaluwarsa (expired) paling awal** harus **keluar / dijual lebih dulu**, untuk meminimalkan kerugian akibat obat rusak atau tidak layak jual.

### Representasi di sistem ini (Opsi B — Batch Stok)

| Tabel | Peran |
|-------|--------|
| `obats` | **Master produk** (`kode_obat` unik, `barcode` EAN opsional, nama, harga jual) |
| `stok_batches` | **Batch stok** per tanggal expired (`obat_id` + `tanggal_exp` + `jumlah`) |
| `detail_penjualan_batch` | Audit: stok diambil dari batch mana saat jual |

Aturan unik: **`kode_obat` unique** (tidak duplikat produk).  
Obat sama dengan **expired berbeda** = **baris batch berbeda** di `stok_batches` (bukan duplikat master).

Kolom `obats.stok` dan `obats.tanggal_exp` = **ringkasan** (disinkronkan dari batch: total stok & expired FEFO terdekat).

### Pseudocode — Tampilan prioritas (Dashboard)

```
ALGORITMA PrioritasFEFO_Dashboard
INPUT  : tabel stok_batches (+ join obats)
OUTPUT : 5 batch prioritas jual

MULAI
    HASIL ← query stok_batches
        WHERE jumlah > 0
        AND tanggal_exp > hari_ini + 30 hari   // sellable
        ORDER BY tanggal_exp ASC               // FEFO
        LIMIT 5
    TAMPILKAN HASIL di dashboard
SELESAI
```

### Pseudocode — Pengurangan stok saat jual (POS)

```
ALGORITMA JualFEFO(obat, qty_jual)
INPUT  : obat_id, qty_jual
OUTPUT : alokasi per batch

MULAI
    sisa ← qty_jual
    batches ← stok_batches WHERE obat_id AND sellable
              ORDER BY tanggal_exp ASC    // FEFO

    UNTUK SETIAP batch DALAM batches HITUNG
        JIKA sisa = 0 THEN KELUAR
        ambil ← MIN(sisa, batch.jumlah)
        batch.jumlah ← batch.jumlah - ambil
        catat alokasi (batch_id, ambil)
        sisa ← sisa - ambil
    AKHIR UNTUK

    JIKA sisa > 0 THEN GAGAL "stok tidak cukup"
    update ringkasan obats.stok
SELESAI
```

### Diagram alur

```
┌──────────────────┐     ┌──────────────────┐
│  obats (master)  │────▶│  stok_batches    │
│  kode_obat UNIQUE│ 1:N │  tanggal_exp     │
└──────────────────┘     │  jumlah (stok)   │
                         └────────┬─────────┘
                                  │ ORDER BY tanggal_exp ASC
                                  ▼
                         ┌──────────────────┐
                         │  Jual / Dashboard │
                         │  FEFO             │
                         └──────────────────┘
```

### Implementasi kode — Model `StokBatch`

**File:** `app/Models/StokBatch.php`

```php
public function scopeSellable(Builder $query): Builder
{
    return $query->hasStock()->notNearExpiry();
}

public function scopeOrderFefo(Builder $query): Builder
{
    return $query->orderBy('tanggal_exp', 'asc')->orderBy('id', 'asc');
}

public static function fefoPriorities(int $limit = 5)
{
    return static::with('obat.kategori')
        ->sellable()
        ->orderFefo()
        ->limit($limit)
        ->get();
}
```

### Implementasi kode — FEFO saat penjualan (inti)

**File:** `app/Models/Obat.php` — method `decreaseStockFefo()`

```php
public function decreaseStockFefo(int $quantity): array
{
    $remaining = $quantity;
    $allocations = [];

    $batches = $this->stokBatches()
        ->sellable()
        ->orderFefo()           // batch expired terdekat dulu
        ->lockForUpdate()
        ->get();

    foreach ($batches as $batch) {
        if ($remaining <= 0) break;

        $take = min($remaining, $batch->jumlah);
        $batch->decrement('jumlah', $take);

        $allocations[] = [
            'stok_batch_id' => $batch->id,
            'jumlah' => $take,
        ];
        $remaining -= $take;
    }

    if ($remaining > 0) {
        throw new RuntimeException('Stok batch tidak mencukupi');
    }

    $this->syncFromBatches();
    return $allocations;
}
```

**File:** `app/Http/Controllers/Kasir/PenjualanController.php`

```php
protected function recordBatchSale(DetailPenjualan $detail, Obat $obat, int $quantity): void
{
    $allocations = $obat->decreaseStockFefo($quantity);

    foreach ($allocations as $row) {
        DetailPenjualanBatch::create([
            'detail_penjualan_id' => $detail->id,
            'stok_batch_id' => $row['stok_batch_id'],
            'jumlah' => $row['jumlah'],
        ]);
    }
}
```

### Implementasi kode — Pembelian (tambah batch)

**File:** `app/Models/Obat.php` — method `addBatch()`

```php
public function addBatch(int $quantity, $tanggalExp, ?int $hargaBeli = null): StokBatch
{
    $exp = Carbon::parse($tanggalExp)->toDateString();

    $batch = $this->stokBatches()->firstOrCreate(
        ['tanggal_exp' => $exp],
        ['jumlah' => 0, 'harga_beli' => $hargaBeli ?? 0]
    );

    $batch->increment('jumlah', $quantity);
    $this->syncFromBatches();

    return $batch->fresh();
}
```

Saat **pembelian**, stok masuk ke batch sesuai `tanggal_exp` di form. Jika exp sama dengan batch yang sudah ada, **qty digabung**; jika exp beda, **batch baru**.

### Dashboard FEFO

```php
// Kasir & Admin
$fifo_obat = StokBatch::fefoPriorities(5);
```

### Contoh ilustrasi (satu produk, banyak batch)

**Master:** Paracetamol — `kode_obat` = OB001 (unik)

| Batch | tanggal_exp | jumlah | Urutan jual (FEFO) |
|-------|-------------|--------|---------------------|
| 1 | 2026-04-01 | 20 | **Dijual dulu** |
| 2 | 2026-08-15 | 50 | Dijual setelah batch 1 habis |

Kasir jual 25 unit → sistem ambil 20 dari batch 1 + 5 dari batch 2 (otomatis).

---

### Aturan terkait di Model Obat (POS)

Selain FEFO tampilan, model `Obat` punya validasi penjualan agar obat bermasalah tidak masuk kasir:

**File:** `app/Models/Obat.php`

```php
public const MIN_DAYS_BEFORE_EXPIRY = 30;

/** Obat yang masih aman dijual (> 30 hari sebelum expired) */
public function scopeNotNearExpiry(Builder $query): Builder
{
    return $query->whereDate('tanggal_exp', '>', now()->addDays(self::MIN_DAYS_BEFORE_EXPIRY));
}

/** Siap dijual di kasir: ada stok + belum expired + tidak hampir expired */
public function scopeSellable(Builder $query): Builder
{
    return $query->inStock()->notNearExpiry();
}

public function daysUntilExpiry(): int
{
    return (int) now()->startOfDay()->diffInDays(
        Carbon::parse($this->tanggal_exp)->startOfDay(),
        false
    );
}

public function isExpired(): bool
{
    return $this->daysUntilExpiry() <= 0;
}

public function isNearExpiry(): bool
{
    $days = $this->daysUntilExpiry();
    return $days > 0 && $days <= self::MIN_DAYS_BEFORE_EXPIRY;
}
```

| Method / Scope | Peran |
|----------------|-------|
| `orderBy('tanggal_exp', 'asc')` | **FEFO** — prioritas tampilan |
| `scopeSellable()` | Filter obat yang **boleh** dijual di POS |
| `isExpired()` / `isNearExpiry()` | Validasi sebelum masuk keranjang |

---

### Catatan implementasi (penting untuk laporan)

1. **FEFO aktif di POS**: stok berkurang per batch urut `tanggal_exp` ASC (`decreaseStockFefo`).
2. **Anti-duplikat master**: `kode_obat` unique; batch beda exp = baris di `stok_batches`.
3. **Pembelian** wajib isi tanggal EXP → menambah/merge batch, bukan sekadar `stok++` di master.
4. Obat dengan exp ≤ 30 hari tidak bisa dijual (`scopeSellable` pada batch).

---

## Scan Barcode Kamera & Cetak Label

Fitur ini mempermudah praktik kuliah: scan obat pakai kamera HP/laptop — baik **barcode asli di kemasan (EAN)** maupun label cetak internal.

### Dua jenis kode obat

| Kolom | Contoh | Fungsi |
|-------|--------|--------|
| `kode_obat` | `OB001` | Kode internal apotek (laporan, anti-duplikat master) |
| `barcode` | `8991234567890` | Angka di kemasan (EAN-13) — **opsional**, untuk scan langsung |

Saat scan (kamera / USB), sistem mencari ke **keduanya**. Barcode kemasan hanya berisi angka; nama, harga, stok, expired tetap dari database.

### Syarat teknis

- Browser modern (Chrome / Edge / Firefox / Safari).
- **Kamera hanya aktif di `https://` atau `http://localhost` / `127.0.0.1`** — izinkan akses kamera saat browser meminta.
- Isi field **Barcode Kemasan** di form obat = angka yang sama dengan label di kemasan.

### Alur demo praktik

**Opsi A — Scan kemasan asli (disarankan untuk demo realistis):**

1. **Admin** → Tambah/Edit obat → isi **Barcode Kemasan** (scan kamera atau ketik angka EAN).
2. **Kasir** → POS → scan langsung ke barcode di botol.

**Opsi B — Label cetak internal:**

1. **Admin** → Data Obat → **UPC** → cetak label (pakai EAN jika ada, kalau tidak `OB00x`).
2. Tempel label → scan di POS.

### Penggunaan per modul

| Lokasi | Tombol / shortcut | Perilaku |
|--------|-------------------|----------|
| POS Kasir (`/kasir/pos`) | Kamera / **F3** | Scan berulang (continuous); sukses = tambah keranjang |
| POS Kasir | Input + **Enter** | Tetap mendukung ketik manual & scanner USB |
| Admin Data Obat | **Scan Cari Obat** | Filter tabel + sorot baris obat |
| Admin Data Obat | **UPC** per baris | Preview & cetak label barcode |
| Admin Pembelian | **Scan** di modal tambah | Pilih obat di dropdown otomatis |

### API lookup

| Route | Role | Response |
|-------|------|----------|
| `GET /kasir/obat/scan?kode=...&context=pos` | Kasir | JSON POS + validasi stok/jual |
| `GET /admin/obat/lookup?kode=...` | Admin | JSON master obat (`id`, `kode_obat`, `nama_obat`, `harga_beli`, …) |

Controller bersama: `app/Http/Controllers/ObatLookupController.php` — method `Obat::findByScanCode()`.

### File terkait

- `resources/views/components/barcode-scanner-modal.blade.php`
- `resources/views/components/barcode-print-modal.blade.php`
- `public/js/barcode-scanner.js`, `public/js/barcode-print.js`
- `public/css/barcode-scanner.css`, `public/css/barcode-print.css`

---

## Instalasi & Menjalankan

### Prasyarat

- PHP >= 8.2
- Composer
- MySQL atau SQLite
- Node.js & NPM (opsional, untuk asset Vite)

### Langkah

```bash
# 1. Clone / masuk folder project
cd apotek-zema-2

# 2. Install dependency PHP
composer install

# 3. Salin environment
copy .env.example .env   # Windows
# cp .env.example .env   # Linux/Mac

# 4. Generate key
php artisan key:generate

# 5. Atur database di .env (contoh SQLite)
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

# 6. Migrasi database
php artisan migrate

# 7. (Opsional) Midtrans untuk QRIS
# MIDTRANS_SERVER_KEY=...
# MIDTRANS_CLIENT_KEY=...
# MIDTRANS_IS_PRODUCTION=false

# 8. Jalankan server
php artisan serve
```

Buka browser: `http://127.0.0.1:8000`

### Akun demo

Buat user melalui registrasi atau seeder (jika ada), dengan field `role`:

- `admin` → panel admin
- `kasir` → layar POS

---

## Struktur Folder Penting

```
apotek-zema-2/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Modul admin
│   │   └── Kasir/          # POS, penjualan, dashboard kasir
│   ├── Models/
│   │   └── Obat.php        # Model obat + scope POS & validasi expired
│   └── Services/
│       └── MidtransService.php
├── database/migrations/    # Skema database
├── public/
│   ├── css/kasir-pos.css   # Tampilan POS
│   ├── css/barcode-scanner.css
│   ├── css/barcode-print.css
│   ├── js/kasir-pos.js     # Logika frontend POS
│   ├── js/barcode-scanner.js
│   └── js/barcode-print.js
├── resources/views/
│   ├── admin/              # View admin
│   └── kasir/
│       ├── pos/            # Layar kasir
│       └── data_riwayat/   # Riwayat penjualan
└── routes/web.php          # Routing aplikasi
```

---

## Ringkasan Alur Aplikasi

```
[Login] → Admin / Kasir
              │
    ┌─────────┴─────────┐
    ▼                   ▼
[Admin]             [Kasir]
- Master data       - POS (jual)
- Pembelian         - Riwayat
- Laporan           - Dashboard + FEFO
- FEFO panel
```

---

## Lisensi

Proyek tugas kuliah — Apotek Zema. PUTRI PURNAMASARI - UNIVERSITAS CIPASUNG, TASIKMALAYA
