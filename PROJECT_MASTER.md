# PROJECT MASTER DOCUMENTATION: E-RANDIS
"Dokumen ini adalah ringkasan master proyek. Untuk rincian implementasi kode, skema database lengkap, dan daftar komponen UI, WAJIB merujuk ke file AI_HANDOVER.md."
## 1. Project Overview
- **Nama Project**: E-RANDIS (Sistem Informasi Manajemen Kendaraan Dinas Pemerintah).
- **Tujuan**: Mendata, melacak, dan mengelola aset kendaraan dinas secara terpusat, real-time, dan akuntabel.
- **Permasalahan yang diselesaikan**: Pendataan aset yang berserakan, kesulitan pelacakan kondisi kendaraan fisik, dan tidak adanya standarisasi riwayat penggunaan aset lintas instansi (OPD).
- **Scope Sistem**: Manajemen pengguna (multi-role), manajemen master data instansi (OPD) dan jenis kendaraan, inventarisasi kendaraan, import/export data massal, dan *audit log* sistem.
- **User Target**: 
  - Superadmin (Developer/Root)
  - Admin BMD (Pengelola Aset Global)
  - Admin OPD (Pengelola Aset Instansi)
- **Status Project**: Production-Ready (Fase Optimasi).

---

## 2. Tech Stack
- **Framework Core**: Laravel 12
- **PHP Version**: 8.2+
- **Database**: MySQL / MariaDB
- **Asset Bundler**: Vite
- **UI Framework**: Bootstrap 5 (Customized via SCSS)
- **Package Penting**: 
  - `Maatwebsite/Excel` (Laravel Excel untuk Import/Export)
  - `SweetAlert2` (Notifikasi interaktif)
  - `Bootstrap Icons` (Ikonografi)
- **Deployment Target**: Server lokal (Laragon) atau Container (Docker ready via `compose.yaml`).

---

## 3. System Architecture
- **Arsitektur Umum**: Monolith (MVC Laravel) yang teroptimasi.
- **Pola Desain Utama**:
  - **Service Layer**: Logika bisnis kompleks dan kalkulasi *cache* diletakkan di dalam *Service* (contoh: `VehicleService`), bukan di *Controller*.
  - **Observer Pattern**: Automasi sistem dan pencatatan riwayat (Audit Log) dikendalikan penuh oleh *Eloquent Observers* (`VehicleObserver`, `OpdObserver`, `UserObserver`).
  - **Multi-Tenancy (Data Isolation)**: Menggunakan `TenantScope` (Global Scope) pada model `Vehicle` untuk mengunci data pengguna OPD agar hanya bisa melihat aset instansinya sendiri. *Fail-safe*: Jika `opd_id` null, akses otomatis terkunci total.
- **Auth & Permission Flow**: 
  - Otorisasi berbasis tipe *Enum* `UserRole`.
  - Aturan *middleware* dideklarasikan eksplisit di level *Controller* (menggunakan antarmuka `HasMiddleware` Laravel 12), bukan ditumpuk di `routes/web.php`.

---

## 4. Folder Structure
Struktur direktori disesuaikan dengan arsitektur spesifik E-RANDIS:

```text
app/
 ├── Enums/        # Menyimpan nilai statis sistem (UserRole, VehicleStatus, VehicleCondition).
 ├── Http/
 │    ├── Controllers/ # Murni untuk menangani HTTP Request & Response.
 │    └── Requests/    # (FormRequests) Sentralisasi logika validasi input.
 ├── Models/       # Model Eloquent & pendefinisian Global Scope.
 ├── Observers/    # Trigger otomatis database (Audit Log, pembuatan akun otomatis).
 └── Services/     # Logika bisnis inti dan Helper Cache.

resources/
 ├── css/          # Arsitektur Modular (7-1 Pattern):
 │    ├── abstracts/ # Variabel & Mixins.
 │    ├── base/      # Tipografi, Ikon, Scrollbar.
 │    ├── components/# Tombol, Kartu, Tabel, Badge, Statistik, Carousel.
 │    ├── layout/    # Navbar, Sidebar.
 │    ├── pages/     # Halaman Landing.
 │    ├── themes/    # Penimpaan (overrides) Mode Gelap.
 │    └── app.scss   # Titik Masuk Utama (Pusat Impor).
 ├── js/           # app.js (Pusat inisiasi library JS).
 └── views/
      ├── components/ # Blade Components yang dapat digunakan ulang.
      └── ...
```

---

## 5. Database Architecture
- **ERD Sederhana**:
  - `users` (M) -- (1) `opds` (User tergabung dalam 1 OPD).
  - `vehicles` (M) -- (1) `opds` (Kendaraan dimiliki oleh 1 OPD).
  - `vehicles` (M) -- (1) `vehicle_types` (Kendaraan memiliki 1 Tipe).
  - `activities` (M) -- (1) `users` (Riwayat log yang dilakukan User).
- **Migration & Constraint Strategy**:
  - Relasi Instansi: `opd_id` pada `users` menggunakan `onDelete('cascade')`.
  - Relasi Log/Audit: `user_id` pada `activities` menggunakan `onDelete('set null')` untuk mempertahankan riwayat meski user telah dihapus.
- **Indexing Strategy**: B-Tree Index diterapkan pada `status`, `opd_id`, dan `vehicle_type_id` untuk menghindari *Full Table Scan*.

---

