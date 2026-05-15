# Pemisahan Kolom Kondisi & Status Kendaraan

## Latar Belakang
Saat ini kolom `status` mencampur dua konsep:
- **Kondisi fisik** kendaraan (Baik, Rusak Ringan, Rusak Berat, Hilang)
- **Status operasional** (Tersedia, Dipinjam, Nonaktif)

Ini menyebabkan data impor Excel masuk mentah tanpa normalisasi, dan query dashboard harus "tambal sulam" mencoba mencocokkan berbagai variasi string.

## Proposed Changes

### Database Layer

#### [NEW] Migration: `add_kondisi_to_vehicles_table`
- Menambahkan kolom `kondisi` (string, default `'Baik'`) setelah kolom `status`
- Menambahkan index pada kolom `kondisi`

---

### Enum Layer

#### [NEW] [VehicleCondition.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Enums/VehicleCondition.php)
Enum baru untuk kondisi fisik kendaraan:

| Case | Value | Label | Alias Singkatan |
|------|-------|-------|-----------------|
| `BAIK` | `Baik` | Baik / Layak | `B`, `BAIK` |
| `RUSAK_RINGAN` | `Rusak Ringan` | Rusak Ringan | `RR`, `RUSAK RINGAN` |
| `RUSAK_BERAT` | `Rusak Berat` | Rusak Berat | `RB`, `RUSAK BERAT` |
| `HILANG` | `Hilang` | Hilang / Tidak Ditemukan | `HILANG`, `H` |
| `DALAM_PENELUSURAN` | `Dalam Penelusuran` | Dalam Penelusuran | `DALAM PENELUSURAN`, `TIDAK DIKETAHUI`, `TD` |

Dilengkapi metode statis `fromImport(string)` yang menormalisasi singkatan dari Excel.

#### [MODIFY] [VehicleStatus.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Enums/VehicleStatus.php)
- Menghapus case `RUSAK` (pindah ke `VehicleCondition`)
- Menyederhanakan menjadi: `TERSEDIA`, `DIPINJAM`, `NONAKTIF`

---

### Model Layer

#### [MODIFY] [Vehicle.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Models/Vehicle.php)
- Menambahkan `kondisi` ke `$fillable`
- Menambahkan `@property` PHPDoc untuk `kondisi`
- Menambahkan method `getConditions()` (seperti `getStatuses()`)
- Status operasional diturunkan otomatis dari kondisi saat import

---

### Import Layer

#### [MODIFY] [VehicleImport.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Imports/VehicleImport.php)
Logika baris 110 yang saat ini mentah:
```php
// SEBELUM (masalah):
'status' => (strtoupper($row[9]) == 'BAIK' || ...) ? 'Tersedia' : ($row[9] ?? 'Tersedia'),

// SESUDAH (bersih):
$kondisi = VehicleCondition::fromImport($row[9]);
'kondisi' => $kondisi->value,
'status'  => $kondisi->toDefaultStatus()->value,
```

Mapping otomatis kondisi → status default:
- `Baik` / `Rusak Ringan` → `Tersedia`
- `Rusak Berat` / `Hilang` / `Dalam Penelusuran` → `Nonaktif`

---

### Service Layer

#### [MODIFY] [VehicleService.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Services/VehicleService.php)
Dashboard stats diperbaiki menggunakan enum:
- `available` → COUNT WHERE `kondisi = 'Baik'`
- `rusak_ringan` → COUNT WHERE `kondisi = 'Rusak Ringan'`  
- `rusak_berat` → COUNT WHERE `kondisi = 'Rusak Berat'`
- `hilang` → COUNT WHERE `kondisi IN ('Hilang', 'Dalam Penelusuran')`

---

### Controller Layer

#### [MODIFY] [VehicleController.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Http/Controllers/VehicleController.php)
- Menambahkan `$conditions` ke view data (`index`, `create`, `edit`)
- Menambahkan filter `kondisi` di query index
- Update `searchLandingVehicle` untuk menampilkan kondisi

---

### View Layer

#### [MODIFY] [index.blade.php](file:///d:/laragon/www/E-RANDIS_PHP/resources/views/vehicles/index.blade.php)
- Stat cards: Memecah "Maintenance" menjadi statistik yang lebih bermakna  
- Filter: Menambah dropdown filter `Kondisi`
- Tabel: Menampilkan kolom Kondisi terpisah dari Status
- Modal Add/Edit: Menambah dropdown `Kondisi` 
- Modal Detail: Menampilkan informasi kondisi
- JS: Update populate logic untuk field `kondisi`

#### [MODIFY] [status-badge.blade.php](file:///d:/laragon/www/E-RANDIS_PHP/resources/views/components/status-badge.blade.php)
- Mendukung rendering untuk `VehicleCondition` selain `VehicleStatus`

#### [NEW] [condition-badge.blade.php](file:///d:/laragon/www/E-RANDIS_PHP/resources/views/components/condition-badge.blade.php)
- Komponen badge khusus untuk kondisi fisik kendaraan  
- Warna: Baik=hijau, RR=kuning, RB=merah, Hilang=ungu, Penelusuran=abu

#### [MODIFY] [home.blade.php](file:///d:/laragon/www/E-RANDIS_PHP/resources/views/home.blade.php)
- Update stat cards dashboard dengan data kondisi terbaru

---

### Export Layer

#### [MODIFY] [VehicleExport.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Exports/VehicleExport.php)
- Menambahkan kolom `Kondisi` terpisah dari `Status`

#### [MODIFY] [VehicleTemplateExport.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Exports/VehicleTemplateExport.php)
- Kolom header "Kondisi" diganti menjadi "Kondisi (B/RR/RB/Hilang)"

---

### Validation Layer

#### [MODIFY] [StoreVehicleRequest.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Http/Requests/StoreVehicleRequest.php)
- Menambahkan rule `kondisi` (required, in:Baik,Rusak Ringan,Rusak Berat,Hilang,Dalam Penelusuran)

#### [MODIFY] [UpdateVehicleRequest.php](file:///d:/laragon/www/E-RANDIS_PHP/app/Http/Requests/UpdateVehicleRequest.php)
- Menambahkan rule `kondisi` (required, in:...)

---

### Documentation

#### [MODIFY] [AI_HANDOVER.md](file:///d:/laragon/www/E-RANDIS_PHP/AI_HANDOVER.md)
- Menambahkan dokumentasi kolom `kondisi` di skema database
- Menambahkan penjelasan mapping singkatan import

---

## Verification Plan

### Automated Tests
```bash
php artisan migrate
php artisan tinker  # Verifikasi enum VehicleCondition::fromImport('RB')
```

### Manual Verification
- Import file Excel dengan variasi singkatan (B, RB, RR, Hilang, dll)
- Verifikasi dashboard menampilkan statistik terpisah
- Verifikasi filter kondisi di halaman kendaraan
- Verifikasi modal Add/Edit memiliki dropdown kondisi
