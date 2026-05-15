# E-RANDIS

E-RANDIS (Enterprise Portal Manajemen Kendaraan Dinas) adalah aplikasi berbasis web yang dikembangkan menggunakan Laravel 12. Aplikasi ini digunakan untuk mengelola, mencatat, dan memonitor data kendaraan operasional/dinas di berbagai instansi atau Organisasi Perangkat Daerah (OPD). Aplikasi ini juga dilengkapi dengan fitur pencarian publik, import/export data massal menggunakan Excel, serta pengaturan CMS mandiri.

## 🏗️ Arsitektur & Struktur Folder
Aplikasi ini menggunakan pola arsitektur **MVC (Model-View-Controller)** standar dari framework Laravel.

*   `app/Http/Controllers/`: Berisi logika kontroler aplikasi (misal: `VehicleController`, `MasterDataController`, `OpdController`).
*   `app/Models/`: Berisi Eloquent ORM (Representasi Tabel Database) seperti `Vehicle`, `VehicleType`, `Opd`, `Setting`, dan `User`.
*   `database/migrations/`: Berisi definisi skema database (DDL).
*   `resources/views/`: Berisi tampilan antarmuka (UI) menggunakan Laravel Blade.
*   `routes/web.php`: Berisi definisi rute (URL) dan pengelompokan *middleware* yang menjadi titik masuk sistem.
*   `public/`: Direktori publik tempat Vite mengkompilasi file statis (CSS/JS).

## 📡 API / Rute yang Tersedia
Meskipun tidak menggunakan API terpisah secara murni (menggunakan Blade monolithic), berikut rute utama sistem:

**Public (Akses Umum):**
*   `GET /`: Landing page portal E-RANDIS.
*   `GET /vehicle-search`: API pencarian data kendaraan publik.

**Protected (Akses Admin / Auth):**
*   `GET /home`: Halaman Dashboard Statistik.
*   `GET|POST|PUT|DELETE /vehicles`: Manajemen data utama kendaraan (CRUD).
*   `GET /vehicles/export`: Export data kendaraan ke format Excel.
*   `GET /vehicles/template`: Download template Excel untuk import.
*   `POST /vehicles/import`: Import data kendaraan via file Excel.
*   `POST /vehicles/truncate`: Menghapus seluruh data kendaraan.
*   `GET|POST|PUT|DELETE /vehicle-types`: Manajemen Master Data Tipe Kendaraan.
*   `POST /vehicle-types/cleanup`: Membersihkan data tipe kendaraan yang tidak terpakai.
*   `GET|POST|PUT|DELETE /opds`: Manajemen Master Data OPD/Instansi.
*   `GET /settings` & `POST /settings`: Pengaturan variabel portal (Logo, Nama, dll).

## 🗄️ Skema Database
Sistem ini menggunakan struktur relasional untuk menjamin integritas data:

1.  **`vehicles`** (Data Kendaraan Utama)
    *   `id`, `no_polisi`, `merk`, `tipe`, `jenis`, `tahun_pembuatan`, `no_rangka`, `no_mesin`, `warna`, `tgl_stnk`, `opd_id` (Relasi ke OPD), `vehicle_type_id` (Relasi ke VehicleType), `pemegang`, `status` (Tersedia, Digunakan, Rusak, Dilelang), `keterangan`.
2.  **`vehicle_types`** (Master Data Tipe Kendaraan)
    *   `id`, `name`, `description`.
3.  **`opds`** (Master Data Instansi)
    *   `id`, `nama`, `singkatan`, `alamat`.
4.  **`settings`** (Konfigurasi Aplikasi)
    *   `id`, `key`, `value`, `type` (text/image), `group`.
5.  **`users`** (Sistem Autentikasi Admin)
    *   `id`, `name`, `email`, `password`.

## 🛠️ Technology Stack
*   **Backend**: PHP ^8.2, Laravel Framework ^12.0
*   **Frontend**: Laravel Blade, Bootstrap ^5.3.8, Sass
*   **Asset Bundler**: Vite
*   **Database**: MySQL / SQLite (Sesuai Konfigurasi `.env`)

### 📦 Library Eksternal Utama
*   `maatwebsite/excel` (^3.1): Library handal untuk fitur Export & Import Excel.
*   `laravel/ui` (^4.6): Scaffolding untuk sistem autentikasi Bootstrap.
*   `axios`: HTTP Client untuk proses asynchronous di frontend.

## ⚙️ Cara Setup Project
Ikuti langkah-langkah berikut untuk menjalankan project ini di environment lokal Anda:

1. Clone repositori ini:
   ```bash
   git clone <url-repo-anda>
   cd E-RANDIS_PHP
   ```
2. Install dependensi PHP (Composer):
   ```bash
   composer install
   ```
3. Install dependensi Node.js (NPM):
   ```bash
   npm install
   ```
4. Setup Environment:
   * Copy file konfigurasi environment
   ```bash
   cp .env.example .env
   ```
   * Buka file `.env` dan sesuaikan kredensial koneksi Database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
5. Generate Application Key:
   ```bash
   php artisan key:generate
   ```
6. Jalankan Migrasi Database (dan Seeder jika ada):
   ```bash
   php artisan migrate
   ```

## 🚀 Cara Run Aplikasi
Aplikasi ini membutuhkan dua buah *service* yang berjalan berdampingan (Backend & Frontend bundler). Buka dua jendela terminal dan jalankan:

**Terminal 1 (Backend PHP):**
```bash
php artisan serve
```

**Terminal 2 (Frontend Asset Compilation Vite):**
```bash
npm run dev
```

*Atau Anda dapat menggunakan script serentak jika environment Anda mendukung (sesuai composer.json):*
```bash
composer run dev
```
Akses aplikasi melalui browser pada `http://localhost:8000`.

## 🧪 Cara Test Aplikasi
Untuk memastikan sistem berjalan dengan baik, Anda dapat menjalankan unit/feature test bawaan menggunakan PHPUnit:
```bash
php artisan test
```
