# Review Lanjutan Laporan Duplikasi Kendaraan

## Ringkasan

Perbaikan besar pada Modul Laporan sudah berjalan ke arah yang benar:

- laporan `duplicate` sudah dibatasi untuk `admin` dan `superadmin`;
- ekspor standar sudah kembali memakai jalur `FromQuery`;
- ekspor laporan yang perlu pengayaan data sudah dipisah ke jalur `FromCollection`;
- kontrak `PostProcessesReportRows` sudah dibuat;
- style highlight duplikasi sudah memakai token tema light/dark mode;
- test otorisasi dan pemilihan exporter sudah ditambahkan.

Namun, masih ada **satu isu desain penting** yang belum boleh dianggap selesai: penggunaan **static cache** di `DuplicateVehicleReport`.

---

## Temuan Utama

### 1. Static cache berisiko menghasilkan data basi lintas request

Lokasi:

- `app/Reports/Strategies/DuplicateVehicleReport.php`

Kode saat ini:

```php
protected static ?Collection $cachedDuplicates = null;
```

Masalah:

- `static` state dapat bertahan selama proses PHP masih hidup.
- Pada lingkungan long-running seperti Octane, worker queue, atau proses yang me-reuse state, isi cache bisa tetap terbawa setelah data kendaraan berubah.
- Akibatnya laporan duplikasi dapat menampilkan pasangan duplikasi lama walaupun database sudah berubah.

Kesimpulan:

- Ini bukan pola cache yang aman untuk laporan dinamis.
- Cache seperti ini tidak punya mekanisme invalidasi yang jelas.

### 2. Heuristic `count() > 15` terlalu rapuh

Kode saat ini:

```php
if ($vehicles->count() > 15) {
    self::$cachedDuplicates = $vehicles;
} else {
    self::$cachedDuplicates = $this->query([])->get();
}
```

Masalah:

- Angka `15` berasal dari ukuran paginasi preview, bukan dari kontrak bisnis.
- Export atau print yang hasilnya hanya 10 baris tetap memicu query kedua.
- Jika ukuran paginasi preview berubah suatu saat nanti, logika ini bisa salah klasifikasi.

Kesimpulan:

- Penentuan apakah dataset sudah lengkap tidak boleh didasarkan pada jumlah baris.

### 3. Dokumentasi terlalu optimistis

Lokasi:

- `AI_HANDOVER.md`

Klaim saat ini menyebut analisis duplikasi memakai satu kueri referensi tunggal. Implementasi aktual belum selalu demikian:

- preview masih memerlukan query referensi tambahan;
- export/print hanya bebas query tambahan bila hasilnya lebih dari 15 baris;
- static cache sendiri berisiko stale.

Kesimpulan:

- Dokumentasi perlu memakai istilah yang lebih faktual sampai desain final benar-benar diterapkan.

---

## Arah Perbaikan yang Direkomendasikan

### Prinsip yang harus dijaga

1. Tidak memakai state `static` untuk menyimpan dataset laporan.
2. Tidak memakai heuristic berbasis jumlah baris untuk menebak apakah dataset lengkap.
3. Jalur data harus eksplisit:
   - preview boleh mengambil dataset referensi tambahan;
   - export/print boleh memakai dataset penuh yang memang sudah tersedia.

### Solusi yang disarankan

Buat alur referensi duplikasi secara eksplisit, misalnya:

```php
public function postProcess(Collection $rows, ?Collection $referenceRows = null): void
```

Lalu gunakan seperti ini:

- **Preview**
  - `$rows` = halaman hasil paginasi
  - `$referenceRows` = hasil query referensi penuh yang dipanggil sekali secara eksplisit

- **Export / Print**
  - `$rows` = seluruh dataset hasil query
  - `$referenceRows` = `$rows`

Dengan pola ini:

- tidak ada static cache;
- tidak ada state basi lintas request;
- tidak ada tebakan berbasis angka 15;
- perilaku setiap jalur menjadi jelas dan mudah diuji.

### Alternatif yang lebih rapi

Jika logika duplikasi makin besar, pertimbangkan membuat service khusus:

```text
app/Services/DuplicateVehicleDetector.php
```

Tanggung jawab service:

- membangun dataset kandidat duplikasi;
- membuat mapping grup duplikasi;
- menghasilkan `keterangan_duplikat`;
- menghasilkan `duplicate_group_key`.

`DuplicateVehicleReport` cukup mengorkestrasi query laporan dan memanggil service tersebut.

---

## Perubahan yang Harus Dilakukan

1. Hapus:

```php
protected static ?Collection $cachedDuplicates = null;
```

2. Hapus logika:

```php
if ($vehicles->count() > 15) {
    ...
}
```

3. Ubah kontrak `PostProcessesReportRows` agar mendukung dataset referensi eksplisit, atau buat kontrak baru bila ingin tetap menjaga signature lama.

4. Ubah:

- `ReportService`
- `ReportController`
- `DuplicateVehicleReport`

agar jalur preview, print, dan export mengirim dataset referensi dengan jelas.

5. Perbarui dokumentasi:

- ganti klaim `satu kueri referensi tunggal`
- menjadi kalimat yang lebih akurat, misalnya:

```text
analisis identik dilakukan melalui post-processing in-memory untuk menghindari query per baris.
```

6. Tambahkan test regresi:

- memastikan hasil duplikasi berubah setelah data kendaraan berubah;
- memastikan tidak ada state lama yang terbawa antar pemanggilan;
- memastikan preview dan export/print menghasilkan analisis yang konsisten.

---

## Definisi Selesai

Perbaikan boleh dianggap selesai bila:

1. tidak ada lagi `static cache` untuk dataset duplikasi;
2. tidak ada lagi heuristic berbasis `count() > 15`;
3. preview, export, dan print memakai alur referensi data yang eksplisit;
4. dokumentasi sesuai implementasi nyata;
5. test regresi membuktikan data tidak stale setelah perubahan database.

---

## Catatan untuk Junior Developer

Perbaikan sebelumnya sudah sangat baik. Yang tersisa sekarang bukan celah keamanan besar, melainkan **ketelitian desain**:

- jangan menukar satu query tambahan dengan state global yang sulit dikendalikan;
- lebih baik sedikit lebih eksplisit tetapi selalu benar;
- untuk laporan, kesegaran data lebih penting daripada optimasi yang rapuh.
