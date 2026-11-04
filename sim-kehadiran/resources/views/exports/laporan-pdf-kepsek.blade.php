<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            vertical-align: middle;
        }

        /* =====================
                KOP
        ===================== */

        .kop td {
            border: none;
        }

        .logo {
            width: 85px;
        }

        .calligraphy {
            height: 35px;
        }

        .nama-sekolah {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.3;
        }

        .alamat {
            font-size: 8.5px;
            color: #222;
            line-height: 1.4;
        }

        .garis-kop {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin-top: 8px;
            margin-bottom: 18px;
        }

        /* =====================
                JUDUL
        ===================== */

        .judul {
            text-align: center;
            margin-bottom: 15px;
        }

        .judul h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }

        /* =====================
                INFO
        ===================== */

        .info {
            margin-bottom: 15px;
        }

        .info td {
            border: none;
            padding: 2px;
        }

        /* =====================
                TABEL
        ===================== */

        .laporan th {
            background: #ececec;
            border: 1px solid #000;
            padding: 6px;
            font-size: 10px;
            text-align: center;
        }

        .laporan td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 9px;
        }

        .laporan tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* =====================
                REKAP
        ===================== */

        .rekap {
            margin-top: 18px;
        }

        .rekap td {
            border: 1px solid #000;
            padding: 6px 3px;
            text-align: center;
            font-size: 9px;
        }

        /* =====================
                FOOTER TTD
        ===================== */

        .footer {
            margin-top: 40px;
        }

        .footer td {
            border: none;
            text-align: center;
            font-size: 10px;
            line-height: 1.6;
        }

        .info-export {
            margin-top: 25px;
            font-size: 9px;
            color: #555;
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- =====================
            KOP
    ===================== -->

    <table class="kop">

        <tr>
            <td colspan="2" align="center">
                <img src="{{ public_path('images/calligraphy-green.png') }}" class="calligraphy">
            </td>
        </tr>

        <tr>

            <td width="18%" align="center">
                <img src="{{ public_path('images/logo.png') }}" class="logo">
            </td>

            <td width="82%" align="center">

                <div class="nama-sekolah">
                    SEKOLAH DASAR ISLAM TERPADU DAARUL FATAA
                </div>

                <div class="alamat">
                    <strong>PUSAT :</strong>
                    Jl. Casablanca, Palbatu III Menteng Dalam,
                    Tebet, Jakarta Selatan
                    • Telp: (021) 8370 6279
                </div>

                <div class="alamat">
                    <strong>CABANG :</strong>
                    Pesantren Daarul Fataa,
                    Jl. Sudi Mampir RT 01/03,
                    Ciamanggis, Bojonggede
                    • Telp: (0251) 8551381, 8551386
                </div>

                <div class="alamat">
                    Website : www.sditdaarulfataa.sch.id
                    • Email : sdit_daarulfaataa@yahoo.co.id
                </div>

            </td>

        </tr>

    </table>

    <div class="garis-kop"></div>

    <!-- =====================
            JUDUL
    ===================== -->

    <div class="judul">
        <h3>REKAPITULASI PRESENSI GURU DAN TENAGA KEPENDIDIKAN</h3>
    </div>

    <!-- =====================
            FILTER / INFO
    ===================== -->

    <table class="info">

        <tr>
            <td width="18%">Periode</td>
            <td width="2%">:</td>
            <td>{{ $namaBulan }} {{ $selectedTahun }}</td>
        </tr>

        <tr>
            <td>Status Kehadiran</td>
            <td>:</td>
            <td>{{ request('status') ? ($laporan->first()->statusKehadiran->status_kehadiran_label ?? 'Filter Status') : 'Semua Status' }}</td>
        </tr>

    </table>

    <!-- =====================
            DETAIL TABLE
    ===================== -->

    <table class="laporan">

        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="23%">Nama Pegawai</th>
                <th width="12%">Masuk</th>
                <th width="12%">Keluar</th>
                <th width="14%">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @forelse($laporan as $item)

                <tr>

                    <td align="center">
                        {{ $loop->iteration }}
                    </td>

                    <td align="center">
                        {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                    </td>

                    <td>
                        {{ $item->user->nama ?? $item->user->name ?? '-' }}
                    </td>

                    <td align="center">
                        {{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') : '-' }}
                    </td>

                    <td align="center">
                        {{ $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') : '-' }}
                    </td>

                    <td align="center">
                        {{ $item->statusKehadiran->status_kehadiran_label ?? 'Alpa' }}
                    </td>

                    <td>
                        {{ $item->keterangan ?: '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" align="center">
                        Tidak ada data rekapitulasi presensi pada periode ini.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    <!-- =====================
            REKAP SUMMARY
    ===================== -->

    @php
        $rekapGroup = $laporan->groupBy(function ($item) {
            return $item->statusKehadiran->status_kehadiran_label ?? 'Lainnya';
        })->map(fn($item) => $item->count());
    @endphp

    <table class="rekap">

        <tr>
            <td><strong>Total Data</strong><br>{{ $laporan->count() }}</td>
            <td><strong>Hadir</strong><br>{{ $rekapGroup['Hadir'] ?? 0 }}</td>
            <td><strong>Terlambat</strong><br>{{ $rekapGroup['Terlambat'] ?? 0 }}</td>
            <td><strong>Sakit</strong><br>{{ $rekapGroup['Sakit'] ?? 0 }}</td>
            <td><strong>Izin</strong><br>{{ $rekapGroup['Izin'] ?? 0 }}</td>
            <td><strong>Cuti</strong><br>{{ $rekapGroup['Cuti'] ?? 0 }}</td>
            <td><strong>Tugas</strong><br>{{ $rekapGroup['Tugas'] ?? 0 }}</td>
            <td><strong>Alpa</strong><br>{{ $rekapGroup['Alpa'] ?? $rekapGroup['Alfa'] ?? 0 }}</td>
        </tr>

    </table>

    <!-- =====================
            TTD
    ===================== -->

    <table class="footer">

        <tr>

            <td width="50%"></td>

            <td width="50%">
                Mengetahui & Menyetujui,
                <br>
                Kepala Sekolah

                <br><br><br><br>

                <strong>
                    {{ auth()->user()->nama ?? auth()->user()->name }}
                </strong>
            </td>

        </tr>

    </table>

    <!-- =====================
            INFO EXPORT
    ===================== -->

    <div class="info-export">

        Diekspor pada:
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        pukul
        {{ \Carbon\Carbon::now()->format('H:i') }}
        WIB

        <br>

        Oleh:
        {{ auth()->user()->nama ?? auth()->user()->name }} (Kepala Sekolah)

    </div>

</body>

</html>