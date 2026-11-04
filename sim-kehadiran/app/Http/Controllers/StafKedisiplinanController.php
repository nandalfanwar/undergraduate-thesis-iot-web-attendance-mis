<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\DataKehadiran;
use App\Models\StatusKehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


class StafKedisiplinanController extends Controller
{
    // public function dashboard()
    // {
    //     $hariIni = Carbon::today();

    //     // Total guru/tendik
    //     $totalGuru = User::where('role_id', 1)->count();

    //     // Statistik hari ini
    //     $hadir = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 1)
    //         ->count();

    //     $alpa = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 2)
    //         ->count();

    //     $sakit = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 3)
    //         ->count();

    //     $izin = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 4)
    //         ->count();

    //     $terlambat = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 7)
    //         ->count();

    //     $riwayat = DataKehadiran::with(['user','statusKehadiran'])
    //         ->latest('tanggal')
    //         ->take(10)
    //         ->get();       

    //     return view('staf-kedisiplinan.dashboard', compact(
    //         'totalGuru',
    //         'hadir',
    //         'alpa',
    //         'sakit',
    //         'izin',
    //         'terlambat',
    //         'riwayat'
    //     ));
    // }

    public function dashboard()
    {
        $hariIni = Carbon::today();
        $waktuSekarang = Carbon::now()->format('H:i:s');
        $batasJamMasuk = '07:30:00'; // Sesuaikan dengan batas jam masuk sekolahmu

        // 1. Total keseluruhan Guru & Tendik (Role 1 dan 2)
        $totalGuru = User::whereIn('role_id', [1, 2])->count();

        // 2. Hitung statistik riwayat yang sudah masuk ke database hari ini
        $hadir = DataKehadiran::whereDate('tanggal', $hariIni)
            ->where('status_kehadiran_id', 1)->count(); // 1: Hadir

        $sakit = DataKehadiran::whereDate('tanggal', $hariIni)
            ->where('status_kehadiran_id', 3)->count(); // 3: Sakit

        $izin = DataKehadiran::whereDate('tanggal', $hariIni)
            ->where('status_kehadiran_id', 4)->count(); // 4: Izin

        $cuti = DataKehadiran::whereDate('tanggal', $hariIni)
            ->where('status_kehadiran_id', 5)->count(); // 5: Cuti

        $tugas = DataKehadiran::whereDate('tanggal', $hariIni)
            ->where('status_kehadiran_id', 6)->count(); // 6: Tugas

        $terlambat = DataKehadiran::whereDate('tanggal', $hariIni)
            ->where('status_kehadiran_id', 7)->count(); // 7: Terlambat

        $manualAlpa = DataKehadiran::whereDate('tanggal', $hariIni)
            ->where('status_kehadiran_id', 2)->count(); // 2: Alpa (yang diinput manual)

        // 3. Hitung Alpa secara Otomatis (Virtual Alpa)
        // Total wajib hadir dikurangi semua orang yang sudah tercatat datanya di database hari ini
        $totalMasukSistem = DataKehadiran::whereDate('tanggal', $hariIni)->count();
        
        $alpaOtomatis = 0;
        if ($waktuSekarang > $batasJamMasuk) {
            $alpaOtomatis = max(0, $totalGuru - $totalMasukSistem);
        }
        
        // Total Alpa gabungan (Alpa otomatis sistem + input manual alpa)
        $alpa = $alpaOtomatis + $manualAlpa;

        // 4. Riwayat aktivitas terakhir
        $riwayat = DataKehadiran::with(['user','statusKehadiran'])
            ->latest('updated_at')
            ->take(10)
            ->get();      

        return view('staf-kedisiplinan.dashboard', compact(
            'totalGuru',
            'hadir',
            'alpa',
            'sakit',
            'izin',
            'cuti',
            'tugas',
            'terlambat',
            'riwayat'
        ));
    }

    // public function refresh()
    // {
    //     $hariIni = Carbon::today();

    //     $totalGuru = User::where('role_id', 1)->count();

    //     $hadir = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 1)
    //         ->count();

    //     $alpa = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 2)
    //         ->count();

    //     $sakit = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 3)
    //         ->count();

    //     $izin = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 4)
    //         ->count();

    //     $cuti = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 5)
    //         ->count();

    //     $tugas = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 6)
    //         ->count();

    //     $terlambat = DataKehadiran::whereDate('tanggal', $hariIni)
    //         ->where('status_kehadiran_id', 7)
    //         ->count();

