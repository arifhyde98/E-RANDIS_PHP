# 🤖 AI Handover & Architecture Guide: E-RANDIS

Dokumen ini merupakan sumber kebenaran tunggal (*Single Source of Truth*) mengenai arsitektur, jejak rekam fitur, skema database, konvensi antarmuka (UI/UX), dan aturan backend untuk sistem **E-RANDIS** (Sistem Informasi Manajemen Kendaraan Dinas Pemerintah / Bapenda Sulteng).

**Setiap agen AI yang melanjutkan pengembangan proyek ini DIWAJIBKAN membaca dokumen ini terlebih dahulu untuk menjaga konsistensi standar kode dan kelangsungan arsitektur.**

---

## 🛠️ Environment & Technology Stack
- **Framework Core:** Laravel 12 / PHP 8.2+
- **Database:** MySQL / MariaDB (Teroptimasi dengan skema B-tree Indexing)
- **Frontend / Assets:** Vite + Bootstrap 5 (Customized via SCSS tersentralisasi di `app.scss`)
- **Infrastruktur / Deployment:** Mendukung eksekusi lokal berbasis **Laragon** serta telah disiapkan konfigurasi **Docker** (`Dockerfile` & `docker-compose.yml`) untuk kemudahan kontainerisasi.

---

## 📦 Peta Fitur Penuh (*Full Feature Stack*)

### 1. Manajemen Aset Kendaraan (`VehicleController`)
- **Pencarian Publik Landing Page:** Antarmuka pencarian bagi masyarakat di rute `/` dan `/vehicle-search`. Input otomatis dibersihkan oleh `VehicleService::formatPlateNumber()` (kapitalisasi, penghapusan spasi ganda, dan filter karakter alfanumerik).
- **Impor Excel Massal (`/vehicles/import`):** Menggunakan kelas `VehicleImport` yang otomatis memetakan teks mentah instansi dan jenis kendaraan ke dalam relasi `opd_id` dan `vehicle_type_id`. Terdapat cetak biru pengembangan jangka panjang menuju **AI Smart Import** untuk pemetaan kolom dinamis tanpa templat baku.
- **Ekspor Excel (`/vehicles/export`):** Mengunduh seluruh data aset dalam format sprei terstruktur.
- **Unduh Templat Impor (`/vehicles/template`):** Mengunduh berkas acuan baku pengisian data Excel.
- **Reset Massal / Kosongkan Data (`/vehicles/truncate`):** Menghapus seluruh rekaman kendaraan secara cepat (*truncate*) untuk inisialisasi ulang basis data.

### 2. Manajemen Master Data (Hub)
- Rute terpusat (`/master-data`) untuk mengelola **Jenis Kendaraan** (`VehicleTypeController`) dan **OPD / Dinas** (`OpdController`).
- Dilengkapi fitur pembersihan otomatis (*atomic cleanup*) untuk menghapus entitas jenis kendaraan yang tidak lagi memiliki relasi aset aktif:
  ```php
  VehicleType::whereDoesntHave('vehicles')->delete();
  ```

### 3. CMS Pengaturan Web (`SettingController`)
- Antarmuka manajemen konfigurasi situs (`/settings`) untuk memodifikasi identitas web (logo, judul aplikasi, teks *footer*).
- Berkas gambar/logo diunggah secara aman ke direktori `public/uploads/settings` dengan penamaan UUID untuk mencegah bentrokan nama berkas.

---

## 🗄️ Skema Database & Relasi Kunci

### Tabel `vehicles`
Menyimpan entitas aset utama dengan arsitektur kolom ternormalisasi:
- `id` (PK, BigInt)
- `no_polisi` (String, Unique) — Nomor plat kendaraan.
- `merk`, `tipe`, `warna`, `no_rangka`, `no_mesin` — Detail fisik aset.
- `tahun_pembuatan`, `tgl_perolehan`, `nilai_perolehan` — Akuntansi aset.
- `stnk_ada`, `bpkb_ada` (String: 'Ada' / 'Tidak') — Status kelengkapan dokumen.
- `status` (String) — Status operasional (Tersedia, Dipinjam, Rusak, Maintenance).
- `opd` (String) & `pemegang` (String) — Teks penanggung jawab historis.
- **Foreign Keys:**
  - `opd_id` (Nullable FK ke `opds.id`, ON DELETE SET NULL)
  - `vehicle_type_id` (Nullable FK ke `vehicle_types.id`, ON DELETE SET NULL)

### Strategi Indeksasi Database (*Query Optimization*)
Telah diterapkan indeks lapis ganda melalui *migration* `2026_05_14_151900` untuk mencegah *Full Table Scan*:
- **B-tree Index:** Pada kolom `status`, `opd_id`, dan `vehicle_type_id`.
- **Composite Index:** Pada kombinasi `['no_polisi', 'status']` untuk kueri pencarian yang difilter.

---

