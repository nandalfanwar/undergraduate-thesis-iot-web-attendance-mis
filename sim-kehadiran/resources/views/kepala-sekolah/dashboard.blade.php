@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="fw-bold mb-1">
                Selamat Datang, {{ auth()->user()->nama ?? auth()->user()->name }}
            </h3>
            <p class="text-muted mb-0 small">
                Kepala Sekolah SDIT Daarul Fataa — Hari ini {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Grouped Stat Cards --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Ringkasan Kehadiran Hari Ini</h5>
                <small class="text-muted">Pantauan otomatis aktivitas presensi Guru & Tendik</small>
            </div>
            <a href="{{ route('kepala-sekolah.laporan-rekapitulasi') }}" class="text-decoration-none small fw-semibold text-primary">
                Selengkapnya <i class="ph ph-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="card-body pt-0">
            <div class="row g-3">
                {{-- Total Staf --}}
                <div class="col-md-2">
                    <div class="card border bg-light h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center p-3">
                            <i class="ph ph-users text-primary fs-1 mb-1"></i>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalGuru ?? 0 }}</h2>
                            <span class="text-muted small">Total Staf</span>
                        </div>
                    </div>
                </div>

                {{-- Group HADIR --}}
                <div class="col-md-4">
                    <div class="card border border-success-subtle bg-success-subtle h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-success d-flex align-items-center">
                                    <i class="ph ph-check-circle fs-5 me-1"></i> HADIR
                                </span>
                                <span class="badge bg-success fs-6">{{ ($hadir ?? 0) + ($terlambat ?? 0) }}</span>
                            </div>
                            <hr class="my-2 text-success opacity-25">
                            <div class="row text-center pt-1">
                                <div class="col-6 border-end border-success-subtle">
                                    <h4 class="fw-bold text-success mb-0">{{ $hadir ?? 0 }}</h4>
                                    <small class="text-muted" style="font-size: 0.75rem;">Tepat Waktu</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="fw-bold text-warning-emphasis mb-0">{{ $terlambat ?? 0 }}</h4>
                                    <small class="text-muted" style="font-size: 0.75rem;">Terlambat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Group TIDAK HADIR --}}
                <div class="col-md-6">
                    <div class="card border border-danger-subtle bg-danger-subtle h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-danger d-flex align-items-center">
                                    <i class="ph ph-x-circle fs-5 me-1"></i> TIDAK HADIR
                                </span>
                                <span class="badge bg-danger fs-6">
                                    {{ ($alpa ?? 0) + ($sakit ?? 0) + ($izin ?? 0) + ($cuti ?? 0) + ($tugas ?? 0) }}
                                </span>
                            </div>
                            <hr class="my-2 text-danger opacity-25">
                            <div class="row text-center pt-1 g-1">
                                <div class="col border-end border-danger-subtle">
                                    <h4 class="fw-bold text-danger mb-0">{{ $alpa ?? 0 }}</h4>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Alpa</small>
                                </div>
                                <div class="col border-end border-danger-subtle">
                                    <h4 class="fw-bold text-warning mb-0">{{ $sakit ?? 0 }}</h4>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Sakit</small>
                                </div>
                                <div class="col border-end border-danger-subtle">
                                    <h4 class="fw-bold text-primary mb-0">{{ $izin ?? 0 }}</h4>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Izin</small>
                                </div>
                                <div class="col border-end border-danger-subtle">
                                    <h4 class="fw-bold text-info mb-0">{{ $cuti ?? 0 }}</h4>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Cuti</small>
                                </div>
                                <div class="col">
                                    <h4 class="fw-bold text-secondary mb-0">{{ $tugas ?? 0 }}</h4>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Tugas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> 
        </div> 
    </div> 

    {{-- Tabel Riwayat Kehadiran Hari Ini --}}
    <!-- <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Riwayat Kehadiran Hari Ini</h5>
            <small class="text-muted">Diperbarui secara real-time</small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Nama Guru / Tendik</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatHariIni as $data)
                            @php
                                $status = $data->statusKehadiran->status_kehadiran_label ?? 'Alpa';
                            @endphp
                            <tr>
                                <td class="ps-3 fw-medium text-dark">
                                    {{ $data->user->nama ?? $data->user->name }}
                                </td>
                                <td>{{ $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('H:i') : '-' }}</td>
                                <td>{{ $data->waktu_keluar ? \Carbon\Carbon::parse($data->waktu_keluar)->format('H:i') : '-' }}</td>
                                <td>
                                    <span class="badge 
                                        @if($status=='Hadir') bg-success 
                                        @elseif($status=='Terlambat') bg-warning text-dark
                                        @elseif($status=='Alpa') bg-danger 
                                        @elseif($status=='Sakit') bg-warning text-dark 
                                        @elseif($status=='Izin') bg-primary 
                                        @elseif($status=='Cuti') bg-info text-dark
                                        @elseif($status=='Tugas' || $status=='Tugas Luar') bg-secondary 
                                        @else bg-secondary @endif">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Belum ada aktivitas presensi untuk hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->

    <div class="card-body p-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Riwayat Kehadiran Hari Ini</h5>
            <small class="text-muted">Diperbarui secara real-time</small>
        </div>
    <!-- Tambahkan style max-height dan overflow-y di sini -->
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top"> <!-- Optional: sticky-top agar header tetap di atas saat di-scroll -->
                    <tr>
                        <th class="ps-3">Nama Guru/Tendik</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatHariIni as $data)
                        @php
                            $status = $data->statusKehadiran->status_kehadiran_label ?? 'Alpa';
                        @endphp
                        <tr>
                            <td class="ps-3 fw-medium text-dark">
                                {{ $data->user->nama ?? $data->user->name }}
                            </td>
                            <td>{{ $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('H:i') : '-' }}</td>
                            <td>{{ $data->waktu_keluar ? \Carbon\Carbon::parse($data->waktu_keluar)->format('H:i') : '-' }}</td>
                            <td>
                                <span class="badge 
                                    @if($status=='Hadir') bg-success 
                                    @elseif($status=='Terlambat') bg-warning text-dark
                                    @elseif($status=='Alpa') bg-danger 
                                    @elseif($status=='Sakit') bg-warning text-dark 
                                    @elseif($status=='Izin') bg-primary 
                                    @elseif($status=='Cuti') bg-info text-dark
                                    @elseif($status=='Tugas' || $status=='Tugas Luar') bg-secondary 
                                    @else bg-secondary @endif">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Belum ada aktivitas presensi untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection