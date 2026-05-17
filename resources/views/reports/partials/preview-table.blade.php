@php
    $empty = $data->isEmpty();
@endphp

<x-table-card
    title="Hasil Pratinjau Laporan"
    subtitle="Data ter-agregasi berdasarkan filter aktif saat ini."
    :collection="$data"
    :empty="$empty"
    emptyText="Tidak ada kendaraan dinas yang cocok dengan kriteria filter."
    emptyIcon="bi-search"
>
    <!-- Slot Header -->
    <x-slot:thead>
        <tr>
            <th class="text-center" style="width: 60px;">#</th>
            @foreach($headers as $key => $label)
                <th class="text-navy fw-semibold">{{ $label }}</th>
            @endforeach
        </tr>
    </x-slot:thead>

    <!-- Slot Action (Tombol Ekspor Terproteksi) -->
    <x-slot:actions>
        @if(!$empty)
            <div class="action-toolbar d-flex gap-2">
                <button type="button" onclick="exportExcel()" class="btn btn-action btn-action-success btn-sm shadow-sm fw-semibold d-inline-flex align-items-center gap-2">
                    <span class="btn-action-icon"><i class="bi bi-file-earmark-excel"></i></span>
                    <span>Excel</span>
                </button>
                <button type="button" onclick="printReport()" class="btn btn-action btn-action-primary btn-sm shadow-sm fw-semibold d-inline-flex align-items-center gap-2">
                    <span class="btn-action-icon"><i class="bi bi-printer"></i></span>
                    <span>Cetak</span>
                </button>
            </div>
        @endif
    </x-slot:actions>

    <!-- Daftar Isi Data -->
    @foreach($data as $index => $row)
        <tr>
            <td class="text-center text-secondary small fw-medium">
                {{ $data->firstItem() + $index }}
            </td>
            @foreach($headers as $key => $label)
                <td>
                    @if($key === 'no_polisi')
                        <span class="plate-number">{{ strtoupper(trim($row->{$key})) }}</span>
                    @elseif($key === 'nilai_perolehan')
                        <span class="fw-bold text-navy">
                            Rp{{ number_format($row->{$key}, 0, ',', '.') }}
                        </span>
                    @elseif($key === 'kondisi')
                        @php
                            $badgeClass = match($row->{$key}) {
                                'Baik' => 'bg-success-subtle text-success border border-success-subtle',
                                'Rusak Ringan' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                'Rusak Berat' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5 rounded-pill small fw-semibold">
                            {{ $row->{$key} }}
                        </span>
                    @elseif($key === 'status')
                        @php
                            $badgeClass = match($row->{$key}) {
                                'Tersedia', 'Aktif' => 'bg-info-subtle text-info border border-info-subtle',
                                'Digunakan', 'Dipinjam' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5 rounded-pill small fw-semibold">
                            {{ $row->{$key} }}
                        </span>
                    @elseif($key === 'stnk_ada' || $key === 'bpkb_ada')
                        @php
                            $isAda = strtolower($row->{$key}) === 'ada';
                            $badgeClass = $isAda ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5 rounded small fw-semibold">
                            <i class="bi {{ $isAda ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                            {{ $row->{$key} }}
                        </span>
                    @elseif($key === 'tgl_stnk')
                        @if($row->{$key})
                            @php
                                $date = \Carbon\Carbon::parse($row->{$key});
                                $isExpired = $date->isPast();
                            @endphp
                            <span class="{{ $isExpired ? 'text-danger fw-semibold' : 'text-secondary' }}">
                                {{ $date->translatedFormat('d M Y') }}
                                @if($isExpired)
                                    <span class="badge bg-danger ms-1 text-white small px-1.5 py-0.5 rounded" style="font-size: 0.65rem;">Mati</span>
                                @endif
                            </span>
                        @else
                            <span class="text-secondary small italic">-</span>
                        @endif
                    @else
                        {{ $row->{$key} ?? '-' }}
                    @endif
                </td>
            @endforeach
        </tr>
    @endforeach

    <!-- Slot Paginasi -->
    <x-slot:pagination>
        {!! $data->links('pagination::bootstrap-5') !!}
    </x-slot:pagination>
</x-table-card>
