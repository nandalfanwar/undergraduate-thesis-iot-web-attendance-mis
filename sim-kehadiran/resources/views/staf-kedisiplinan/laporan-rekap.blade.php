@extends('layouts.app')

@section('content')

<div>
        <h3 class="fw-bold mb-1">
            Laporan & Rekapitulasi
        </h3>

        <p class="text-muted mb-0">
            Laporan dan rekapitulasi seluruh guru/tendik SDIT Daarul Faataa.
        </p>
</div>

@php
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
@endphp

<!-- <div class="card mb-3">
    
    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-3">

                    <label class="form-label">
                        Guru/Tendik
                    </label>

                    <select
                        name="nama"
                        class="form-select">

                        <option value="">
                            Semua Guru / Tendik
                        </option>

                        @foreach($guru as $item)

                            <option
                                value="{{ $item->nama }}"
                                {{ request('nama') == $item->nama ? 'selected' : '' }}>

                                {{ $item->nama }}

                            </option>

                        @endforeach

                    </select>

                </div>  

                <div class="col-md-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option value="">
                            Semua Status
                        </option>

                        @foreach($statusList as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ request('status') == $item->id ? 'selected' : '' }}>

                                {{ $item->status_kehadiran_label }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">Bulan</label>

                    <select
                        name="bulan"
                        class="form-select">

                        <option value="">
                            Semua Bulan
                        </option>

                        @foreach($bulan as $key => $value)

                            <option
                                value="{{ $key }}"
                                {{ request('bulan') == $key ? 'selected' : '' }}>

                                {{ $value }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">Tahun</label>

                    <select
                        name="tahun"
                        class="form-select">

                        <option value="">
                            Semua Tahun
                        </option>

                        @for($tahun = date('Y'); $tahun >= 2023; $tahun--)

                            <option
                                value="{{ $tahun }}"
                                {{ request('tahun') == $tahun ? 'selected' : '' }}>

                                {{ $tahun }}

                            </option>

                        @endfor

                    </select>

                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">

                    <button
                        type="submit"
                        class="btn btn-success w-100">

                        Filter

                    </button>

                    <a
                        href="{{ route('staf-kedisiplinan.laporan-rekapitulasi') }}"
                        class="btn btn-secondary w-100">

                        Reset

                    </a>

                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">

                    <a
                        href="{{ route('staf-kedisiplinan.export.excel', request()->query()) }}"
                        class="btn btn-primary w-100">

                        Export Excel

                    </a>
                </div>

            </div>

        </form>

    </div>

</div> -->

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">

        <form method="GET">

            <div class="row g-3">

                {{-- Guru / Tendik --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Guru / Tendik
                    </label>

                    <select
                        name="nama"
                        class="form-select">

                        <option value="">
                            Semua Guru / Tendik
                        </option>

                        @foreach($guru as $item)

                            <option
                                value="{{ $item->nama }}"
                                {{ request('nama') == $item->nama ? 'selected' : '' }}>

                                {{ $item->nama }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Status --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option value="">
                            Semua Status
                        </option>

                        @foreach($statusList as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ request('status') == $item->id ? 'selected' : '' }}>

                                {{ $item->status_kehadiran_label }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Bulan --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Bulan
                    </label>

                    <select
                        name="bulan"
                        class="form-select">

                        <option value="">
                            Semua Bulan
                        </option>

                        @foreach($bulan as $key => $value)

                            <option
                                value="{{ $key }}"
                                {{ request('bulan') == $key ? 'selected' : '' }}>

                                {{ $value }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Tahun --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Tahun
                    </label>

                    <select
                        name="tahun"
                        class="form-select">

                        <option value="">
                            Semua Tahun
                        </option>

                        @for($tahun = date('Y'); $tahun >= 2023; $tahun--)

                            <option
                                value="{{ $tahun }}"
                                {{ request('tahun') == $tahun ? 'selected' : '' }}>

                                {{ $tahun }}

                            </option>

                        @endfor

                    </select>

                </div>

            </div>

            <div class="row mt-3">

                <div class="col-md-6">

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-success">

                            Filter

                        </button>

                        <a
                            href="{{ route('staf-kedisiplinan.laporan-rekapitulasi') }}"
                            class="btn btn-secondary">

                            Reset

                        </a>

                    </div>

                </div>

                <div class="col-md-6 text-end">

                    <a
                        href="{{ route('staf-kedisiplinan.export.excel', request()->query()) }}"
                        class="btn btn-success me-2">
                        <i class="ph ph-file-xls text-white me-1"></i>
                            Export Excel

                    </a>

                    <a
                        href="{{ route('staf-kedisiplinan.export.pdf', request()->query()) }}"
                        class="btn btn-danger">

                        <i class="ph ph-file-pdf text-white me-1"></i>
                            Export PDF      

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- Rekapitulasi -->
<!-- <div class="row mb-4 g-3">
    <div class="col-md-2">
        <div class="card text-center border-success">
            <div class="card-body">
                <h6>Hadir</h6>
                <h3 class="text-success">{{ $hadir }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center border-secondary">
            <div class="card-body">
                <h6>Terlambat</h6>
                <h3 class="text-secondary">{{ $terlambat }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center border-danger">
            <div class="card-body">
                <h6>Alpa</h6>
                <h3 class="text-danger">{{ $alpa }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h6>Sakit</h6>
                <h3 class="text-warning">{{ $sakit }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center border-info">
            <div class="card-body">
                <h6>Izin</h6>
                <h3 class="text-info">{{ $izin }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h6>Cuti</h6>
                <h3 class="text-primary">{{ $cuti }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center border-secondary">
            <div class="card-body">
                <h6>Tugas</h6>
                <h3 class="text-secondary">{{ $tugas }}</h3>
            </div>
        </div>
    </div>

</div> -->

<div class="row row-cols-2 row-cols-md-4 row-cols-xl-7 g-3 mb-4">

    <div class="col">
        <div class="card text-center h-100 border-success">
            <div class="card-body">
                <h6 class="mb-2">Hadir</h6>
                <h3 class="text-success mb-0">{{ $hadir }}</h3>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card text-center h-100 border-secondary">
            <div class="card-body">
                <h6 class="mb-2">Terlambat</h6>
                <h3 class="text-secondary mb-0">{{ $terlambat }}</h3>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card text-center h-100 border-danger">
            <div class="card-body">
                <h6 class="mb-2">Alpa</h6>
                <h3 class="text-danger mb-0">{{ $alpa }}</h3>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card text-center h-100 border-warning">
            <div class="card-body">
                <h6 class="mb-2">Sakit</h6>
                <h3 class="text-warning mb-0">{{ $sakit }}</h3>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card text-center h-100 border-info">
            <div class="card-body">
                <h6 class="mb-2">Izin</h6>
                <h3 class="text-info mb-0">{{ $izin }}</h3>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card text-center h-100 border-primary">
            <div class="card-body">
                <h6 class="mb-2">Cuti</h6>
                <h3 class="text-primary mb-0">{{ $cuti }}</h3>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card text-center h-100 border-dark">
            <div class="card-body">
                <h6 class="mb-2">Tugas</h6>
                <h3 class="text-dark mb-0">{{ $tugas }}</h3>
            </div>
        </div>
    </div>

</div>

<!-- Tabel Rekapitulasi -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">Daftar Riwayat Kehadiran</h5>
        </div>
    <div class="card-body">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Status</th>
                    <th>Keterangan</th>

                </tr>

            </thead>

            <tbody>

                @forelse($laporan as $item)

                    @php
                        $status = $item->statusKehadiran->status_kehadiran_label;

                        $badge = match ($status) {
                            'Hadir' => 'bg-success',
                            'Alpa' => 'bg-danger',
                            'Sakit' => 'bg-warning text-dark',
                            'Izin' => 'bg-info text-dark',
                            'Cuti' => 'bg-primary',
                            'Tugas' => 'bg-secondary',
                            default => 'bg-dark',
                        };
                    @endphp

                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                        </td>
                        <td>
                            {{ $item->user->nama }}
                        </td>
                        <td>
                            {{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') : '-' }}
                        </td>

                        <td>
                            {{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') : '-' }}
                        </td>

                        <td>

                            <span class="badge {{ $badge }}">

                                {{ $status }}

                            </span>

                        </td>

                        <td>

                            {{ $item->keterangan ?? '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center">

                            Tidak ada data.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        {{ $laporan->links() }}

    </div>

</div>

@endsection