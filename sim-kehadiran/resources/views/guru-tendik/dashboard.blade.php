@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="fw-bold">
                Selamat Datang, {{ auth()->user()->nama }}
            </h3>

            <p class="text-muted mb-0">
                Hari ini {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Kehadiran Hari Ini --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body p-4">

            <div class="row text-center">

                <div class="col-md-6 border-end">

                    <small class="text-muted">
                        Tap In
                    </small>

                    <h2 class="fw-bold text-success mt-2">
                        {{ $today?->waktu_masuk
                            ? \Carbon\Carbon::parse($today->waktu_masuk)->format('H:i')
                            : '-' }}
                    </h2>

                </div>

                <div class="col-md-6">

                    <small class="text-muted">
                        Tap Out
                    </small>

                    <h2 class="fw-bold text-danger mt-2">
                        {{ $today?->waktu_keluar
                            ? \Carbon\Carbon::parse($today->waktu_keluar)->format('H:i')
                            : '-' }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    {{-- Riwayat --}}
    <!-- <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                Data Kehadiran Minggu Ini
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                    <tr>

                        <th>Tanggal</th>
                        <th>Tap In</th>
                        <th>Tap Out</th>
                        <th>Status</th>
                        <th>Keterangan</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($riwayat as $data)

                        @php
                            $status = $data->statusKehadiran->status_kehadiran_label;
                        @endphp

                        <tr>

                            <td>
                                {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l, d F Y') }}
                            </td>

                            <td>
                                {{ $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('H:i') : '-' }}
                            </td>

                            <td>
                                {{ $data->waktu_keluar ? \Carbon\Carbon::parse($data->waktu_keluar)->format('H:i') : '-' }}
                            </td>

                            <td>

                                <span class="badge
                                    @if($status == 'Hadir') bg-success
                                    @elseif($status == 'Terlambat') bg-warning text-dark
                                    @elseif($status == 'Alpa') bg-danger
                                    @elseif($status == 'Sakit') bg-info
                                    @elseif($status == 'Izin') bg-primary
                                    @endif">

                                    {{ $status }}

                                </span>

                            </td>

                            <td>
                                {{ $data->keterangan ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center text-muted py-4">

                                Belum ada data kehadiran.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div> -->

    {{-- Riwayat Kehadiran Minggu Ini --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-transparent border-0 pt-3 pb-2 px-4">
        <h5 class="fw-bold text-dark mb-0">
            Data Kehadiran Minggu Ini 
        </h5>
        <p class="text-muted fw-normal ms-1" style="font-size: 0.85rem;">
                ({{ \Carbon\Carbon::now()->startOfWeek()->translatedFormat('d M') }} - {{ \Carbon\Carbon::now()->endOfWeek()->translatedFormat('d M Y') }})
        </p>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Tap In</th>
                        <th>Tap Out</th>
                        <th>Status</th>
                        <th class="pe-4">Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($riwayat as $data)
                        @php
                            $status = $data->statusKehadiran->status_kehadiran_label;

                            $badge = match ($status) {
                                'Hadir'     => 'bg-success-subtle text-success',
                                'Terlambat' => 'bg-warning-subtle text-warning-emphasis',
                                'Alpa'      => 'bg-danger-subtle text-danger',
                                'Sakit'     => 'bg-warning-subtle text-warning-emphasis',
                                'Izin'      => 'bg-info-subtle text-info-emphasis',
                                'Cuti'      => 'bg-primary-subtle text-primary',
                                'Tugas'     => 'bg-secondary-subtle text-secondary',
                                default     => 'bg-light text-dark',
                            };
                        @endphp

                        <tr>
                            <td class="ps-4">
                                {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l, d M Y') }}
                            </td>

                            <td>
                                {{ $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('H:i') : '-' }}
                            </td>

                            <td>
                                {{ $data->waktu_keluar ? \Carbon\Carbon::parse($data->waktu_keluar)->format('H:i') : '-' }}
                            </td>

                            <td>
                                <span class="badge {{ $badge }} rounded-2 px-2.5 py-1.5 fw-medium">
                                    {{ $status }}
                                </span>
                            </td>

                            <td class="pe-4 text-muted">
                                {{ $data->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada data kehadiran untuk minggu ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

@endsection