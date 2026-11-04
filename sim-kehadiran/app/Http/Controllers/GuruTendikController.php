<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\DataKehadiran;
use App\Models\StatusKehadiran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GuruTendikController extends Controller
{
    public function dashboard()
    {
        $riwayat = DataKehadiran::with('statusKehadiran')
            ->where('user_id', auth()->id())
            ->whereBetween('tanggal', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->orderBy('tanggal', 'desc')
            ->get();

        $today = DataKehadiran::where('user_id', auth()->id())
            ->whereDate('tanggal', now())
            ->first();

        return view('guru-tendik.dashboard', compact('today', 'riwayat'));
    }

    /**
     * Private Helper Query
     * Mengunci data HANYA untuk user yang sedang login
     */
    private function getLaporanSayaQuery(Request $request)
    {
        $selectedBulan = $request->input('bulan', date('n'));
        $selectedTahun = $request->input('tahun', date('Y'));

        return DataKehadiran::with('statusKehadiran')
            ->where('user_id', auth()->id())
            ->when($selectedBulan, function ($q) use ($selectedBulan) {
                $q->whereMonth('tanggal', $selectedBulan);
            })
            ->when($selectedTahun, function ($q) use ($selectedTahun) {
                $q->whereYear('tanggal', $selectedTahun);
            });
    }

    /**
     * Halaman Rekapitulasi Presensi Saya
     */
    public function laporanRekapitulasi(Request $request)
    {
        $query = $this->getLaporanSayaQuery($request);

        // Data Tabel dengan Paginasi
        $laporan = (clone $query)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Count ringkasan untuk grafik & statistik
        $hadir     = (clone $query)->where('status_kehadiran_id', 1)->count();
        $alpa      = (clone $query)->where('status_kehadiran_id', 2)->count();
        $sakit     = (clone $query)->where('status_kehadiran_id', 3)->count();
        $izin      = (clone $query)->where('status_kehadiran_id', 4)->count();
        $cuti      = (clone $query)->where('status_kehadiran_id', 5)->count();
        $tugas     = (clone $query)->where('status_kehadiran_id', 6)->count();
        $terlambat = (clone $query)->where('status_kehadiran_id', 7)->count();

        return view('guru-tendik.laporan-rekap', compact(
            'laporan',
            'hadir',
            'alpa',
            'sakit',
            'izin',
            'cuti',
            'tugas',
            'terlambat'
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        
        $laporan = DataKehadiran::where('user_id', $user->id)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan))
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tanggal', $request->tahun))
            ->orderBy('tanggal', 'desc')
            ->get();

        $bulanParam = $request->has('bulan') ? $request->bulan : date('n');
        $namaBulan = !empty($bulanParam) ? Carbon::create()->month((int)$bulanParam)->translatedFormat('F') : 'Semua Bulan';
        $tahun = $request->has('tahun') ? $request->tahun : date('Y');

        $filterInfo = [
            'bulan' => $namaBulan,
            'tahun' => !empty($tahun) ? $tahun : 'Semua Tahun',
            'guru'  => $user->nama
        ];

        return Excel::download(
            new LaporanExport($laporan, 'guru', $filterInfo),
            now()->format('Y-m-d') . "-laporan-presensi-saya.xlsx"
        );
    }

    /**
     * Export PDF Presensi Saya
     */
    public function exportPdf(Request $request)
    {
        $laporan = $this->getLaporanSayaQuery($request)
            ->orderBy('tanggal', 'desc')
            ->get();

        $bulanParam = $request->input('bulan', date('n'));
        $bulan = $bulanParam
            ? Carbon::create()->month((int)$bulanParam)->translatedFormat('F')
            : 'Semua Bulan';

        $tahun = $request->input('tahun', date('Y'));
        $status = 'Semua Status';
        $guru = auth()->user()->nama ?? auth()->user()->name;

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

        $kepalaSekolah = User::where('role_id', 3)->first();

        $pdf = Pdf::loadView('exports.laporan-pdf-gurutendik', compact(
            'laporan',
            'bulan',
            'tahun',
            'status',
            'guru',
            'rekap',
            'kepalaSekolah'
        ))->setPaper('a4', 'portrait');

        $namaClean = str_replace(' ', '_', strtolower($guru));

        return $pdf->download(
            now()->format('Y-m-d') . "-presensi-{$namaClean}.pdf"
        );
    }
}