@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Edit <span class="text-gradient">Data Kendaraan</span></h2>
        <p class="text-secondary mb-0">Memperbarui informasi untuk kendaraan <strong>{{ $vehicle->no_polisi }}</strong>.</p>
    </div>

    <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            {{-- Data Utama --}}
            <div class="col-lg-8">
                <div class="premium-card p-4 h-100">
                    <h5 class="fw-bold mb-4">Informasi Kendaraan</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">No. Polisi (Plat)</label>
                            <input type="text" name="no_polisi" class="form-control @error('no_polisi') is-invalid @enderror" value="{{ old('no_polisi', $vehicle->no_polisi) }}">
                            @error('no_polisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Jenis Kendaraan</label>
                            <select name="jenis" class="form-select @error('jenis') is-invalid @enderror">
                                @foreach(['Mobil', 'Motor', 'Bus', 'Truck'] as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis', $vehicle->jenis) == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                            @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Merk</label>
                            <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror" value="{{ old('merk', $vehicle->merk) }}">
                            @error('merk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Tipe / Model</label>
                            <input type="text" name="tipe" class="form-control @error('tipe') is-invalid @enderror" value="{{ old('tipe', $vehicle->tipe) }}">
                            @error('tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">Tahun Pembuatan</label>
                            <input type="number" name="tahun_pembuatan" class="form-control @error('tahun_pembuatan') is-invalid @enderror" value="{{ old('tahun_pembuatan', $vehicle->tahun_pembuatan) }}">
                            @error('tahun_pembuatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">Warna</label>
                            <input type="text" name="warna" class="form-control @error('warna') is-invalid @enderror" value="{{ old('warna', $vehicle->warna) }}">
                            @error('warna') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">Tgl Jatuh Tempo STNK</label>
                            <input type="date" name="tgl_stnk" class="form-control @error('tgl_stnk') is-invalid @enderror" value="{{ old('tgl_stnk', $vehicle->tgl_stnk) }}">
                            @error('tgl_stnk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-uppercase">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $vehicle->keterangan) }}</textarea>
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
                        <input type="text" name="opd" class="form-control @error('opd') is-invalid @enderror" value="{{ old('opd', $vehicle->opd) }}">
                        @error('opd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">Nama Pemegang</label>
                        <input type="text" name="pemegang" class="form-control @error('pemegang') is-invalid @enderror" value="{{ old('pemegang', $vehicle->pemegang) }}">
                        @error('pemegang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">Status Kendaraan</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['Tersedia', 'Digunakan', 'Rusak', 'Dilelang'] as $status)
                                <option value="{{ $status }}" {{ old('status', $vehicle->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="premium-card p-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-premium py-3 fs-5">Simpan Perubahan</button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-light py-2 fw-bold text-secondary">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
