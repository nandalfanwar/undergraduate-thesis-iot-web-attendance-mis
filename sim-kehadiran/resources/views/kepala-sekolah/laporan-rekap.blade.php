@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">

<!-- Filter -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body">

        <form method="GET" action="{{ route('kepala-sekolah.laporan-rekapitulasi') }}">

            <div class="row g-3">

                {{-- Guru / Tendik --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        Guru / Tendik
                    </label>

                    <select name="nama" class="form-select form-select-sm">
                        <option value="">
                            Semua Guru / Tendik
                        </option>

                        {{-- Mengecek variabel $guru atau $guruList agar tidak error --}}
                        @php
                            $listGuru = $guru ?? $guruList ?? [];
                        @endphp

                        @foreach($listGuru as $item)
                            <option value="{{ $item->nama }}" {{ request('nama') == $item->nama ? 'selected' : '' }}>
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        Status
                    </label>

                    <select name="status" class="form-select form-select-sm">
                        <option value="">
                            Semua Status
                        </option>

                        @if(isset($statusList))
                            @foreach($statusList as $item)
                                <option value="{{ $item->id }}" {{ request('status') == $item->id ? 'selected' : '' }}>
                                    {{ $item->status_kehadiran_label }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Bulan --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        Bulan
                    </label>

                    <select name="bulan" class="form-select form-select-sm">
                        <option value="">
                            Semua Bulan
                        </option>

                        @if(isset($bulan))
                            @foreach($bulan as $key => $value)
                                <option value="{{ $key }}" 
                                    {{ (string)($selectedBulan ?? request('bulan', date('n'))) === (string)$key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        Tahun
                    </label>

                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">
                            Semua Tahun
                        </option>

                        @for($tahun = date('Y'); $tahun >= 2023; $tahun--)
                            <option value="{{ $tahun }}" 
                                {{ (string)($selectedTahun ?? request('tahun', date('Y'))) === (string)$tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endfor
                    </select>
                </div>

            </div>

            <div class="row mt-3">

                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm fw-medium px-3">
                            <i class="ph ph-funnel me-1"></i> Filter
                        </button>

                        <a href="{{ route('kepala-sekolah.laporan-rekapitulasi') }}" class="btn btn-light btn-sm border text-muted fw-medium px-3">
                            <i class="ph ph-arrow-counter-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>

                <div class="col-md-6 text-end">
                    <a href="{{ route('kepala-sekolah.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm me-2 fw-medium">
                        <i class="ph ph-file-xls me-1"></i> Export Excel
                    </a>

                    <a href="{{ route('kepala-sekolah.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm fw-medium">
                        <i class="ph ph-file-pdf me-1"></i> Export PDF
                    </a>
                </div>

            </div>

        </form>

    </div>
</div>

    {{-- Stat Cards Ringkasan Periode Ini --}}
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-7 g-2 mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm text-center p-2 bg-white h-100">
                <small class="text-muted d-block" style="font-size: 0.75rem;">Hadir</small>
                <h4 class="fw-bold text-success mb-0">{{ $hadir }}</h4>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center p-2 bg-white h-100">
                <small class="text-muted d-block" style="font-size: 0.75rem;">Terlambat</small>
                <h4 class="fw-bold text-warning-emphasis mb-0">{{ $terlambat }}</h4>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center p-2 bg-white h-100">
                <small class="text-muted d-block" style="font-size: 0.75rem;">Alpa</small>
                <h4 class="fw-bold text-danger mb-0">{{ $alpa }}</h4>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center p-2 bg-white h-100">
                <small class="text-muted d-block" style="font-size: 0.75rem;">Sakit</small>
                <h4 class="fw-bold text-warning mb-0">{{ $sakit }}</h4>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center p-2 bg-white h-100">
                <small class="text-muted d-block" style="font-size: 0.75rem;">Izin</small>
                <h4 class="fw-bold text-primary mb-0">{{ $izin }}</h4>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center p-2 bg-white h-100">
                <small class="text-muted d-block" style="font-size: 0.75rem;">Cuti</small>
                <h4 class="fw-bold text-info mb-0">{{ $cuti }}</h4>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center p-2 bg-white h-100">
                <small class="text-muted d-block" style="font-size: 0.75rem;">Tugas</small>
                <h4 class="fw-bold text-secondary mb-0">{{ $tugas }}</h4>
            </div>
        </div>
    </div>

    {{-- Tabel Data Laporan --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Guru / Tendik</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Status</th>
                            <th class="pe-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $item)
                            @php
                                $status = $item->statusKehadiran->status_kehadiran_label ?? 'Alpa';

                                $badge = match ($status) {
                                    'Hadir' => 'bg-success',
                                    'Terlambat' => 'bg-warning text-dark',
                                    'Alpa' => 'bg-danger',
                                    'Sakit' => 'bg-warning text-dark',
                                    'Izin' => 'bg-primary',
                                    'Cuti' => 'bg-info text-dark',
                                    'Tugas', 'Tugas Luar' => 'bg-secondary',
                                    default => 'bg-secondary',
                                };
                            @endphp

                            <tr>
                                <td class="ps-3 fw-medium text-dark">
                                    {{ $item->user->nama ?? $item->user->name ?? '-' }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
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
                                <td class="pe-3 text-muted small">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Tidak ada data rekapitulasi yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(method_exists($laporan, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $laporan->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection