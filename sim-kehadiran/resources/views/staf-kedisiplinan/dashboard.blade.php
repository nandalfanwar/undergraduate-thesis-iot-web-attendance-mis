@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Welcome --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="fw-bold">
                Selamat Datang, {{ auth()->user()->nama }}
            </h3>

            <p class="text-muted mb-2">
                Staf Kedisiplinan SDIT Daarul Fataa
            </p>

            <small class="text-secondary">
                {{ now()->translatedFormat('l, d F Y') }}
            </small>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">

        <div class="col-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <i class="ph ph-users text-success fs-2"></i>
                    <small class="text-muted">Total Guru/Tendik</small>
                    <h2 class="fw-bold" id="totalGuru">{{ $totalGuru }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="ph ph-check-circle text-success fs-2"></i>
                    <h3 id="hadir">{{ $hadir }}</h3>
                    <small>Hadir</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="ph ph-clock text-warning fs-2"></i>
                    <h3 id="terlambat">{{ $terlambat }}</h3>
                    <small>Terlambat</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="ph ph-x-circle text-danger fs-2"></i>
                    <h3 id="alpa">{{ $alpa }}</h3>
                    <small>Alpa</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="ph ph-first-aid-kit text-warning fs-2"></i>
                    <h3 id="sakit">{{ $sakit }}</h3>
                    <small>Sakit</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="ph ph-note-pencil text-primary fs-2"></i>
                    <h3>{{ $izin }}</h3>
                    <small>Izin</small>
                </div>
            </div>
        </div>

    </div>

    {{-- Riwayat --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">Riwayat Kehadiran Terbaru</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody id="riwayat-body">

                    @forelse($riwayat as $data)

                    @php
                        $status = $data->statusKehadiran->status_kehadiran_label;
                    @endphp

                    <tr>

                        <td>{{ $data->user->nama }}</td>

                        <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}</td>

                        <td>{{ $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('H:i') : '-' }}</td>

                        <td>{{ $data->waktu_keluar ? \Carbon\Carbon::parse($data->waktu_keluar)->format('H:i') : '-' }}</td>

                        <td>

                            <span class="badge
                                @if($status == 'Hadir') bg-success
                                @elseif($status == 'Alpa') bg-danger
                                @elseif($status == 'Sakit') bg-warning text-dark
                                @elseif($status == 'Izin') bg-info text-dark
                                @elseif($status == 'Cuti') bg-primary
                                @elseif($status == 'Tugas') bg-secondary
                                @elseif($status == 'Terlambat') bg-danger-subtle text-danger border border-danger
                                @else bg-light text-dark
                                @endif">
                                {{ $status }}
                            </span>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Belum ada data kehadiran.
                        </td>
                    </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>

let lastUpdate = null;

async function refreshDashboard(){

    try{

        const response = await fetch(
            "{{ route('staf-kedisiplinan.dashboard.refresh') }}"
        );

        const data = await response.json();

        if(lastUpdate === data.last_update){

            return;

        }

        lastUpdate = data.last_update;

        document.getElementById("totalGuru").textContent = data.totalGuru;
        document.getElementById("hadir").textContent = data.hadir;
        document.getElementById("alpa").textContent = data.alpa;
        document.getElementById("sakit").textContent = data.sakit;
        document.getElementById("izin").textContent = data.izin;

        if(document.getElementById("terlambat")){

            document.getElementById("terlambat").textContent = data.terlambat;

        }

        let html = "";

        data.riwayat.forEach(item=>{

            html += `
            <tr>

                <td>${item.nama}</td>

                <td>${item.tanggal}</td>

                <td>${item.masuk}</td>

                <td>${item.keluar}</td>

                <td>
                    <span class="badge ${item.badge}">
                        ${item.status}
                    </span>
                </td>

            </tr>
            `;

        });

        document.getElementById("riwayat-body").innerHTML = html;

    }catch(err){

        console.error(err);

    }

}

refreshDashboard();

setInterval(refreshDashboard,3000);

</script>
@endpush

@endsection