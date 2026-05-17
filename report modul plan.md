# RENCANA FINAL IMPLEMENTASI: MODUL LAPORAN MODULAR E-RANDIS

Dokumen ini menjadi pegangan final sebelum proses coding Modul Laporan dimulai. Rancangan disusun agar selaras dengan arsitektur existing E-RANDIS, menjaga isolasi data OPD, meminimalkan dampak ke modul lain, dan tetap mudah dikembangkan di masa depan.

## 1. Tujuan Modul

Modul Laporan bertujuan menyediakan:
1. Ringkasan statistik kendaraan.
2. Pratinjau laporan dinamis berdasarkan filter.
3. Ekspor laporan ke Excel.
4. Cetak laporan ke PDF/browser print.
5. Fondasi modular agar jenis laporan baru dapat ditambahkan tanpa mengubah controller inti.

Jenis laporan awal:
- Laporan Status dan Kondisi Kendaraan
- Laporan Distribusi Aset per OPD
- Laporan Masa Berlaku Dokumen/STNK

## 2. Prinsip Arsitektur

### 2.1 Service Layer

`ReportController` hanya menangani HTTP request/response.

Logika bisnis, pemilihan strategy, ringkasan data, dan orkestrasi preview/ekspor diletakkan di `ReportService`.

### 2.2 Strategy Pattern

Setiap jenis laporan memiliki strategy sendiri agar logika query tidak menumpuk dalam satu class.

### 2.3 Registry Pattern

`ReportRegistry` bertugas memilih strategy berdasarkan `type` laporan.

Dengan begitu `ReportService` tidak perlu mengetahui detail class laporan satu per satu.

### 2.4 Tenant-Aware by Default

Semua query laporan berbasis `Vehicle::query()` sehingga otomatis tunduk pada `TenantScope` existing:
- OPD hanya melihat data miliknya sendiri.
- Jika `opd_id` user OPD null, akses terkunci total.
- Admin dan Superadmin dapat melihat data global.

### 2.5 Reuse Existing Components

UI wajib memakai:
- `<x-stat-card>`
- `<x-table-card>`
- `x-condition-badge` bila relevan

Tidak membuat komponen baru bila komponen existing sudah mencukupi.

## 3. Keputusan Desain Final

### 3.1 Preview Laporan

Preview akan memakai **HTML partial Blade via AJAX**, bukan JSON mentah.

Alasan:
- Konsisten dengan komponen Blade existing.
- Lebih mudah menjaga format `.plate-number`, badge, empty state, dan pagination.
- JavaScript lebih tipis dan risiko inkonsistensi UI lebih rendah.

### 3.2 Caching

Fase awal:
- **Cache summary diizinkan**
- **Preview belum di-cache**

Alasan:
- Preview memiliki kombinasi filter sangat banyak.
- Invalidation cache preview lebih rumit dan berisiko menyentuh modul lama.
- Query preview dipaginasi, sehingga lebih aman diukur dulu sebelum diberi cache.

Jika nanti performa terbukti perlu:
- gunakan cache versioning per tenant atau cache tags;
- jangan langsung memakai key `md5_filters` tanpa strategi invalidasi yang matang.

### 3.3 Performa Query

Jangan mengasumsikan semua laporan sudah optimal hanya karena ada index existing.

Index saat ini mendukung `status`, `opd_id`, `vehicle_type_id`, tetapi laporan juga akan banyak memakai:
- `kondisi`
- `tgl_stnk`
- `tahun_pembuatan`

Tahapan aman:
1. implementasi dulu;
2. ukur dengan `EXPLAIN`;
3. tambah index hanya bila benar-benar dibutuhkan.

### 3.4 Summary Query

Ringkasan laporan harus memakai **agregasi tunggal**, bukan chaining builder berulang, agar:
- lebih cepat;
- hasil lebih akurat;
- tidak terkena bug `orWhere` yang melebar.

## 4. Struktur Berkas Final

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── ReportController.php
│   └── Requests/
│       └── ReportFilterRequest.php
├── Services/
│   └── ReportService.php
├── Reports/
│   ├── Contracts/
│   │   └── ReportStrategy.php
│   ├── Strategies/
│   │   ├── VehicleStatusReport.php
│   │   ├── OpdAssetReport.php
│   │   └── DocumentValidityReport.php
│   └── ReportRegistry.php
└── Exports/
    └── DynamicReportExport.php

resources/
├── css/
│   └── pages/
│       └── _reports.scss
└── views/
    └── reports/
        ├── index.blade.php
        ├── partials/
        │   └── preview-table.blade.php
        └── print.blade.php

tests/
└── Feature/
    └── ReportAccessTest.php
