# 🤖 AI Handover & Architecture Guide: E-RANDIS

Dokumen ini merupakan sumber kebenaran tunggal (*Single Source of Truth*) mengenai arsitektur, jejak rekam fitur, skema database, konvensi antarmuka (UI/UX), dan aturan backend untuk sistem **E-RANDIS** (Sistem Informasi Manajemen Kendaraan Dinas Pemerintah / Bapenda Sulteng).

**Setiap agen AI yang melanjutkan pengembangan proyek ini DIWAJIBKAN membaca dokumen ini terlebih dahulu untuk menjaga konsistensi standar kode dan kelangsungan arsitektur.**

---

## 1. 🛠️ Environment & Technology Stack
- **Framework Core:** Laravel 12 / PHP 8.2+
- **Database:** MySQL / MariaDB (Teroptimasi dengan skema B-tree Indexing)
- **Frontend / Assets:** 
  - **Bundler:** Vite
  - **UI Framework:** Bootstrap 5 (Customized via SCSS tersentralisasi di `app.scss`)
  - **Iconography:** Bootstrap Icons (Local via NPM/Vite)
  - **Notifications:** SweetAlert2 (Local via NPM/Vite) untuk peringatan, validasi *real-time*, & konfirmasi aksi
  - **Typography:** Plus Jakarta Sans (Local via @fontsource)
- **Data Engine:** Laravel Excel (Maatwebsite/Excel) sebagai mesin utama pengolahan Impor & Ekspor data massal.
- **Infrastruktur / Deployment:** Mendukung eksekusi lokal berbasis **Laragon** serta telah disiapkan konfigurasi **Docker** (`Dockerfile` & `docker-compose.yml`) untuk kemudahan kontainerisasi.

---

## 2. 🔐 Autentikasi & Akses Kontrol
- **Sistem Login:** Menggunakan `Auth::routes()` bawaan Laravel (Login, Register, Reset Password).
- **Model Akses:** Saat ini menggunakan skema **single-role** — semua user yang terautentikasi memiliki hak akses penuh sebagai Admin.
- **Proteksi Middleware:** Seluruh rute di dalam grup `Route::middleware('auth')` hanya dapat diakses setelah login.
- **Halaman Publik:** Hanya Landing Page (`/`) dan endpoint pencarian AJAX (`/vehicle-search`) yang dapat diakses tanpa login.
- **Catatan:** Belum diimplementasikan sistem *Role-Based Access Control* (RBAC). Jika diperlukan di masa depan, pertimbangkan penggunaan package seperti `spatie/laravel-permission`.

---

## 3. 🗄️ Skema Database & Relasi Kunci

### Tabel `vehicles`
Menyimpan entitas aset utama dengan arsitektur kolom ternormalisasi:
- `id` (PK, BigInt)
- `no_polisi` (String, Unique) — Nomor plat kendaraan.
- `merk`, `tipe`, `warna`, `no_rangka`, `no_mesin` — Detail fisik aset.
- `tahun_pembuatan`, `tgl_perolehan`, `nilai_perolehan` — Akuntansi aset.
- `stnk_ada`, `bpkb_ada` (String: 'Ada' / 'Tidak') — Status kelengkapan dokumen.
- `status` (String) — Status operasional (Tersedia, Dipinjam, Nonaktif).
- `kondisi` (String) — Kondisi fisik kendaraan (Baik, Rusak Ringan, Rusak Berat, Hilang, Dalam Penelusuran).
- `opd` (String) & `pemegang` (String) — Teks penanggung jawab historis.
- **Foreign Keys:**
  - `opd_id` (Nullable FK ke `opds.id`, ON DELETE SET NULL)
  - `vehicle_type_id` (Nullable FK ke `vehicle_types.id`, ON DELETE SET NULL)

### Tabel `vehicle_types`
Master data klasifikasi jenis kendaraan:
- `id` (PK, BigInt)
- `name` (String, Unique) — Nama tipe/kategori kendaraan.
- `description` (Text, Nullable) — Deskripsi opsional.

### Tabel `opds`
Master data Organisasi Perangkat Daerah (instansi pemerintah):
- `id` (PK, BigInt)
- `nama` (String, Unique) — Nama lengkap instansi.
- `singkatan` (String, Nullable) — Singkatan resmi instansi.
- `alamat` (Text, Nullable) — Alamat kantor.