## 🎨 Design System & Estetika Pemerintahan Resmi
Aplikasi **tidak menggunakan efek visual berlebihan** (*glassmorphism* pudar atau gradien mencolok) demi mengutamakan kecepatan muat, kejelasan data, dan identitas formal instansi pemerintah.

### 1. Palet & Gaya Visual
- **Skema Warna Formal:** Memprioritaskan **Navy, Putih, dan Abu-abu (Gray)** yang stabil dan profesional.
- **Batas Tabel Tajam:** Menggunakan pembatas (*border*) tabel yang tegas guna mempermudah pemindaian ribuan baris data.
- **Plat Nomor Identik:** Nomor Polisi wajib dibungkus dengan kelas `.plate-number` (font **Monospace** tebal) agar konsisten secara visual.

### 2. Standar Responsivitas Tabel Seluler (*Mobile-First UX*)
- **Sticky First Column:** Kolom pertama tabel dikunci menggunakan CSS `position: sticky` agar tidak hilang saat digeser horizontal di layar HP.
- **Responsive Column Hiding:** Kolom pelengkap disembunyikan di layar kecil melalui utilitas `d-none d-md-table-cell`.
- **Visual Swipe Hint:** Indikator *swipe* di bagian bawah tabel pada mode *mobile* sebagai panduan UX.

---

## 🧩 Standar Komponen Blade (Wajib Dipakai)
Dilarang keras menulis elemen mentah berulang. Gunakan komponen Blade berikut:

### `<x-table-card>`
Pembungkus tabel utama yang otomatis menyediakan slot pencarian, *empty state*, dan pembungkus *scroll* responsif.
```html
<x-table-card title="Daftar Kendaraan" :search="true" placeholder="Cari aset...">
    <x-slot name="actions">
        <!-- Tombol Aksi -->
    </x-slot>
    <!-- Struktur tabel standar -->
</x-table-card>
```

### `<x-modal>`
Komponen modal untuk CRUD *Single Page Interaction*, mendukung perilaku *mobile-first full-screen*, serta *header/footer* yang tetap (*sticky*).

---

## ⚙️ Backend Architecture & Aturan Validasi

### 1. Validasi Kelas Permintaan (*Form Request Validation*)
Penyimpanan dan pembaruan data wajib menggunakan kelas validasi terpisah demi menjaga keamanan dan kebersihan pengontrol:
- `StoreVehicleRequest`: Menjamin `no_polisi` unik dan atribut wajib terisi saat penambahan.
- `UpdateVehicleRequest`: Memvalidasi keunikan `no_polisi` dengan mengecualikan ID kendaraan yang sedang diperbarui (`unique:vehicles,no_polisi,' . $vehicle->id`).

### 2. Lapisan Layanan (*Service Layer*)
Logika bisnis dan kalkulasi diletakkan di dalam kelas *Service* (`VehicleService`).

### 3. Konvensi Middleware
Semua *Controller* wajib mengimplementasikan antarmuka `HasMiddleware` standar Laravel 12 dengan metode statis `middleware()`.

### 4. Pembatasan Rute Akses
Karena operasi menggunakan antarmuka *Modal*, rute halaman formulir tradisional wajib dibatasi:
```php
Route::resource('vehicles', VehicleController::class)->except(['create', 'edit', 'show']);
```

### 5. Strategi Caching
- **Statistik Dashboard:** Menggunakan 1 kueri agregasi kondisional yang di-cache via `cache()->remember('dashboard.stats', 300)` (5 menit).
- **Pengaturan Global:** Di-cache via `cache()->remember('setting.{key}', 3600)` (1 jam) dengan penghapusan otomatis (`cache()->forget`) saat diperbarui.

### 6. Standar Dokumentasi Kode (PHPDoc)
Seluruh kode backend (Models, Controllers, Services, Enums, dll) wajib memiliki dokumentasi **PHPDoc dalam Bahasa Indonesia**.
- **Kelas/Enum:** Berikan penjelasan singkat mengenai tujuan dan fungsi utama kelas tersebut.
- **Properti Model:** Gunakan anotasi `@property` untuk mendefinisikan kolom database agar *auto-complete* pada editor berfungsi optimal.
- **Metode:** Sertakan penjelasan fungsionalitas, penjelasan parameter (`@param`), dan tipe nilai kembalian (`@return`).
- **Konsistensi:** Hindari penggunaan Bahasa Inggris dalam blok komentar dokumentasi untuk menjaga keseragaman codebase.

---

## 🚨 Aturan Kritis untuk Sesi AI Berikutnya
1. **Jangan asumsikan konteks:** Selalu gunakan `view_file` untuk membaca berkas sebelum memodifikasi.
2. **Kepatuhan Desain:** Dilarang mengembalikan efek *glassmorphism* atau warna mencolok. Pertahankan gaya formal pemerintah (Navy/Putih/Gray).
3. **Penyusunan Aset:** Selalu jalankan `npm run dev` saat mengedit file SCSS atau JS.