```

## 5. Tanggung Jawab Tiap Komponen

### `ReportStrategy`

Kontrak wajib semua laporan:
- `query(array $filters): Builder`
- `headers(): array`
- opsional di masa depan: `title()`, `filename()`

### `Strategies/*`

Berisi logika spesifik tiap laporan:
- `VehicleStatusReport`
- `OpdAssetReport`
- `DocumentValidityReport`

### `ReportRegistry`

Memetakan:
- `status` -> `VehicleStatusReport`
- `opd` -> `OpdAssetReport`
- `document` -> `DocumentValidityReport`

### `ReportService`

Menangani:
- ringkasan statistik;
- generate preview;
- pemilihan strategy via registry;
- orkestrasi data untuk ekspor.

### `ReportController`

Menangani:
- halaman index;
- endpoint AJAX preview;
- endpoint export Excel;
- endpoint print/cetak.

### `ReportFilterRequest`

Menangani:
- validasi `type`;
- validasi `kondisi` via `Rule::enum`;
- validasi `opd_id`;
- validasi `tahun`;
- pemaksaan `opd_id` milik user bila role OPD.

## 6. Alur Data

```text
User pilih filter
        |
        v
ReportController
        |
        v
ReportFilterRequest validasi + kunci opd_id user OPD
        |
        v
ReportService
        |
        v
ReportRegistry memilih strategy
        |
        v
Strategy menjalankan Vehicle::query()
        |
        v
TenantScope otomatis membatasi data
        |
        v
Partial Blade dirender
        |
        v
HTML preview dikirim kembali via AJAX
```

## 7. Aturan Keamanan

1. User OPD tidak boleh melihat dropdown OPD lain.
2. Backend tetap wajib memaksa `opd_id` ke milik user OPD.
3. Jangan pernah memakai `withoutGlobalScopes()` pada query laporan biasa.
4. Semua endpoint laporan minimal memakai middleware `auth`.
5. Jika ada ekspor, filter yang dipakai harus berasal dari hasil validasi request, bukan input mentah.
6. Uji khusus harus membuktikan request `opd_id` palsu dari user OPD tetap tidak bisa bocor.

## 8. Aturan UI

1. Gunakan gaya formal existing: navy, putih, abu-abu.
2. Gunakan `<x-stat-card>` untuk ringkasan.
3. Gunakan `<x-table-card>` untuk tabel preview.
4. Nomor polisi wajib memakai `.plate-number`.
5. Format rupiah wajib memakai pemisah ribuan Indonesia.
6. SCSS baru diletakkan di `_reports.scss`, lalu diimpor dari `app.scss`.
7. Tidak menambahkan glassmorphism atau gradien mencolok di luar gaya existing komponen.

## 9. Struktur Route yang Disarankan

```php
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/preview', [ReportController::class, 'preview'])->name('reports.preview');
Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');
```

Middleware tetap didefinisikan di `ReportController` sesuai standar proyek.

## 10. Pengujian Wajib

### `ReportAccessTest`

Minimal mencakup:
1. Guest diarahkan ke login.
2. OPD hanya melihat kendaraan miliknya.
3. OPD tidak bisa mengintip data OPD lain walau mengirim `opd_id` berbeda.
4. OPD dengan `opd_id = null` tidak menerima data.
5. Admin/Superadmin dapat melihat data global.
6. Filter `kondisi`, `tahun`, dan `type` tervalidasi dengan benar.
7. Endpoint preview mengembalikan partial yang sesuai.

## 11. Batas Dampak ke Modul Lain

File existing yang kemungkinan disentuh:
- `routes/web.php`
- `resources/css/app.scss`
- mungkin partial sidebar/navbar bila ingin menambah menu “Laporan”

File existing yang **sebisa mungkin tidak perlu diubah**:
- `VehicleController`
- `OpdController`
- `UserController`
- `VehicleObserver`
- `VehicleService`

Catatan:
- `VehicleService` baru perlu disentuh jika di fase lanjutan cache laporan benar-benar diterapkan.
- Dengan keputusan tanpa cache preview di fase awal, risiko gangguan lintas modul tetap rendah.

## 12. Roadmap Implementasi

### Fase 1: Fondasi Backend

- buat `ReportStrategy`;
- buat `ReportRegistry`;
- buat `ReportService`;
- buat `ReportFilterRequest`;
- buat `ReportController`;
- tambah route laporan;
- implementasi 1 strategy awal: `VehicleStatusReport`.

### Fase 2: UI dan Preview

- buat `reports/index.blade.php`;
- buat `partials/preview-table.blade.php`;
- gunakan `x-stat-card` dan `x-table-card`;
- tambahkan AJAX filter preview;
- buat `_reports.scss`.

### Fase 3: Strategi Laporan Tambahan

- `OpdAssetReport`;
- `DocumentValidityReport`;
- sesuaikan header dan format kolom tiap laporan.

### Fase 4: Ekspor

- implementasi `DynamicReportExport`;
- implementasi endpoint Excel;
- buat tampilan `print.blade.php`.

### Fase 5: QA dan Optimasi

- feature test tenant;
- uji query dengan data besar;
- jalankan `EXPLAIN`;
- tambah index baru hanya bila hasil ukur membuktikan perlu.

## 13. Keputusan Akhir yang Direkomendasikan

1. Gunakan `ReportStrategy`, bukan nama generik `ReportInterface`.
2. Pakai `ReportRegistry` agar modul mudah tumbuh.
3. Gunakan HTML partial AJAX untuk preview.
4. Cache summary boleh, cache preview ditunda.
5. Jangan ubah modul lama kecuali benar-benar perlu.
6. Ukur performa dulu sebelum menambah index baru.
7. Semua aturan tenant tetap bertumpu pada `TenantScope` existing.

Dengan bentuk ini, Modul Laporan akan:
- modular;
- aman;
- konsisten dengan arsitektur E-RANDIS;
- dan punya risiko rendah terhadap modul yang sudah stabil.