### Tabel `settings`
Konfigurasi CMS yang dapat diubah melalui antarmuka admin:
- `id` (PK, BigInt)
- `key` (String, Unique) — Kunci pengaturan (misal: `app_name`, `logo`).
- `value` (Text, Nullable) — Nilai pengaturan.
- `type` (String, Default: 'text') — Tipe input: `text`, `image`, `textarea`.
- `group` (String, Default: 'general') — Grup pengelompokan: `general`, `landing`, `login`.

### Strategi Indeksasi Database (*Query Optimization*)
Telah diterapkan indeks lapis ganda melalui *migration* `2026_05_14_151900` untuk mencegah *Full Table Scan*:
- **B-tree Index:** Pada kolom `status`, `opd_id`, dan `vehicle_type_id`.
- **Composite Index:** Pada kombinasi `['no_polisi', 'status']` untuk kueri pencarian yang difilter.

---

## 4. ⚙️ Backend Architecture & Aturan Validasi

### Lapisan Layanan (*Service Layer*)
Logika bisnis dan kalkulasi diletakkan di dalam kelas *Service* (`VehicleService`).

### Validasi Kelas Permintaan (*Form Request Validation*)
Penyimpanan dan pembaruan data wajib menggunakan kelas validasi terpisah demi menjaga keamanan dan kebersihan pengontrol:
- `StoreVehicleRequest`: Menjamin `no_polisi` unik dan atribut wajib terisi saat penambahan.
- `UpdateVehicleRequest`: Memvalidasi keunikan `no_polisi` dengan mengecualikan ID kendaraan yang sedang diperbarui (`unique:vehicles,no_polisi,' . $vehicle->id`).

### Konvensi Middleware & Akses Rute
- Semua *Controller* wajib mengimplementasikan antarmuka `HasMiddleware` standar Laravel 12 dengan metode statis `middleware()`.
- Karena operasi menggunakan antarmuka *Modal*, rute halaman formulir tradisional wajib dibatasi:
```php
Route::resource('vehicles', VehicleController::class)->except(['create', 'edit', 'show']);
```

### Standar Kode & Enum
Aplikasi menggunakan Enum (PHP 8.1+) untuk menjaga integritas data:
- `VehicleStatus`: Mengelola status operasional (Tersedia, Dipinjam, Nonaktif).
- `VehicleCondition`: Mengelola kondisi fisik, dilengkapi method `fromImport()` untuk normalisasi singkatan Excel (RB, RR, B, dll).

### Logika Smart Import (Normalisasi)
Sistem melakukan pembersihan data otomatis saat import Excel:
1. **Penerjemahan Singkatan**: Mengonversi variasi teks mentah (misal: "RB", "RR", "B") menjadi standar "Rusak Berat", "Rusak Ringan", dsb.
2. **Penentuan Status Otomatis**: Jika kondisi fisik adalah 'Rusak Berat' atau 'Hilang', sistem otomatis mengatur status operasional ke 'Nonaktif'.

### Strategi Caching
- **Statistik Dashboard**: Menggunakan 1 kueri agregasi kondisional yang di-cache via `cache()->remember('dashboard.stats', 300)` (5 menit).
- **Cache Invalidation**: Cache wajib dihapus (`cache()->forget('dashboard.stats')`) setiap kali terjadi operasi **Store, Update, Destroy, Import,** atau **Truncate** pada data kendaraan.
- **Pengaturan Global**: Di-cache via `cache()->remember('setting.{key}', 3600)` (1 jam) dengan penghapusan otomatis (`cache()->forget`) saat data diperbarui.

---

## 5. 🎨 Design System, Estetika & Standar UI
Aplikasi **tidak menggunakan efek visual berlebihan** (*glassmorphism* pudar atau gradien mencolok) demi mengutamakan kecepatan muat, kejelasan data, dan identitas formal instansi pemerintah.

### Palet & Gaya Visual
- **Skema Warna Formal:** Memprioritaskan **Navy, Putih, dan Abu-abu (Gray)** yang stabil dan profesional.
- **Batas Tabel Tajam:** Menggunakan pembatas (*border*) tabel yang tegas guna mempermudah pemindaian ribuan baris data.
- **Plat Nomor Identik:** Nomor Polisi wajib dibungkus dengan kelas `.plate-number` (font **Monospace** tebal) agar konsisten secara visual.
- **Format Akuntansi**: Seluruh tampilan mata uang (seperti `nilai_perolehan`) wajib menggunakan format titik ribuan (Contoh: Rp 150.000.000). 
  - Di Blade: `number_format($val, 0, ',', '.')`.
  - Di JavaScript (Modal): `.toLocaleString('id-ID')`.
