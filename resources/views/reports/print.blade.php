<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - E-RANDIS</title>
    <!-- Hubungkan Bootstrap 5 Core secara lokal via Vite -->
    @vite(['resources/css/app.scss'])
    
    <style>
        body {
            background-color: #ffffff;
            color: #000000;
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            padding: 30px;
        }

        /* Styling Kop Surat Resmi Instansi Pemerintah */
        .kop-surat {
            border-bottom: 3px double #000000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .kop-logo {
            width: 75px;
            height: auto;
        }

        .kop-title-1 {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0;
        }

        .kop-title-2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
        }

        .kop-title-3 {
            font-size: 10pt;
            font-style: italic;
            margin: 0;
            color: #444444;
        }

        .report-meta {
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .report-meta table td {
            padding: 3px 8px;
            border: none !important;
        }

        /* Format Khusus Monospace Plat Nomor */
        .plate-number {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            border: 1px solid #000000;
            padding: 2px 6px;
            background-color: #f8fafc;
            border-radius: 3px;
        }

        /* Format Garis Batas Tabel Cetak Resmi */
        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table-print th, 
        .table-print td {
            border: 1px solid #000000 !important;
            padding: 6px 10px !important;
            font-size: 10pt !important;
            vertical-align: middle;
        }

        tr.dup-highlight td {
            background-color: rgba(30, 64, 175, 0.07) !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-print th {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            font-weight: bold !important;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Bagian Lembar Tanda Tangan (Palu, ...) */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }

        /* Pengaturan Cetak Halaman Printer */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <!-- TOMBOL KONTROL CETAK MANUAL (DIPORTABELKAN) -->
    <div class="no-print d-flex justify-content-between align-items-center bg-light p-3 border rounded mb-4 shadow-sm">
        <div>
            <i class="bi bi-printer-fill text-navy me-1"></i>
            <span class="fw-semibold text-navy">Pratinjau Cetak E-RANDIS</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.close()" class="btn btn-sm btn-outline-secondary px-3 fw-medium">Tutup Tab</button>
            <button onclick="window.print()" class="btn btn-sm btn-primary px-4 fw-semibold">Cetak Sekarang</button>
        </div>
    </div>

    <!-- KOP SURAT PEMERINTAH (SULAWESI TENGAH) -->
    <div class="kop-surat d-flex align-items-center gap-3">
        <!-- Logo Pemerintah Provinsi Sulawesi Tengah (Tadulako) -->
        <img src="{{ asset('images/logo-sulteng.svg') }}" alt="Logo Sulteng" class="kop-logo">
        <div class="text-center flex-grow-1">
            <div class="kop-title-1">PEMERINTAH PROVINSI SULAWESI TENGAH</div>
            <div class="kop-title-2">BADAN PENDAPATAN DAERAH (BAPENDA)</div>
            <div class="kop-title-3">Jalan Cik Ditiro No. 23, Kota Palu, Sulawesi Tengah - Telepon: (0451) 421234</div>
        </div>
    </div>

    <!-- JUDUL DOKUMEN LAPORAN -->
    <div class="text-center mb-4">
        <h5 class="fw-bold text-uppercase mb-1" style="text-decoration: underline;">{{ $reportTitle }}</h5>
        <span class="small text-secondary">Sistem Informasi Manajemen Kendaraan Dinas (E-RANDIS)</span>
    </div>

    <!-- DETAIL PARAMETER FILTER LAPORAN -->
    <div class="report-meta">
        <table class="table table-sm w-auto">
            <tbody>
                <tr>
                    <td class="text-secondary fw-semibold">Kriteria Kondisi</td>
                    <td>:</td>
                    <td class="fw-medium">{{ $filters['kondisi'] ?? 'Semua Kondisi' }}</td>
                </tr>
                <tr>
                    <td class="text-secondary fw-semibold">Tahun Perolehan</td>
                    <td>:</td>
                    <td class="fw-medium">{{ $filters['tahun'] ?? 'Semua Tahun' }}</td>
                </tr>
                <tr>
                    <td class="text-secondary fw-semibold">Tanggal Cetak</td>
                    <td>:</td>
                    <td class="fw-medium">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WITA</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- TABEL UTAMA DENGAN SELURUH DATA KENDARAAN (TANPA PAGINASI DI KERTAS) -->
    <table class="table table-bordered table-print">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                @foreach($headers as $key => $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $groupColors = [];
                $colorIndex = 0;
            @endphp
            @forelse($data as $index => $row)
                @php
                    $rowClass = '';
                    $type = $filters['type'] ?? 'status';
                    if ($type === 'duplicate') {
                        $groupKey = $row->duplicate_group_key;
                        if (!isset($groupColors[$groupKey])) {
                            $groupColors[$groupKey] = ($colorIndex++ % 2 === 0);
                        }
                        
                        if ($groupColors[$groupKey] && !str_starts_with($groupKey, 'none_')) {
                            $rowClass = 'class="dup-highlight"';
                        }
                    }
                @endphp
                <tr {!! $rowClass !!}>
                    <td class="text-center">{{ $index + 1 }}</td>
                    @foreach($headers as $key => $label)
                        <td>
                            @if($key === 'no_polisi')
                                <span class="plate-number">{{ strtoupper(trim($row->{$key})) }}</span>
                            @elseif($key === 'nilai_perolehan')
                                Rp{{ number_format($row->{$key}, 0, ',', '.') }}
                            @elseif($key === 'tgl_stnk' || $key === 'tgl_perolehan')
                                {{ $row->{$key} ? \Carbon\Carbon::parse($row->{$key})->translatedFormat('d F Y') : '-' }}
                            @else
                                {{ $row->{$key} ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="text-center py-4">Belum ada data kendaraan dinas yang sesuai dengan kriteria filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- LEMBAR PENANDATANGANAN RESMI (TANDA TANGAN KEPALA DINAS) -->
    <div class="signature-section clearfix">
        <div class="signature-box">
            <div>Palu, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            <div class="fw-bold mt-1">Plt. Kepala Badan Pendapatan Daerah,</div>
            <div style="height: 80px;"></div>
            <div class="fw-bold text-decoration-underline">Drs. H. ARIF HYDE, M.Si</div>
            <small class="text-secondary d-block">Pembina Utama Muda</small>
            <small class="text-secondary">NIP. 19780512 200212 1 002</small>
        </div>
    </div>

    <!-- PEMICU WINDOW.PRINT OTOMATIS SAAT HALAMAN SELESAI DIMUAT -->
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Berikan jeda 800ms agar browser merender dokumen/tabel secara sempurna sebelum dialog print terbuka
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