## 6. Coding Convention
- **Standar Route**: Dideklarasikan bersih di `routes/web.php`. Logika izin masuk diletakkan di *Controller*.
- **Struktur Controller**: Wajib menggunakan `new Middleware('role:...')` (Standar Laravel 12). Dilarang keras menggunakan validasi *inline* `$request->validate()`; wajib menggunakan `FormRequest` khusus.
- **Dokumentasi (PHPDoc)**: Seluruh blok PHPDoc wajib ditulis menggunakan **Bahasa Indonesia** yang baku. Gunakan tag `@property` pada Model untuk *auto-complete*.
- **Enum Over Strings**: Nilai status (`Tersedia`, `Dipinjam`) dan kondisi fisik tidak di-*hardcode*, melainkan dipanggil melalui kelas `Enum`.

---

## 7. Frontend Rules
- **Design System**: Gaya formal instansi pemerintah.
- **Warna Utama**: Navy (`#1E40AF`), Putih, dan Abu-abu (Gray). Hindari gradien mencolok atau efek *glassmorphism* berlebihan.
- **Table Style**: Batas tabel wajib tegas (sharp borders). Kolom pertama wajib *sticky* di mode responsif. Penomoran otomatis berbasis paginasi.
- **Form Style**: Menggunakan antarmuka interaksi halaman tunggal (*Single Page Interaction*) dengan komponen `<x-modal>`.
- **Format Akuntansi**: Mata uang wajib berformat titik ribuan (`Rp 150.000.000`). Plat nomor wajib monospace (`.plate-number`).

---

## 8. AI Development Rules
Aturan sangat ketat bagi asisten AI yang akan membaca dan mengedit sistem ini:
- **Frontend Rules (Modular SCSS)**:
  - **DILARANG** menulis gaya kustom langsung di `app.scss`. File ini hanya untuk impor.
  - **WAJIB** menaruh gaya baru ke dalam modul yang sesuai (misal: tombol ke `_buttons.scss`, variabel ke `_variables.scss`).
  - Jika membuat halaman baru, buat partial SCSS baru di folder `pages/` dan impor di `app.scss`.
- **DILARANG membuat duplikat komponen**. Jika butuh tabel, gunakan `<x-table-card>`. Jika butuh modal CRUD, gunakan `<x-modal>`. Jika butuh kartu metrik, gunakan `<x-stat-card>`.
2. **WAJIB membaca file sebelum eksekusi**. Gunakan perintah membaca berkas (*view_file*) sebelum melakukan *update* kode.
3. **Standar Bahasa**: Seluruh interaksi, anotasi kode, pesan *commit*, dan UI wajib menggunakan **Bahasa Indonesia**.
4. **Validasi CRUD**: Seluruh operasi penambahan dan pembaruan database **WAJIB** dikawal oleh `FormRequest` terpisah.
5. **No Destructive DB Ops**: Jangan menyarankan *Soft Deletes* jika tidak ada di skema awal. Hormati arsitektur `Set Null` pada tabel Audit.

---

## 9. Existing Features
Status implementasi fitur utama sistem.

| Feature | Status | Notes |
|---|---|---|
| Autentikasi & Multi-Role | DONE | Stabil dengan `TenantScope` |
| Dashboard & Metrik | DONE | Menggunakan *targeted cache invalidation* |
| Master Data (OPD & Jenis) | DONE | Mendukung *atomic cleanup* |
| Kendaraan (CRUD) | DONE | UI Modal tersentralisasi & validasi ketat |
| Import Excel Kendaraan | DONE | Automasi relasi & *Batch Insert* teroptimasi |
| Audit Trail (Log Sistem) | DONE | Hanya dapat diakses Superadmin |
| CMS Pengaturan Global | DONE | Tersimpan di *Cache* |

---

## 10. Roadmap
### Phase 1: Foundation (Selesai)
- Sistem otorisasi, arsitektur *Tenant*, dan manajemen aset dasar.
### Phase 2: Optimization (Selesai)
- Peningkatan performa kueri (*indexing*), UI responsif, pembersihan kode, dan standarisasi *FormRequest*.
### Phase 3: Future Expansion (Mendatang)
- **AI Smart Import**: Pemetaan dinamis header Excel berbasis AI agar tidak terkunci pada satu format *template* baku.

---

## 11. Known Problems
- Secara struktural sudah stabil. Area yang perlu diperhatikan di masa depan adalah pengelolaan file fisik (*storage*) foto kendaraan agar tidak membebani kapasitas *disk* (meski saat ini telah dibatasi maksimal 4 foto & metode *Replace All*).

---

## 12. Deployment
- **Environment**: Konfigurasi kunci ada di `.env` (pastikan DB terkoneksi).
- **Storage**: Wajib menjalankan `php artisan storage:link` agar foto tampil.
- **Build Command**: Wajib menjalankan `npm run build` setelah mengubah SCSS.
- **Cache**: Setelah pembaruan *production*, jalankan `php artisan optimize:clear` untuk me-reset cache `dashboard.stats` dan `setting`.

---

## 13. AI Context Summary
**TL;DR for AI Agents**: 
Sistem ini adalah **E-RANDIS**, aplikasi manajemen aset kendaraan berbasis **Laravel 12**. Aplikasi ini mengedepankan keamanan berlapis (*Multi-tenant* dengan `TenantScope`), logika otomatisasi via **Observer**, dan memisahkan logika bisnis melalui **Service Layer**. Antarmuka dibangun dengan **Bootstrap 5 + Custom SCSS** bernuansa formal (Navy/Putih). Seluruh kode backend **wajib dikomentari menggunakan Bahasa Indonesia**. Saat melakukan *coding*, pastikan selalu menggunakan kembali komponen Blade (seperti `<x-modal>`) dan `FormRequest` untuk validasi. Patuhi standar ini demi keberlangsungan sistem.