- **Bahasa Antarmuka**: Seluruh notifikasi, pesan kesalahan (validation errors), peringatan (warnings), dan label UI **WAJIB** menggunakan Bahasa Indonesia yang profesional dan mudah dimengerti.

### Standar Responsivitas Tabel Seluler (*Mobile-First UX*)
- **Sticky First Column:** Kolom pertama tabel dikunci menggunakan CSS `position: sticky` agar tidak hilang saat digeser horizontal di layar HP.
- **Responsive Column Hiding:** Kolom pelengkap disembunyikan di layar kecil melalui utilitas `d-none d-md-table-cell`.
- **Visual Swipe Hint:** Indikator *swipe* di bagian bawah tabel pada mode *mobile* sebagai panduan UX.

### Standar Tampilan Tabel & Komponen Reusable
- **Paginasi Global**: Menggunakan `Paginator::useBootstrapFive()` di `AppServiceProvider` untuk memastikan template navigasi yang bersih dan konsisten.
- **Penomoran Tabel**: Menggunakan `$loop->iteration` yang dikombinasikan dengan metadata paginasi: `($collection->currentPage() - 1) * $collection->perPage() + $loop->iteration`.
- **Komponen Lainnya**: Menggunakan `x-modal` sebagai shell modal, `x-stat-card` untuk kartu statistik, dan `x-condition-badge` untuk label kondisi.

### Standar Komponen Blade (Wajib Dipakai)
Dilarang keras menulis elemen mentah berulang. Gunakan komponen Blade berikut:

#### `<x-table-card>`
Pembungkus tabel utama yang otomatis menyediakan slot pencarian, *empty state*, dan pembungkus *scroll* responsif.
- Wajib mengirimkan `:collection="$data"` agar informasi *"Menampilkan X sampai Y dari Z data"* muncul secara otomatis.
- Slot `:pagination` digunakan untuk merender tombol navigasi `{{ $data->links() }}`.
```html
<x-table-card title="Daftar Kendaraan" :search="true" placeholder="Cari aset...">
    <x-slot name="actions">
        <!-- Tombol Aksi -->
    </x-slot>
    <!-- Struktur tabel standar -->
</x-table-card>
```

#### `<x-modal>`
Komponen modal untuk CRUD *Single Page Interaction*, mendukung perilaku *mobile-first full-screen*, serta *header/footer* yang tetap (*sticky*).

### Penanganan Foto Kendaraan
- Maksimal 4 foto per kendaraan disimpam dalam kolom JSON `foto_kendaraan`.
- Logika Edit menggunakan **Replace All**: Mengunggah foto baru akan menghapus seluruh foto lama.
- Foto disimpan di disk `public/vehicles`. Wajib menjalankan `php artisan storage:link`.

---

## 6. 📦 Peta Fitur Penuh (*Full Feature Stack*)

### Manajemen Aset Kendaraan (`VehicleController`)
- **Pencarian Publik Landing Page:** Antarmuka pencarian bagi masyarakat di rute `/` dan `/vehicle-search`. Input otomatis dibersihkan oleh `VehicleService::formatPlateNumber()` (kapitalisasi, penghapusan spasi ganda, dan filter karakter alfanumerik).
- **Impor Excel Massal (`/vehicles/import`):** Menggunakan kelas `VehicleImport` yang otomatis memetakan teks mentah instansi dan jenis kendaraan ke dalam relasi `opd_id` dan `vehicle_type_id`. Terdapat cetak biru pengembangan jangka panjang menuju **AI Smart Import** untuk pemetaan kolom dinamis tanpa templat baku.
- **Ekspor Excel (`/vehicles/export`):** Mengunduh seluruh data aset dalam format sprei terstruktur.
- **Unduh Templat Impor (`/vehicles/template`):** Mengunduh berkas acuan baku pengisian data Excel.
- **Reset Massal / Kosongkan Data (`/vehicles/truncate`):** Menghapus seluruh rekaman kendaraan secara cepat (*truncate*) untuk inisialisasi ulang basis data.

### Manajemen Master Data (Hub)
- Rute terpusat (`/master-data`) untuk mengelola **Jenis Kendaraan** (`VehicleTypeController`) dan **OPD / Dinas** (`OpdController`).
- Dilengkapi fitur pembersihan otomatis (*atomic cleanup*) untuk menghapus entitas jenis kendaraan yang tidak lagi memiliki relasi aset aktif:
  ```php
  VehicleType::whereDoesntHave('vehicles')->delete();
  ```

