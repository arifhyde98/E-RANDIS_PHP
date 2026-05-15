@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('content')
<div class="container-fluid px-0">
    
    <!-- PAGE HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item active text-navy fw-medium" aria-current="page">Data Kendaraan</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-navy mb-0">Manajemen Data Kendaraan</h3>
        </div>
        <div class="action-toolbar d-flex flex-wrap gap-2">
            <form action="{{ route('vehicles.truncate') }}" method="POST" class="delete-confirm">
                @csrf
                <button type="submit" class="btn btn-outline-danger shadow-sm fw-medium d-flex align-items-center gap-2">
                    <i class="bi bi-trash3"></i> <span class="d-none d-sm-inline">Kosongkan</span>
                </button>
            </form>
            <button type="button" class="btn btn-action btn-action-success shadow-sm fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <span class="btn-action-icon"><i class="bi bi-file-earmark-arrow-up"></i></span>
                <span class="d-none d-sm-inline">Import</span>
            </button>
            <a href="{{ route('vehicles.export') }}" class="btn btn-action btn-action-primary shadow-sm fw-semibold d-flex align-items-center gap-2" data-export-button>
                <span class="btn-action-icon"><i class="bi bi-download"></i></span>
                <span class="d-none d-sm-inline" data-export-label>Export</span>
            </a>
            <button type="button" class="btn btn-primary shadow-sm fw-medium d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                <i class="bi bi-plus-lg"></i> Tambah Kendaraan
            </button>
        </div>
    </div>



    <!-- OPTIONAL SIDEBAR SUMMARY (Displayed as top cards on smaller screens) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Kondisi Baik" :value="$stats['baik']" icon="check-circle" gradient="success" subtitle="Aset Layak Pakai" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Rusak Ringan" :value="$stats['rusak_ringan']" icon="exclamation-triangle" gradient="warning" subtitle="Butuh Maintenance" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Rusak Berat" :value="$stats['rusak_berat']" icon="x-octagon" gradient="danger" subtitle="Tidak Operasional" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card title="Hilang / TD" :value="$stats['hilang']" icon="question-circle" gradient="secondary" subtitle="Dalam Penelusuran" />
        </div>
    </div>

    <!-- MAIN TABLE SECTION -->
    <x-table-card 
        :empty="$vehicles->isEmpty()" 
        :collection="$vehicles"
        emptyText="Belum ada data kendaraan" 
        emptyIcon="bi-car-front">
        
        <x-slot:filters>
            <form action="{{ route('vehicles.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 bg-white shadow-none" placeholder="Cari nomor polisi atau nama pengguna...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm shadow-none" name="status">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm shadow-none" name="kondisi">
                        <option value="">Semua Kondisi</option>
                        @foreach($conditions as $key => $label)
                            <option value="{{ $key }}" {{ request('kondisi') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-medium">Filter</button>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-light border btn-sm bg-white" title="Reset Filter"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </x-slot:filters>

        <x-slot:thead>
            <tr>
                <th class="py-3 px-4 border-bottom-0 fw-semibold text-center" style="width: 50px;">No.</th>
                <th class="py-3 border-bottom-0 fw-semibold">No. Polisi</th>
                <th class="py-3 border-bottom-0 fw-semibold">Nama Kendaraan</th>
                <th class="py-3 border-bottom-0 fw-semibold d-none d-md-table-cell">Jenis / Tahun</th>
                <th class="py-3 border-bottom-0 fw-semibold">Pengguna / OPD</th>
                <th class="py-3 border-bottom-0 fw-semibold text-center">Kondisi Fisik</th>
                <th class="py-3 border-bottom-0 fw-semibold text-center">Status</th>
                <th class="py-3 px-4 border-bottom-0 fw-semibold text-center">Aksi</th>
            </tr>
        </x-slot:thead>

        @foreach($vehicles as $vehicle)
            <tr>
                <td class="px-4 py-3 text-center fw-medium text-secondary">
                    {{ ($vehicles->currentPage() - 1) * $vehicles->perPage() + $loop->iteration }}
                </td>
                <td class="py-3">
                    <span class="badge bg-light text-dark border border-secondary border-opacity-25 px-3 py-2 fs-6 rounded-3 fw-bold plate-number">{{ $vehicle->no_polisi }}</span>
                </td>
                <td class="py-3">
                    <div class="fw-bold text-navy">{{ $vehicle->merk }}</div>
                    <div class="small text-secondary">{{ $vehicle->tipe }}</div>
                </td>
                <td class="py-3 d-none d-md-table-cell">
                    <div class="text-dark fw-medium">{{ $vehicle->vehicleType->name ?? ($vehicle->jenis ?? 'Mobil Dinas') }}</div>
                    <div class="small text-secondary">
                        {{ $vehicle->tahun_pembuatan ?? ($vehicle->tgl_perolehan ? \Carbon\Carbon::parse($vehicle->tgl_perolehan)->year : '-') }}
                    </div>
                </td>
                <td class="py-3">
                    <div class="fw-medium text-dark"><i class="bi bi-person-fill text-secondary me-1"></i> {{ $vehicle->pemegang }}</div>
                    <div class="small text-secondary">{{ Str::limit($vehicle->opd, 30) }}</div>
                </td>
                <td class="text-center">
                    <x-condition-badge :kondisi="$vehicle->kondisi" />
                </td>
                <td class="text-center">
                    <x-status-badge :status="$vehicle->status" />
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-light border shadow-none text-navy" 
                                data-bs-toggle="modal" data-bs-target="#detailVehicleModal" 
                                data-vehicle="{{ json_encode($vehicle->only(['id', 'no_polisi', 'merk', 'tipe', 'jenis', 'opd', 'opd_id', 'pemegang', 'status', 'vehicle_type_id', 'tahun_pembuatan', 'warna', 'stnk_ada', 'bpkb_ada', 'tgl_stnk', 'tgl_perolehan', 'nilai_perolehan', 'no_mesin', 'no_rangka', 'keterangan'])) }}" title="Detail Kendaraan">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border shadow-none text-primary" 
                                data-bs-toggle="modal" data-bs-target="#editVehicleModal" 
                                data-vehicle="{{ json_encode($vehicle->only(['id', 'no_polisi', 'merk', 'tipe', 'jenis', 'opd', 'opd_id', 'pemegang', 'status', 'vehicle_type_id', 'tahun_pembuatan', 'warna', 'stnk_ada', 'bpkb_ada', 'tgl_stnk', 'tgl_perolehan', 'nilai_perolehan', 'no_mesin', 'no_rangka', 'keterangan'])) }}" title="Edit Data">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline delete-confirm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border shadow-none text-danger" data-bs-toggle="tooltip" title="Hapus Data">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach

        <x-slot:pagination>
            {{ $vehicles->links() }}
        </x-slot:pagination>
    </x-table-card>

</div>

@push('modals')
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-navy" id="importModalLabel">Import Data Kendaraan</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vehicles.import') }}" method="POST" enctype="multipart/form-data" data-import-form>
                @csrf
                <div class="modal-body p-4">
                    <div class="import-helper d-flex border rounded-3 p-3 mb-4">
                        <div class="me-3">
                            <i class="bi bi-info-circle text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Persiapkan Data Anda</h6>
                            <p class="small text-secondary mb-2">Pastikan data Excel (.xlsx) mengikuti format standar sistem agar proses import berjalan lancar.</p>
                            <a href="{{ route('vehicles.template') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" data-template-button>
                                <i class="bi bi-download"></i> Download Template
                            </a>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required data-import-file>
                        <div class="small text-secondary mt-2" data-import-file-name>Belum ada file dipilih.</div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-medium px-4" data-import-submit>
                        <i class="bi bi-cloud-arrow-up me-1"></i> Proses Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('modals')
    <!-- ADD VEHICLE MODAL -->
    <x-modal id="addVehicleModal" title="Tambah Kendaraan Dinas Baru" size="xl" submitLabel="Simpan Data" form="addVehicleForm">
        <form id="addVehicleForm" action="{{ route('vehicles.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">No. Polisi <span class="text-danger">*</span></label>
                    <input type="text" name="no_polisi" class="form-control" placeholder="DN 1234 XX" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Jenis <span class="text-danger">*</span></label>
                    <select name="vehicle_type_id" class="form-select" required onchange="document.getElementById('add_jenis_text').value = this.options[this.selectedIndex].text">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($vehicleTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="jenis" id="add_jenis_text" value="">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Merk <span class="text-danger">*</span></label>
                    <input type="text" name="merk" class="form-control" placeholder="Toyota" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tipe / Model <span class="text-danger">*</span></label>
                    <input type="text" name="tipe" class="form-control" placeholder="Innova" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-uppercase">OPD / Instansi <span class="text-danger">*</span></label>
                    <select name="opd_id" class="form-select" required onchange="document.getElementById('add_opd_text').value = this.options[this.selectedIndex].text">
                        <option value="">-- Pilih OPD --</option>
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->nama }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="opd" id="add_opd_text" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-uppercase">Pemegang <span class="text-danger">*</span></label>
                    <input type="text" name="pemegang" class="form-control" placeholder="Nama Pemegang" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Kondisi Fisik <span class="text-danger">*</span></label>
                    <select name="kondisi" class="form-select shadow-none" required>
                        @foreach($conditions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Status Operasional <span class="text-danger">*</span></label>
                    <select name="status" class="form-select shadow-none" required>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tahun</label>
                    <input type="number" name="tahun_pembuatan" class="form-control" placeholder="2024">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Warna</label>
                    <input type="text" name="warna" class="form-control" placeholder="Hitam">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Dok. STNK <span class="text-danger">*</span></label>
                    <select name="stnk_ada" class="form-select" required>
                        <option value="Ada">Ada</option>
                        <option value="Tidak">Tidak Ada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Dok. BPKB <span class="text-danger">*</span></label>
                    <select name="bpkb_ada" class="form-select" required>
                        <option value="Ada">Ada</option>
                        <option value="Tidak">Tidak Ada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tgl STNK</label>
                    <input type="date" name="tgl_stnk" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tgl Perolehan</label>
                    <input type="date" name="tgl_perolehan" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Nilai Perolehan (Rp)</label>
                    <input type="number" name="nilai_perolehan" class="form-control" placeholder="250000000">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Nomor Mesin</label>
                    <input type="text" name="no_mesin" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Nomor Rangka</label>
                    <input type="text" name="no_rangka" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small text-uppercase">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>
        </form>
    </x-modal>

    <!-- DETAIL VEHICLE MODAL -->
    <x-modal id="detailVehicleModal" title="Detail Informasi Kendaraan" size="lg">
        <div id="detailContent">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </x-modal>
    <!-- EDIT VEHICLE MODAL -->
    <x-modal id="editVehicleModal" title="Edit Informasi Kendaraan" size="xl" submitLabel="Simpan Perubahan" form="editVehicleForm">
        <form id="editVehicleForm" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">No. Polisi <span class="text-danger">*</span></label>
                    <input type="text" name="no_polisi" id="edit_no_polisi" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Jenis <span class="text-danger">*</span></label>
                    <select name="vehicle_type_id" id="edit_vehicle_type_id" class="form-select" required onchange="document.getElementById('edit_jenis_text').value = this.options[this.selectedIndex].text">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($vehicleTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="jenis" id="edit_jenis_text" value="">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Merk <span class="text-danger">*</span></label>
                    <input type="text" name="merk" id="edit_merk" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tipe / Model <span class="text-danger">*</span></label>
                    <input type="text" name="tipe" id="edit_tipe" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-uppercase">OPD / Instansi <span class="text-danger">*</span></label>
                    <select name="opd_id" id="edit_opd_id" class="form-select" required onchange="document.getElementById('edit_opd_text').value = this.options[this.selectedIndex].text">
                        <option value="">-- Pilih OPD --</option>
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->nama }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="opd" id="edit_opd_text" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-uppercase">Pemegang <span class="text-danger">*</span></label>
                    <input type="text" name="pemegang" id="edit_pemegang" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Kondisi Fisik <span class="text-danger">*</span></label>
                    <select name="kondisi" id="edit_kondisi" class="form-select shadow-none" required>
                        @foreach($conditions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Status Operasional <span class="text-danger">*</span></label>
                    <select name="status" id="edit_status" class="form-select shadow-none" required>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tahun</label>
                    <input type="number" name="tahun_pembuatan" id="edit_tahun_pembuatan" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Warna</label>
                    <input type="text" name="warna" id="edit_warna" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Dok. STNK <span class="text-danger">*</span></label>
                    <select name="stnk_ada" id="edit_stnk_ada" class="form-select" required>
                        <option value="Ada">Ada</option>
                        <option value="Tidak">Tidak Ada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Dok. BPKB <span class="text-danger">*</span></label>
                    <select name="bpkb_ada" id="edit_bpkb_ada" class="form-select" required>
                        <option value="Ada">Ada</option>
                        <option value="Tidak">Tidak Ada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tgl STNK</label>
                    <input type="date" name="tgl_stnk" id="edit_tgl_stnk" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase">Tgl Perolehan</label>
                    <input type="date" name="tgl_perolehan" id="edit_tgl_perolehan" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Nilai Perolehan (Rp)</label>
                    <input type="number" name="nilai_perolehan" id="edit_nilai_perolehan" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Nomor Mesin</label>
                    <input type="text" name="no_mesin" id="edit_no_mesin" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-uppercase">Nomor Rangka</label>
                    <input type="text" name="no_rangka" id="edit_no_rangka" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small text-uppercase">Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </form>
    </x-modal>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Detail Modal Logic
        const detailModal = document.getElementById('detailVehicleModal');
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const vehicle = JSON.parse(button.getAttribute('data-vehicle'));
                const detailContent = document.getElementById('detailContent');

                const escapeHtml = (str) => {
                    const div = document.createElement('div');
                    div.textContent = str || '-';
                    return div.innerHTML;
                };

                const formatRupiah = (num) => {
                    if (!num) return 'Rp 0';
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                };
                
                detailContent.innerHTML = `
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                                <small class="text-secondary text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">Nomor Polisi</small>
                                <h4 class="fw-bold text-navy mb-0">${escapeHtml(vehicle.no_polisi)}</h4>
                                <p class="text-secondary mb-0 small">${escapeHtml(vehicle.merk)} (${escapeHtml(vehicle.tahun_pembuatan)})</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100 d-flex flex-column justify-content-center border-start border-info border-4">
                                <small class="text-secondary text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">OPD Pengelola</small>
                                <div class="fw-bold text-navy small">${escapeHtml(vehicle.opd)}</div>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="small text-secondary fw-bold text-uppercase" style="font-size: 0.65rem;">Nomor Mesin</label>
                                    <div class="fw-semibold text-dark">${escapeHtml(vehicle.no_mesin)}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-secondary fw-bold text-uppercase" style="font-size: 0.65rem;">Nomor Rangka</label>
                                    <div class="fw-semibold text-dark">${escapeHtml(vehicle.no_rangka)}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-secondary fw-bold text-uppercase" style="font-size: 0.65rem;">Pemegang</label>
                                    <div class="fw-semibold text-dark">${escapeHtml(vehicle.pemegang)}</div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="p-3 border rounded-3 bg-white d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                            <i class="bi bi-cash-stack fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-secondary d-block mb-0" style="font-size: 0.7rem;">Nilai Perolehan / Aset</small>
                                            <h5 class="fw-bold text-navy mb-0">${formatRupiah(vehicle.nilai_perolehan)}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        // Edit Modal Logic
        const editModal = document.getElementById('editVehicleModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const vehicle = JSON.parse(button.getAttribute('data-vehicle'));
                const form = document.getElementById('editVehicleForm');
                
                // Update Form Action
                const routeTemplate = "{{ route('vehicles.update', ':id') }}";
                form.action = routeTemplate.replace(':id', vehicle.id);

                // Populate Fields
                document.getElementById('edit_no_polisi').value = vehicle.no_polisi || '';
                document.getElementById('edit_vehicle_type_id').value = vehicle.vehicle_type_id || '';
                document.getElementById('edit_jenis_text').value = vehicle.jenis || '';
                document.getElementById('edit_merk').value = vehicle.merk || '';
                document.getElementById('edit_tipe').value = vehicle.tipe || '';
                document.getElementById('edit_opd_id').value = vehicle.opd_id || '';
                document.getElementById('edit_opd_text').value = vehicle.opd || '';
                document.getElementById('edit_pemegang').value = vehicle.pemegang || '';
                document.getElementById('edit_kondisi').value = vehicle.kondisi || 'Baik';
                document.getElementById('edit_status').value = vehicle.status || 'Tersedia';
                document.getElementById('edit_tahun_pembuatan').value = vehicle.tahun_pembuatan || '';
                document.getElementById('edit_warna').value = vehicle.warna || '';
                document.getElementById('edit_stnk_ada').value = vehicle.stnk_ada || 'Ada';
                document.getElementById('edit_bpkb_ada').value = vehicle.bpkb_ada || 'Ada';
                document.getElementById('edit_tgl_stnk').value = vehicle.tgl_stnk || '';
                document.getElementById('edit_tgl_perolehan').value = vehicle.tgl_perolehan || '';
                document.getElementById('edit_nilai_perolehan').value = vehicle.nilai_perolehan || '';
                document.getElementById('edit_no_mesin').value = vehicle.no_mesin || '';
                document.getElementById('edit_no_rangka').value = vehicle.no_rangka || '';
                document.getElementById('edit_keterangan').value = vehicle.keterangan || '';
            });
        }

        // Export/Import Scripts
        const exportButton = document.querySelector('[data-export-button]');
        if (exportButton) {
            exportButton.addEventListener('click', function () {
                const label = exportButton.querySelector('[data-export-label]');
                exportButton.classList.add('is-loading');
                if (label) label.textContent = 'Menyiapkan...';
                setTimeout(() => {
                    exportButton.classList.remove('is-loading');
                    if (label) label.textContent = 'Export';
                }, 2500);
            });
        }

        const importForm = document.querySelector('[data-import-form]');
        const importSubmit = document.querySelector('[data-import-submit]');
        if (importForm && importSubmit) {
            importForm.addEventListener('submit', function () {
                importSubmit.disabled = true;
                importSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengimport...';
            });
        }
    });
</script>
@endpush
@endsection
