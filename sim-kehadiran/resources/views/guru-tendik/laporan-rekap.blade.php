@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h3 class="fw-bold mb-1">
        Laporan & Rekapitulasi
    </h3>

    <p class="text-muted mb-0">
        Akses laporan dan rekapitulasi kehadiran Anda di sini.
    </p>
</div>

@php
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp

<!-- Filter Card -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('guru-tendik.laporan-rekapitulasi') }}">
            <div class="row g-3 align-items-end">
                <!-- Select Bulan -->
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach($bulan as $key => $value)
                            <option value="{{ $key }}" {{ request('bulan', date('n')) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Tahun -->
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for($tahun = date('Y'); $tahun >= 2023; $tahun--)
                            <option value="{{ $tahun }}" {{ request('tahun', date('Y')) == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Tombol Filter & Reset -->
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ph ph-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('guru-tendik.laporan-rekapitulasi') }}" class="btn btn-secondary w-100">
                        Reset
                    </a>
                </div>

                <!-- Tombol Export Khusus Guru-Tendik -->
                <div class="col-md-3 text-end">
                    <a href="{{ route('guru-tendik.export.excel', request()->query()) }}" class="btn btn-success me-1">
                        <i class="ph ph-file-xls me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('guru-tendik.export.pdf', request()->query()) }}" class="btn btn-danger">
                        <i class="ph ph-file-pdf me-1"></i> Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Card Grafik Statistik-->
<div class="row mb-4 g-3">
    <!-- Grafik 1: Proporsi Status (Doughnut) -->
    <div class="col-md-5">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-muted mb-3">Kehadiran Berdasarkan Status</h6>
                <div style="position: relative; height: 260px;">
                    <canvas id="kehadiranChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik 2: Tren Waktu Presensi (Line Chart - Scrollable) -->
    <div class="col-md-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-muted mb-3">Kehadiran Berdasarkan Waktu</h6>
                <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                    <div style="position: relative; height: 260px; min-width: 700px;">
                        <canvas id="trenWaktuChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Rekapitulasi -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">Masuk</th>
                        <th class="py-3">Keluar</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                        @php
                            $status = $item->statusKehadiran->status_kehadiran_label ?? 'Alpa';

                            $badge = match ($status) {
                                'Hadir'     => 'bg-success-subtle text-success border border-success-subtle',
                                'Terlambat' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                'Alpa'      => 'bg-danger-subtle text-danger border border-danger-subtle',
                                'Sakit'     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                'Izin'      => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                'Cuti'      => 'bg-primary-subtle text-primary border border-primary-subtle',
                                'Tugas'     => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                default     => 'bg-light text-dark',
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                            </td>
                            <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') : '-' }}</td>
                            <td>{{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') : '-' }}</td>
                            <td>
                                <span class="badge {{ $badge }} rounded-pill px-3 py-2">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="pe-4">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($laporan->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-end">
                {{ $laporan->links() }}
            </div>
        </div>
    @endif
</div>

<script>
    window.addEventListener('load', function() {
        // 1. Grafik Doughnut (Proporsi Status)
        var elStatus = document.getElementById('kehadiranChart');
        if (elStatus) {
            new Chart(elStatus.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Terlambat', 'Alpa', 'Sakit', 'Izin', 'Cuti', 'Tugas'],
                    datasets: [{
                        data: [
                            {{ (int)($hadir ?? 0) }},
                            {{ (int)($terlambat ?? 0) }},
                            {{ (int)($alpa ?? 0) }},
                            {{ (int)($sakit ?? 0) }},
                            {{ (int)($izin ?? 0) }},
                            {{ (int)($cuti ?? 0) }},
                            {{ (int)($tugas ?? 0) }}
                        ],
                        backgroundColor: [
                            '#198754', '#fd7e14', '#dc3545', 
                            '#ffc107', '#0dcaf0', '#0d6efd', '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        // 2. Grafik Garis / Line Chart (Tren Jam Masuk & Pulang)
        var elWaktu = document.getElementById('trenWaktuChart');
        if (elWaktu) {
            var labelsTanggal = {!! json_encode($laporan->pluck('tanggal')->map(fn($t) => \Carbon\Carbon::parse($t)->format('d/m'))->reverse()->values()) !!};
            var dataMasuk     = {!! json_encode($laporan->pluck('waktu_masuk')->map(fn($w) => $w ? \Carbon\Carbon::parse($w)->format('H.i') : null)->reverse()->values()) !!};
            var dataKeluar    = {!! json_encode($laporan->pluck('waktu_keluar')->map(fn($w) => $w ? \Carbon\Carbon::parse($w)->format('H.i') : null)->reverse()->values()) !!};

            new Chart(elWaktu.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labelsTanggal,
                    datasets: [
                        {
                            label: 'Jam Masuk',
                            data: dataMasuk,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            spanGaps: true
                        },
                        {
                            label: 'Jam Keluar',
                            data: dataKeluar,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            spanGaps: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            suggestedMin: 6.00,
                            suggestedMax: 16.00,
                            ticks: {
                                callback: function(value) {
                                    return value.toFixed(2).replace('.', ':');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var val = context.raw;
                                    return val ? context.dataset.label + ': Jam ' + val.toString().replace('.', ':') : 'Tidak Ada Data';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

@endsection