### CMS Pengaturan Web (`SettingController`)
- Antarmuka manajemen konfigurasi situs (`/settings`) untuk memodifikasi identitas web (logo, judul aplikasi, teks *footer*).
- Berkas gambar/logo diunggah secara aman ke direktori `public/uploads/settings` dengan penamaan UUID untuk mencegah bentrokan nama berkas.

---

## 7. 🗺️ Peta Rute Aplikasi (*Route Map*)

| Metode | URI | Controller@Method | Akses | Keterangan |
|--------|-----|-------------------|-------|------------|
| GET | `/` | `VehicleController@search` | Publik | Landing page + pencarian |
| GET | `/vehicle-search` | `VehicleController@searchLandingVehicle` | Publik | API AJAX pencarian kendaraan |
| GET | `/home` | `HomeController@index` | Auth | Dashboard admin |
| GET | `/vehicles` | `VehicleController@index` | Auth | Daftar kendaraan + filter |
| POST | `/vehicles` | `VehicleController@store` | Auth | Simpan kendaraan baru |
| PUT | `/vehicles/{id}` | `VehicleController@update` | Auth | Perbarui data kendaraan |
| DELETE | `/vehicles/{id}` | `VehicleController@destroy` | Auth | Hapus kendaraan |
| GET | `/vehicles/export` | `VehicleController@export` | Auth | Ekspor Excel |
| GET | `/vehicles/template` | `VehicleController@downloadTemplate` | Auth | Unduh template import |
| POST | `/vehicles/import` | `VehicleController@import` | Auth | Import dari Excel |
| POST | `/vehicles/truncate` | `VehicleController@truncate` | Auth | Kosongkan seluruh data |
| GET | `/master-data` | `MasterDataController@index` | Auth | Hub master data |
| Resource | `/vehicle-types` | `VehicleTypeController` | Auth | CRUD tipe kendaraan |
| POST | `/vehicle-types/cleanup` | `VehicleTypeController@cleanup` | Auth | Bersihkan tipe kosong |
| Resource | `/opds` | `OpdController` | Auth | CRUD data OPD |
| GET | `/settings` | `SettingController@index` | Auth | Halaman pengaturan |
| POST | `/settings` | `SettingController@update` | Auth | Simpan pengaturan |

---

## 8. 📚 Standar Dokumentasi Kode (PHPDoc)
Seluruh kode backend (Models, Controllers, Services, Enums, dll) wajib memiliki dokumentasi **PHPDoc dalam Bahasa Indonesia**.
- **Kelas/Enum:** Berikan penjelasan singkat mengenai tujuan dan fungsi utama kelas tersebut.
- **Properti Model:** Gunakan anotasi `@property` untuk mendefinisikan kolom database agar *auto-complete* pada editor berfungsi optimal.
- **Metode:** Sertakan penjelasan fungsionalitas, penjelasan parameter (`@param`), dan tipe nilai kembalian (`@return`).
- **Konsistensi:** Hindari penggunaan Bahasa Inggris dalam blok komentar dokumentasi untuk menjaga keseragaman codebase.

**Contoh Format Standar:**
```php
/**
 * Mendapatkan statistik dashboard untuk kendaraan.
 * 
 * Data di-cache selama 5 menit untuk performa optimal.
 * 
 * @param string|null $query Kata kunci pencarian
 * @return array{total: int, available: int, damaged: int}
 */
```

---

## 9. 🚨 Aturan Kritis untuk Sesi AI Berikutnya
1. **Jangan asumsikan konteks:** Selalu gunakan `view_file` untuk membaca berkas sebelum memodifikasi.
2. **Kepatuhan Desain:** Dilarang mengembalikan efek *glassmorphism* atau warna mencolok. Pertahankan gaya formal pemerintah (Navy/Putih/Gray).
3. **Penyusunan Aset:** Selalu jalankan `npm run dev` saat mengedit file SCSS atau JS.
4. **Bahasa Indonesia Wajib:** Seluruh dokumentasi kode (PHPDoc, komentar inline, pesan commit) dan komunikasi pengembangan wajib menggunakan **Bahasa Indonesia** secara konsisten.
5. **Jangan eksekusi tanpa persetujuan:** Jika user meminta perubahan pada area spesifik, jangan memperluas cakupan ke file lain tanpa konfirmasi terlebih dahulu.
