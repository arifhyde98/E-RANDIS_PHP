# Rencana Eksekusi Optimasi Loading (Quick Win Dulu)

Dokumen ini menjadi panduan eksekusi untuk meningkatkan performa loading E-RANDIS secara bertahap dengan prioritas dampak terbesar dan risiko terendah terlebih dahulu.

Tanggal: 2026-05-16

## 1. Tujuan

1. Menurunkan waktu loading halaman utama dan modul kendaraan.
2. Mengurangi beban query database per request.
3. Menjaga kestabilan fitur tanpa refactor besar di awal.

## 2. Fase 1 - Quick Win [SELESAI]
1. Optimasi query statistik dashboard jadi single aggregate query. (Selesai - app/Services/VehicleService.php)
2. Perbaiki filter jenis pada daftar kendaraan. (Selesai - app/Http/Controllers/VehicleController.php)
3. Pastikan mode runtime non-dev sudah benar. (Selesai - .env & artisan optimize)
4. Pastikan asset dilayani dari build production. (Selesai - npm run build)

## 3. Fase 2 - Medium Impact [SELESAI]
1. Optimasi pencarian landing (no_polisi) agar index-friendly. (Selesai - app/Services/VehicleService.php)
2. Kurangi payload HTML halaman kendaraan. (Selesai - resources/views/vehicles/index.blade.php)
3. Kurangi pemanggilan Setting::get() berulang di view landing. (Selesai - VehicleController & welcome.blade.php)

## 4. Fase 3 - Hardening & Skalabilitas [IN PROGRESS]

1. Evaluasi driver cache/session (database -> redis jika infrastruktur siap).
2. Kompres gambar besar dan terapkan lazy loading untuk gambar non-kritis.
3. Review indeks tambahan berbasis profiling query nyata.
4. Optimasi Import Kendaraan Massal. (Selesai - app/Imports/VehicleImport.php & VehicleController.php)
   - Implementasi `WithBatchInserts` & `WithChunkReading`.
   - In-memory caching untuk master data (OPD & VehicleType) dengan `firstOrCreate` (Anti-Fail).
   - Penonaktifan model events (observers) selama proses import untuk kecepatan.
   - **Hardening (Security):** 
     - Menggunakan `withoutGlobalScopes()` pada pengecekan plat agar deteksi duplikat bersifat GLOBAL (lintas OPD).
     - Implementasi *Null-Safe Access* pada relasi User-OPD untuk mencegah *crash* saat data tidak lengkap.

## 5. Aturan Eksekusi untuk Junior

1. Kerjakan Fase 1 poin 1-4 terlebih dahulu.
2. Commit per topik kecil, jangan gabung semua dalam satu commit.
3. Setelah Fase 1 selesai, kirim hasil benchmark singkat.
4. Lanjut Fase 2 hanya jika hasil Fase 1 valid dan stabil.

## 6. Definition of Done (Quick Win)

1. Query statistik sudah single aggregate.
2. Bug/pola filter `jenis` sudah diperbaiki.
3. Mode runtime non-dev sudah production-safe.
4. Aset non-dev sudah dari build production.
5. Ada bukti before/after bahwa loading membaik.
