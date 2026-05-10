@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Tambah <span class="text-gradient">Kendaraan Baru</span></h2>
        <p class="text-secondary mb-0">Lengkapi formulir di bawah untuk mendaftarkan aset kendaraan.</p>
    </div>

    <form action="{{ route('vehicles.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            {{-- Data Utama --}}
            <div class="col-lg-8">
                <div class="premium-card p-4 h-100">
                    <h5 class="fw-bold mb-4">Informasi Kendaraan</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">No. Polisi (Plat)</label>
                            <input type="text" name="no_polisi" class="form-control @error('no_polisi') is-invalid @enderror" value="{{ old('no_polisi') }}" placeholder="Contoh: DN 1234 XY">
                            @error('no_polisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Jenis Kendaraan</label>
                            <select name="jenis" class="form-select @error('jenis') is-invalid @enderror">
                                <option value="Mobil" {{ old('jenis') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                <option value="Motor" {{ old('jenis') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                <option value="Bus" {{ old('jenis') == 'Bus' ? 'selected' : '' }}>Bus</option>
                                <option value="Truck" {{ old('jenis') == 'Truck' ? 'selected' : '' }}>Truck</option>
                            </select>
                            @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Merk</label>
                            <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror" value="{{ old('merk') }}" placeholder="Contoh: Toyota">
                            @error('merk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Tipe / Model</label>
                            <input type="text" name="tipe" class="form-control @error('tipe') is-invalid @enderror" value="{{ old('tipe') }}" placeholder="Contoh: Innova Zenix">
                            @error('tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">Tahun Pembuatan</label>
                            <input type="number" name="tahun_pembuatan" class="form-control @error('tahun_pembuatan') is-invalid @enderror" value="{{ old('tahun_pembuatan') }}" placeholder="2024">
                            @error('tahun_pembuatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">Warna</label>
                            <input type="text" name="warna" class="form-control @error('warna') is-invalid @enderror" value="{{ old('warna') }}" placeholder="Contoh: Hitam Metalik">
                            @error('warna') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">Tgl Jatuh Tempo STNK</label>
                            <input type="date" name="tgl_stnk" class="form-control @error('tgl_stnk') is-invalid @enderror" value="{{ old('tgl_stnk') }}">
                            @error('tgl_stnk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-uppercase">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan jika ada...">{{ old('keterangan') }}</textarea>
                            @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Penanggung Jawab --}}
            <div class="col-lg-4">
                <div class="premium-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Pemegang & OPD</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">OPD / Dinas</label>
                        <input type="text" name="opd" class="form-control @error('opd') is-invalid @enderror" value="{{ old('opd') }}" placeholder="Contoh: Bapenda Sulteng">
                        @error('opd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">Nama Pemegang</label>
                        <input type="text" name="pemegang" class="form-control @error('pemegang') is-invalid @enderror" value="{{ old('pemegang') }}" placeholder="Contoh: Budi Santoso">
                        @error('pemegang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">Status Kendaraan</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="Tersedia" {{ old('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Digunakan" {{ old('status') == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
                            <option value="Rusak" {{ old('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Dilelang" {{ old('status') == 'Dilelang' ? 'selected' : '' }}>Dilelang</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="premium-card p-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-premium py-3 fs-5">Simpan Data</button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-light py-2 fw-bold text-secondary">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
