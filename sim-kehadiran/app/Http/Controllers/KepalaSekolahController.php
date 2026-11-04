<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DataKehadiran;
use App\Models\StatusKehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;

class KepalaSekolahController extends Controller
{
    /**
     * Private Helper Query untuk Filter Laporan Presensi
     */
    private function getLaporanQuery(Request $request)
    {
        // Jika request tidak mengirimkan parameter bulan/tahun (pertama kali dibuka), default ke bulan & tahun saat ini
        $selectedBulan = $request->has('bulan') ? $request->bulan : date('n');
        $selectedTahun = $request->has('tahun') ? $request->tahun : date('Y');

        return DataKehadiran::with(['user', 'statusKehadiran'])
            ->when($request->filled('nama'), function ($q) use ($request) {
                $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('nama', 'like', '%' . $request->nama . '%');
                });
            })
            ->when($request->filled('status'), fn($q) => $q->where('status_kehadiran_id', $request->status))
            ->when(!empty($selectedBulan), fn($q) => $q->whereMonth('tanggal', $selectedBulan))
            ->when(!empty($selectedTahun), fn($q) => $q->whereYear('tanggal', $selectedTahun));
    }

    /**
     * Dashboard Harian Kepala Sekolah
     */
    public function dashboard()
    {
        $hariIni = Carbon::today();

        $totalGuru = User::where('role_id', 1)->count();

        // Count kehadiran hari ini per status
        $hadir     = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 1)->count();
        $alpa      = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 2)->count();
        $sakit     = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 3)->count();
        $izin      = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 4)->count();
        $cuti      = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 5)->count();
        $tugas     = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 6)->count();
        $terlambat = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 7)->count();

        $riwayatHariIni = DataKehadiran::with(['user', 'statusKehadiran'])
            ->whereDate('tanggal', $hariIni)
            ->orderByDesc('waktu_masuk')
            ->get();

        return view('kepala-sekolah.dashboard', compact(
            'totalGuru',
            'hadir',
            'terlambat',
            'alpa',
            'sakit',
            'izin',
            'cuti',
            'tugas',
            'riwayatHariIni'
        ));
    }

    /**
     * Laporan & Rekapitulasi Data Presensi
     */
    public function laporanRekapitulasi(Request $request)
    {
        // Tetapkan nilai default bulan dan tahun saat pertama kali halaman di-load
        $selectedBulan = $request->has('bulan') ? $request->bulan : date('n');
        $selectedTahun = $request->has('tahun') ? $request->tahun : date('Y');

        $query = $this->getLaporanQuery($request);

        // Data untuk Tabel dengan Paginasi & Query String Retention
        $laporan = (clone $query)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Data Dropdown Filter
        $guruList = User::whereIn('role_id', [1, 2])->orderBy('nama')->get();
        $statusList = StatusKehadiran::all();

        // Data List Bulan (WAJIB di-pass agar dropdown bulan di Blade terisi)
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Hitung Stat Cards berdasarkan Query Filter Aktif
        $hadir     = (clone $query)->where('status_kehadiran_id', 1)->count();
        $alpa      = (clone $query)->where('status_kehadiran_id', 2)->count();
        $sakit     = (clone $query)->where('status_kehadiran_id', 3)->count();
        $izin      = (clone $query)->where('status_kehadiran_id', 4)->count();
        $cuti      = (clone $query)->where('status_kehadiran_id', 5)->count();
        $tugas     = (clone $query)->where('status_kehadiran_id', 6)->count();
        $terlambat = (clone $query)->where('status_kehadiran_id', 7)->count();

        return view('kepala-sekolah.laporan-rekap', compact(
            'laporan',
            'guruList',
            'statusList',
            'bulan', // Ditambahkan ke compact
            'hadir',
            'alpa',
            'sakit',
            'izin',
            'cuti',
            'tugas',
            'terlambat',
            'selectedBulan',
            'selectedTahun'
        ));
    }

    /**
     * Export Excel Seluruh / Filtered Data Presensi
     */
    public function exportExcel(Request $request)
    {
        $laporan = $this->getLaporanQuery($request)->orderBy('tanggal', 'desc')->get();

        $bulanParam = $request->input('bulan', date('n'));
        $namaBulan = $bulanParam ? Carbon::create()->month((int)$bulanParam)->translatedFormat('F') : 'Semua Bulan';
        $tahun = $request->input('tahun', date('Y'));

        $filterInfo = [
            'bulan' => $namaBulan,
            'tahun' => $tahun,
            'guru'  => $request->filled('nama') ? $request->nama : 'Semua Guru & Tendik'
        ];  

        return Excel::download(
            new LaporanExport($laporan, 'kepala-sekolah', $filterInfo),
            now()->format('Y-m-d') . "-rekap-presensi-kepala-sekolah.xlsx"
        );
    }

    /**
     * Export PDF Rekapitulasi Sekolah
     */
    public function exportPdf(Request $request)
    {
        $laporan = $this->getLaporanQuery($request)
            ->orderBy('tanggal', 'desc')
            ->get();

        $bulanParam = $request->has('bulan') ? $request->bulan : date('n');
        $namaBulan = !empty($bulanParam)
            ? Carbon::create()->month((int)$bulanParam)->translatedFormat('F')
            : 'Semua Bulan';

        $tahun = $request->has('tahun') ? $request->tahun : date('Y');
        $selectedTahun = !empty($tahun) ? $tahun : 'Semua Tahun';
        
        $guru = $request->filled('nama') ? $request->nama : 'Semua Guru & Tendik';

        $statusModel = $request->filled('status') ? StatusKehadiran::find($request->status) : null;
        $status = $statusModel ? $statusModel->status_kehadiran_label : 'Semua Status';

        // Grouping ringkasan data
        $rekapGroup = $laporan->groupBy(function ($item) {
            return $item->statusKehadiran->status_kehadiran_label ?? 'Lainnya';
        })->map(fn($item) => $item->count());

        $rekap = collect([
            'Total Data' => $laporan->count(),
            'Hadir'      => $rekapGroup['Hadir'] ?? 0,
            'Terlambat'  => $rekapGroup['Terlambat'] ?? 0,
            'Sakit'      => $rekapGroup['Sakit'] ?? 0,
            'Izin'       => $rekapGroup['Izin'] ?? 0,
            'Cuti'       => $rekapGroup['Cuti'] ?? 0,
            'Tugas'      => $rekapGroup['Tugas'] ?? 0,
            'Alfa'       => $rekapGroup['Alpa'] ?? $rekapGroup['Alfa'] ?? 0,
        ]);

        $kepalaSekolah = auth()->user();

        $pdf = Pdf::loadView('exports.laporan-pdf-kepsek', compact(
            'laporan',
            'namaBulan',
            'selectedTahun',
            'status',
            'guru',
            'rekap',
            'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        return $pdf->download(
            now()->format('Y-m-d') . "-laporan-presensi-kepala-sekolah.pdf"
        );
    }
}