    //     $riwayat = DataKehadiran::with(['user','statusKehadiran'])
    //         ->latest('updated_at')
    //         ->take(10)
    //         ->get();

    //     return response()->json([
    //         'totalGuru' => $totalGuru,
    //         'hadir' => $hadir,
    //         'alpa' => $alpa,
    //         'sakit' => $sakit,
    //         'izin' => $izin,
    //         'terlambat' => $terlambat,

    //         'last_update' => optional($riwayat->first())->updated_at,

    //         'riwayat' => $riwayat->map(function($item){

    //             $status = $item->statusKehadiran->status_kehadiran_label;

    //             return [

    //                 'nama' => $item->user->nama,

    //                 'tanggal' => Carbon::parse($item->tanggal)
    //                     ->translatedFormat('d F Y'),

    //                 'masuk' => $item->waktu_masuk
    //                     ? Carbon::parse($item->waktu_masuk)->format('H:i')
    //                     : '-',

    //                 'keluar' => $item->waktu_keluar
    //                     ? Carbon::parse($item->waktu_keluar)->format('H:i')
    //                     : '-',

    //                 'status' => $status

    //             ];

    //         })

    //     ]);
    // }

    // Lakukan penyesuaian yang SAMA PERSIS untuk method refresh() karena pakai AJAX
    public function refresh()
    {
        $hariIni = Carbon::today();
        $waktuSekarang = Carbon::now()->format('H:i:s');
        $batasJamMasuk = '07:30:00';

        $totalGuru = User::whereIn('role_id', [1, 2])->count();

        $hadir = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 1)->count();
        $sakit = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 3)->count();
        $izin = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 4)->count();
        $cuti = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 5)->count();
        $tugas = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 6)->count();
        $terlambat = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 7)->count();
        $manualAlpa = DataKehadiran::whereDate('tanggal', $hariIni)->where('status_kehadiran_id', 2)->count();

        $totalMasukSistem = DataKehadiran::whereDate('tanggal', $hariIni)->count();
        $alpaOtomatis = 0;
        if ($waktuSekarang > $batasJamMasuk) {
            $alpaOtomatis = max(0, $totalGuru - $totalMasukSistem);
        }
        $alpa = $alpaOtomatis + $manualAlpa;

        $riwayat = DataKehadiran::with(['user','statusKehadiran'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return response()->json([
            'totalGuru' => $totalGuru,
            'hadir' => $hadir,
            'alpa' => $alpa,
            'sakit' => $sakit,
            'izin' => $izin,
            'terlambat' => $terlambat,
            'last_update' => optional($riwayat->first())->updated_at,
            'riwayat' => $riwayat->map(function($item){
                $status = optional($item->statusKehadiran)->status_kehadiran_label ?? '-';
                return [
                    'nama' => optional($item->user)->nama ?? '-',
                    'tanggal' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                    'masuk' => $item->waktu_masuk ? Carbon::parse($item->waktu_masuk)->format('H:i') : '-',
                    'keluar' => $item->waktu_keluar ? Carbon::parse($item->waktu_keluar)->format('H:i') : '-',
                    'status' => $status
                ];
            })
        ]);
    }


    // public function manajemenKehadiran()
    // {
    //     // debugging untuk melihat data kehadiran yang tidak memiliki user terkait
    //     // dd(
    //     //     DataKehadiran::with('user')
    //     //         ->whereDoesntHave('user')
    //     //         ->get()
    //     // );

    //     $kehadiran = DataKehadiran::with([
    //         'user',
    //         'statusKehadiran'
    //     ]);

    //     if(request('nama')){
    //         $kehadiran->whereHas('user', function($q){
    //             $q->where(
    //                 'nama',
    //                 'like',
    //                 '%'.request('nama').'%'
    //             );
    //         });

    //     }
    //     if(request('status')){

    //         $kehadiran->where(
    //             'status_kehadiran_id',
    //             request('status')
    //         );
    //     }
    //     if(request('tanggal')){

    //         $kehadiran->whereDate(
    //             'tanggal',
    //             request('tanggal')
    //         );
    //     }

    //     $kehadiran = $kehadiran
    //         ->orderBy('tanggal','desc')
    //         ->paginate(10)
    //         ->withQueryString();

    //     $guru = User::where('role_id',1)
    //         ->orderBy('nama')
    //         ->get();

    //      // Dropdown Status
    //     $statusList = StatusKehadiran::all();

        

    //     return view(
    //         'staf-kedisiplinan.manajemen-kehadiran',
    //         compact(
    //             'kehadiran',
    //             'guru',
    //             'statusList'
    //         )
    //     );
    // }

    public function manajemenKehadiran(Request $request)
    {
        // 1. Ambil parameter tanggal dari filter, default ke hari ini
        $tanggalFilter = $request->tanggal ?? Carbon::today()->format('Y-m-d');
        
        // 2. Ambil semua User dengan role Guru (1) atau Tendik (sesuaikan jika ada role lain)
        // Sekaligus ambil data kehadirannya khusus pada tanggal yang dipilih
        $users = User::whereIn('role_id', [1, 2]) // 1: Guru, 2: Tendik (sesuaikan ID rolenya di database-mu)
            ->with(['dataKehadiran' => function($query) use ($tanggalFilter) {
                $query->whereDate('tanggal', $tanggalFilter);
            }])
            ->when($request->nama, function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            })
            ->orderBy('nama')
            ->get();

        // 3. Logika Dinamis "View" untuk Dosen
        $batasJamMasuk = '07:30:00'; // Batas jam masuk sekolah
        $waktuSekarang = Carbon::now()->format('H:i:s');
        $isHariIni = ($tanggalFilter == Carbon::today()->format('Y-m-d'));

        // Mapping data agar semua guru/tendik pasti muncul di tabel
        $kehadiranTampil = $users->map(function ($user) use ($batasJamMasuk, $waktuSekarang, $isHariIni, $tanggalFilter) {
            
            $dataKehadiran = $user->dataKehadiran->first(); 
            
            // Default nilai jika belum ada data sama sekali di database
            $statusDinamis = 'Belum Hadir'; 
            $waktuMasuk = '-';
            $waktuKeluar = '-';
            $keterangan = '-';
            $idKehadiran = null;
            $bukti = null;
            $tglUploadBukti = null;

            if ($dataKehadiran) {
                // SKENARIO A: Sudah ada data (baik dari tap RFID maupun input manual staf)
                $statusDinamis = optional($dataKehadiran->statusKehadiran)->status_kehadiran_label ?? 'Hadir';
                $waktuMasuk = $dataKehadiran->waktu_masuk ? Carbon::parse($dataKehadiran->waktu_masuk)->format('H:i') : '-';
                $waktuKeluar = $dataKehadiran->waktu_keluar ? Carbon::parse($dataKehadiran->waktu_keluar)->format('H:i') : '-';
                $keterangan = $dataKehadiran->keterangan ?? '-';
                $idKehadiran = $dataKehadiran->id;
                $bukti = $dataKehadiran->bukti;
                $tglUploadBukti = $dataKehadiran->tgl_upload_bukti;
            } else {
                // SKENARIO B: Kosong (Belum tap / Belum diinput)
                if ($isHariIni && $waktuSekarang > $batasJamMasuk) {
                    // Jika hari ini dan sudah lewat jam masuk, otomatis tampilkan "Alpa" di layar
                    $statusDinamis = 'Alpa';
                } elseif (!$isHariIni) {
                    // Jika melihat tanggal lampau dan kosong, anggap Alpa
                    $statusDinamis = 'Alpa';
                }
            }

            // Return sebagai objek agar mudah dibaca di file Blade view
            return (object) [
                'id' => $idKehadiran, // ID tabel data_kehadiran (null jika virtual alpa)
                'user_id' => $user->id,
                'nama' => $user->nama,
                'nuptk' => $user->nuptk ?? '-',
                'tanggal' => $tanggalFilter,
                'waktu_masuk' => $waktuMasuk,
                'waktu_keluar' => $waktuKeluar,
                'status' => $statusDinamis,
                'status_kehadiran_id' => $dataKehadiran->status_kehadiran_id ?? null,
                'keterangan' => $keterangan,
                'bukti' => $bukti,
                'tgl_upload_bukti' => $tglUploadBukti,
                'is_virtual' => !$dataKehadiran // Penanda apakah data ini murni virtual (belum ada di DB)
            ];
        });

        // 4. Filter berdasarkan status jika admin memilih filter status di dropdown
        if ($request->filled('status')) {
            $statusObj = StatusKehadiran::find($request->status);
            if ($statusObj) {
                $kehadiranTampil = $kehadiranTampil->where('status', $statusObj->status_kehadiran_label);
            }
        }

        // Karena ini Collection (bukan Eloquent Paginator asli), kita buat manual pagination sederhana atau tampilkan semua
        // Untuk amannya, kita kirimkan data ke view
        $statusList = StatusKehadiran::all();
        $guru = User::whereIn('role_id', [1, 2])->orderBy('nama')->get();

        // Agar pagination bawaan Blade `{{ $kehadiran->links() }}` tidak error, 
        // kita bisa ubah Collection menjadi LengthAwarePaginator jika diperlukan, 
        // atau gunakan paginator manual. (Lihat catatan di bawah jika pakai paginator)
        
        return view(
            'staf-kedisiplinan.manajemen-kehadiran',
            compact(
                'kehadiranTampil',
                'guru',
                'statusList',
                'tanggalFilter'
            )
        );
    }

    // store data kehadiran
    public function store(Request $request)
    {
        $request->validate([
            'user_id'             => 'required|exists:users,id',
            'tanggal'             => 'required|date',
            'status_kehadiran_id' => 'required|exists:status_kehadiran,id',
            'waktu_masuk'         => 'nullable',
            'waktu_keluar'        => 'nullable',
            'keterangan'          => 'nullable|string',
            'bukti'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $dataToCreate = [
            'user_id'             => $request->user_id,
            'tanggal'             => $request->tanggal,
            'status_kehadiran_id' => $request->status_kehadiran_id,
            'waktu_masuk'         => $request->waktu_masuk,
            'waktu_keluar'        => $request->waktu_keluar,
            'keterangan'          => $request->keterangan,
        ];

        // Logika untuk menyimpan file bukti jika di-upload lewat modal tambah
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $path = $file->store('bukti-kehadiran', 'public');

            $dataToCreate['bukti']            = $path;
            $dataToCreate['tgl_upload_bukti'] = now();
        }

        DataKehadiran::create($dataToCreate);

        return back()->with('success', 'Data kehadiran berhasil ditambahkan.');
    }

    //delete data kehadiran
    public function destroy($id)
    {
        DataKehadiran::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Data berhasil dihapus.'
        );
    }

    // update data kehadiran
    public function update(Request $request, $id)
    {
        $kehadiran = DataKehadiran::findOrFail($id);

        // 1. UBAH VALIDASI USER_ID: Hapus 'required' atau ubah jadi 'nullable'
        $request->validate([
            'user_id'             => 'nullable',
            'tanggal'             => 'required|date',
            'status_kehadiran_id' => 'required',
            'bukti'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' 
        ]);
    
        // 2. Data dasar yang di-update
        $dataToUpdate = [
            'tanggal'             => $request->tanggal,
            'status_kehadiran_id' => $request->status_kehadiran_id,
            'waktu_masuk'         => $request->waktu_masuk,
            'waktu_keluar'        => $request->waktu_keluar,
            'keterangan'          => $request->keterangan,
        ];

        // Sertakan user_id hanya jika ada dikirim dari form
        if ($request->filled('user_id')) {
            $dataToUpdate['user_id'] = $request->user_id;
        }

        // 3. Logika Upload Bukti (Proteksi Backend)
        if (empty($kehadiran->bukti) && $request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $path = $file->store('bukti-kehadiran', 'public');

            $dataToUpdate['bukti']            = $path;
            $dataToUpdate['tgl_upload_bukti'] = now(); 
        }

        // 4. Update Ke Database
        $kehadiran->update($dataToUpdate);

        return back()->with('success', 'Data kehadiran berhasil diperbarui.');
    }



    public function laporanRekapitulasi()
    {
        $query = DataKehadiran::with([
            'user',
            'statusKehadiran'
        ]);

        // Filter Nama
        if (request('nama')) {

            $query->whereHas('user', function ($q) {

                $q->where(
                    'nama',
                    'like',
                    '%' . request('nama') . '%'
                );

            });

        }

        // Filter Status
        if (request('status')) {

            $query->where(
                'status_kehadiran_id',
                request('status')
            );

        }

        // Filter Bulan
        if (request('bulan')) {

            $query->whereMonth(
                'tanggal',
                request('bulan')
            );

        }

        // Filter Tahun
        if (request('tahun')) {

            $query->whereYear(
                'tanggal',
                request('tahun')
            );

        }

        $laporan = $query
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Dropdown Guru
        $guru = User::where('role_id', 1)
            ->orderBy('nama')
            ->get();

        // Dropdown Status
        $statusList = StatusKehadiran::all();

        /*
        |--------------------------------------------------------------------------
        | Rekap
        |--------------------------------------------------------------------------
        */

        $rekap = DataKehadiran::query();

        if (request('nama')) {

            $rekap->whereHas('user', function ($q) {

                $q->where(
                    'nama',
                    'like',
                    '%' . request('nama') . '%'
                );

            });

        }

        if (request('status')) {

            $rekap->where(
                'status_kehadiran_id',
                request('status')
            );

        }

        if (request('bulan')) {

            $rekap->whereMonth(
                'tanggal',
                request('bulan')
            );

        }

        if (request('tahun')) {

            $rekap->whereYear(
                'tanggal',
                request('tahun')
            );

        }

        $hadir = (clone $rekap)->where('status_kehadiran_id', 1)->count();
        $alpa  = (clone $rekap)->where('status_kehadiran_id', 2)->count();
        $sakit = (clone $rekap)->where('status_kehadiran_id', 3)->count();
        $izin  = (clone $rekap)->where('status_kehadiran_id', 4)->count();
        $cuti  = (clone $rekap)->where('status_kehadiran_id', 5)->count();
        $tugas = (clone $rekap)->where('status_kehadiran_id', 6)->count();
        $terlambat = (clone $rekap)->where('status_kehadiran_id', 7)->count();

        return view(
            'staf-kedisiplinan.laporan-rekap',
            compact(
                'laporan',
                'guru',
                'statusList',
                'hadir',
                'alpa',
                'sakit',
                'izin',
                'cuti',
                'tugas',
                'terlambat'
            )
        );
    }

    public function exportExcel(Request $request)
    {
        $laporan = $this->getLaporanQuery($request)->orderBy('tanggal', 'desc')->get();

        $bulanParam = $request->has('bulan') ? $request->bulan : date('n');
        $namaBulan = !empty($bulanParam) ? Carbon::create()->month((int)$bulanParam)->translatedFormat('F') : 'Semua Bulan';
        $tahun = $request->has('tahun') ? $request->tahun : date('Y');

        $filterInfo = [
            'bulan' => $namaBulan,
            'tahun' => !empty($tahun) ? $tahun : 'Semua Tahun',
            'guru'  => $request->filled('nama') ? $request->nama : 'Semua Guru & Tendik'
        ];

        return Excel::download(
            new LaporanExport($laporan, 'staf-kedisiplinan', $filterInfo),
            now()->format('Y-m-d') . "-laporan-presensi-staf.xlsx"
        );
    }

    private function getLaporanQuery()
    {
        $query = DataKehadiran::with([
            'user',
            'statusKehadiran'
        ]);

        if (request('nama')) {

            $query->whereHas('user', function ($q) {

                $q->where(
                    'nama',
                    'like',
                    '%' . request('nama') . '%'
                );

            });

        }

        if (request('status')) {

            $query->where(
                'status_kehadiran_id',
                request('status')
            );

        }

        if (request('bulan')) {

            $query->whereMonth(
                'tanggal',
                request('bulan')
            );

        }

        if (request('tahun')) {

            $query->whereYear(
                'tanggal',
                request('tahun')
            );

        }

        return $query;
    }

    public function exportPdf()
    {
        $laporan = $this->getLaporanQuery()
            ->orderBy('tanggal')
            ->get();

        // ==========================
        // Periode
        // ==========================

        $bulan = request('bulan')
            ? Carbon::create()->month(request('bulan'))->translatedFormat('F')
            : 'Semua Bulan';

        $tahun = request('tahun') ?: '';

        // ==========================
        // Status Kehadiran
        // ==========================

        $status = 'Semua Status';

        if (request('status')) {

            $statusData = StatusKehadiran::find(request('status'));

            if ($statusData) {
                $status = $statusData->status_kehadiran_label;
            }
        }

        // ==========================
        // Guru / Tendik
        // ==========================

        $guru = 'Semua Guru dan Tendik';

        if (request('nama')) {

            $guru = request('nama');
        }

        // rekap data kehadiran
        $rekap = $laporan
            ->groupBy(function ($item) {
                return $item->statusKehadiran->status_kehadiran_label;
            })
            ->map(function ($item) {
                return $item->count();
            });


        $rekap = collect([
            'Total Data' => $laporan->count(),
            'Hadir' => $rekap['Hadir'] ?? 0,
            'Terlambat' => $rekap['Terlambat'] ?? 0,
            'Sakit' => $rekap['Sakit'] ?? 0,
            'Izin' => $rekap['Izin'] ?? 0,
            'Cuti' => $rekap['Cuti'] ?? 0,
            'Tugas' => $rekap['Tugas'] ?? 0,
            'Alfa' => $rekap['Alfa'] ?? 0,
        ]);

        // fetch kepala sekolah
        $kepalaSekolah = User::where('role_id', 3)->first();

        $pdf = Pdf::loadView(
            'exports.laporan-pdf',
            compact(
                'laporan',
                'bulan',
                'tahun',
                'status',
                'guru',
                'rekap',
                'kepalaSekolah'
            )
        )->setPaper('a4', 'landscape');

        // return $pdf->download('laporan-kehadiran.pdf');

        return $pdf->download(
            now()->format('Y-m-d_H-i') . '-laporan-kehadiran.pdf'
        );
    }

    // manajemen akun 
    public function manajemenAkun()
    {
        $users = User::with('role')
            ->orderBy('nama')
            ->paginate(10);

        $roles = Role::orderBy('id')->get();

        return view(
            'staf-kedisiplinan.manajemen-akun',
            compact(
                'users',
                'roles'
            )
        );
    }
    
    // store akun
    public function storeAkun(Request $request)
    {
        // debug fetch the log of whats stored sampai darderdor
        // dd('MASUK STORE');

        $request->validate([
            'nuptk' => 'required',

            'nama' => 'required|max:255',

            'email' => 'required|email|unique:users,email',

            'username' => 'required|unique:users,username',

            'password' => 'required|string|min:8',

            'role_id' => 'required',

            'foto' => 'nullable|image|max:2048',

        ]);

        // dd('LOLOS VALIDASI');

        $foto = null;

        if ($request->hasFile('foto')) {

            $foto = $request->file('foto')
                ->store('foto-profile', 'public');

        }
        
        // dd($foto);

        User::create([

            'nuptk' => $request->nuptk,

            'role_id' => $request->role_id,

            'uid_rfid' => null,

            'nama' => $request->nama,

            'email' => $request->email,

            'username' => $request->username,

            'password' => Hash::make($request->password),

            'foto' => $foto,

            

        ]);

        // dd(user::all());

        return back()->with(
            'success',
            'Akun berhasil ditambahkan.'
        );
    }

    // update akun
    public function updateAkun(Request $request, User $user)
    {
        $request->validate([

            'nama' => 'required|max:255',

            'email' => 'required|email|unique:users,email,' . $user->id,

            'username' => 'required|unique:users,username,' . $user->id,

            'role_id' => 'required',

            'password' => 'nullable|string|min:8',

            'foto' => 'nullable|image|max:2048',

        ]);

        //update data utama pengguna
        $user->update([

            'nama' => $request->nama,

            'email' => $request->email,

            'username' => $request->username,

            'role_id' => $request->role_id

        ]);

        //update password jika ada
        if($request->password){

            $user->update([

                'password' => Hash::make($request->password)

            ]);

        }

        // Update foto jika ada
        if ($request->hasFile('foto')) {

            // Hapus foto lama
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {

                Storage::disk('public')->delete($user->foto);

            }

            // Simpan foto baru
            $user->foto = $request->file('foto')
                ->store('foto-profile', 'public');

        }

        $user->save();

        return back()->with(
            'success',
            'Akun berhasil diperbarui.'
        );
    }

    // delete akun
    public function destroyAkun(User $user)
    {
        // Tidak boleh menghapus akun sendiri
        if ($user->id == auth()->id()) {

            return back()->with(
                'error',
                'Anda tidak dapat menghapus akun yang sedang digunakan.'
            );

        }

        // Tidak boleh menghapus akun yang sudah memiliki riwayat kehadiran
        if ($user->dataKehadiran()->exists()) {

            return back()->with(
                'error',
                'Akun tidak dapat dihapus karena sudah memiliki riwayat kehadiran.'
            );

        }

        $user->delete();

        return back()->with(
            'success',
            'Akun berhasil dihapus.'
        );
    }

    //daftar rfid
    public function daftarkanRFID(User $user)
    {
        Storage::put(
            'rfid_target.txt',
            $user->id
        );

        return back()->with(
            'success',
            'Silakan tempel kartu RFID.'
        );
    }

}
